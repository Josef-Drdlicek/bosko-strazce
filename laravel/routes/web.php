<?php

use App\Http\Controllers\AresController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\EntityController;
use App\Http\Controllers\GraphController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SignalController;
use App\Http\Controllers\SubsidyController;
use Illuminate\Support\Facades\Route;

Route::get('/', DashboardController::class)->name('dashboard');

Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');
Route::get('/documents/{document}', [DocumentController::class, 'show'])->name('documents.show');

Route::get('/contracts', [ContractController::class, 'index'])->name('contracts.index');
Route::get('/contracts/{contract}', [ContractController::class, 'show'])->name('contracts.show');

Route::get('/subsidies', [SubsidyController::class, 'index'])->name('subsidies.index');
Route::get('/subsidies/{subsidy}', [SubsidyController::class, 'show'])->name('subsidies.show');

Route::get('/entities', [EntityController::class, 'index'])->name('entities.index');
Route::get('/entities/{entity}', [EntityController::class, 'show'])->name('entities.show');

Route::get('/signals', SignalController::class)->name('signals.index');

Route::get('/graph/{entity}', [GraphController::class, 'show'])->name('graph.show');

Route::get('/ares', [AresController::class, 'index'])->name('ares.index');
Route::get('/ares/search', [AresController::class, 'search'])->name('ares.search');

Route::get('/search', SearchController::class)->name('search');
