<?php

namespace App\Http\Controllers;

use App\Services\SignalService;

class SignalController extends Controller
{
    public function __construct(
        private readonly SignalService $signalService,
    ) {}

    public function __invoke()
    {
        $signals = $this->signalService->getAllSignals();

        return view('signals.index', $signals);
    }
}
