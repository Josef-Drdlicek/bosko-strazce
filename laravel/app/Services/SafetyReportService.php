<?php

namespace App\Services;

use App\Models\SafetyReport;
use Illuminate\Support\Collection;

class SafetyReportService
{
    public function getAllReports(): Collection
    {
        return SafetyReport::recent()->get();
    }

    public function getReportsAsGeoJson(): array
    {
        $reports = $this->getAllReports();

        return [
            'type' => 'FeatureCollection',
            'features' => $reports->map(fn (SafetyReport $report) => [
                'type' => 'Feature',
                'geometry' => [
                    'type' => 'Point',
                    'coordinates' => [(float) $report->longitude, (float) $report->latitude],
                ],
                'properties' => [
                    'id' => $report->id,
                    'category' => $report->category,
                    'description' => $report->description,
                    'reporter_name' => $report->reporter_name,
                    'created_at' => $report->created_at->format('j. n. Y H:i'),
                ],
            ])->values()->all(),
        ];
    }

    public function createReport(array $data): SafetyReport
    {
        return SafetyReport::create($data);
    }

    public function getCategoryCounts(): Collection
    {
        return SafetyReport::selectRaw('category, count(*) as count')
            ->groupBy('category')
            ->pluck('count', 'category');
    }
}
