<?php

use Illuminate\Http\Request;
use App\Pokemon\Infrastructure\Http\Controller\PokemonController;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('pokemon', PokemonController::class)->only(['index', 'show', 'store']); 