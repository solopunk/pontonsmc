<?php

use App\Http\Controllers\AdhesionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('do')->group(function () {
    Route::post('request-adhesion', [AdhesionController::class, 'requestAdhesion']);
});
