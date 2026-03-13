<?php

use App\Http\Controllers\Api\EntityApiController;
use App\Http\Controllers\Api\StatsApiController;
use Illuminate\Support\Facades\Route;

Route::get('/stats', StatsApiController::class);

Route::get('/entities', [EntityApiController::class, 'index']);
Route::get('/entities/{entity}', [EntityApiController::class, 'show']);
Route::get('/entities/{entity}/relations', [EntityApiController::class, 'relations']);
