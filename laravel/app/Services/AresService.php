<?php

namespace App\Services;

use App\Models\Entity;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class AresService
{
    private const BASE_URL = 'https://ares.gov.cz/ekonomicke-subjekty-v-be/rest/ekonomicke-subjekty';
    private const CACHE_TTL_SECONDS = 86400;
    private const TIMEOUT_SECONDS = 15;

    public function findByIco(string $ico): ?array
    {
        return Cache::remember("ares:ico:{$ico}", self::CACHE_TTL_SECONDS, function () use ($ico) {
            $response = Http::timeout(self::TIMEOUT_SECONDS)
                ->acceptJson()
                ->get(self::BASE_URL . "/{$ico}");

            if ($response->failed()) {
                return null;
            }

            return $this->parseSubject($response->json());
        });
    }

    public function search(string $query, int $limit = 25): array
    {
        $response = Http::timeout(self::TIMEOUT_SECONDS)
            ->acceptJson()
            ->post(self::BASE_URL . '/vyhledat', [
                'obchodniJmeno' => $query,
                'pocet' => $limit,
                'start' => 0,
            ]);

        if ($response->failed()) {
            return [];
        }

        $subjects = $response->json('ekonomickeSubjekty', []);

        return array_filter(array_map(
            fn (array $item) => $this->parseSubject($item),
            $subjects,
        ));
    }

    public function enrichEntity(Entity $entity): void
    {
        if (! $entity->ico) {
            return;
        }

        $data = $this->findByIco($entity->ico);

        if ($data === null) {
            return;
        }

        $entity->update([
            'name' => $data['name'] ?? $entity->name,
            'metadata_json' => $data,
        ]);
    }

    private function parseSubject(array $data): array
    {
        $address = $data['sidlo'] ?? [];

        return [
            'ico' => $data['ico'] ?? null,
            'name' => $data['obchodniJmeno'] ?? $data['nazev'] ?? null,
            'legal_form' => $data['pravniForma'] ?? null,
            'date_created' => $data['datumVzniku'] ?? null,
            'date_terminated' => $data['datumZaniku'] ?? null,
            'address' => $this->formatAddress($address),
            'address_raw' => $address,
            'cz_nace' => $data['czNace'] ?? [],
            'financial_office' => $data['financniUrad'] ?? null,
        ];
    }

    private function formatAddress(array $address): string
    {
        $parts = [];
        $street = $address['nazevUlice'] ?? '';
        $houseNumber = $address['cisloDomovni'] ?? '';
        $orientationNumber = $address['cisloOrientacni'] ?? '';

        if ($street) {
            $number = (string) $houseNumber;
            if ($orientationNumber) {
                $number .= "/{$orientationNumber}";
            }
            $parts[] = trim("{$street} {$number}");
        } elseif ($houseNumber) {
            $parts[] = "č.p. {$houseNumber}";
        }

        $city = $address['nazevObce'] ?? '';
        $postalCode = $address['psc'] ?? '';
        if ($city) {
            $parts[] = $postalCode ? trim("{$postalCode} {$city}") : $city;
        }

        return implode(', ', $parts);
    }
}
