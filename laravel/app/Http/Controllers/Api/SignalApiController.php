<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SignalService;
use Illuminate\Http\JsonResponse;

class SignalApiController extends Controller
{
    public function __construct(
        private readonly SignalService $signalService,
    ) {}

    public function __invoke(): JsonResponse
    {
        return response()->json($this->signalService->getAllSignals());
    }
}
