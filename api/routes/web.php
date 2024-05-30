<?php

use App\Http\Controllers\AdhesionController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('request-adhesion', [AdhesionController::class, 'requestAdhesion'])->middleware('guest');
Route::post('submit-password', [AdhesionController::class, 'submitPassword'])->middleware('guest');

// auth
Route::post('login', [AuthController::class, 'login'])->middleware('guest')->defaults('for', 'member')->name('login');
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:member')->defaults('for', 'member');
Route::post('forgot-password', [AuthController::class, 'forgotPassword'])->middleware('guest');

// profile funcs
Route::middleware('auth:member')->prefix('profile')->group(function () {
    Route::patch('{member}/infos', [ProfileController::class, 'patchInfos']);
    // boat
    Route::post('{member}/boat', [ProfileController::class, 'addBoat']);
    Route::patch('{member}/boat', [ProfileController::class, 'patchBoat']);
    Route::delete('{member}/boat', [ProfileController::class, 'deleteBoat']);
    // coowner
    Route::post('{member}/coowner', [ProfileController::class, 'addCoowner']);
    Route::patch('{member}/coowner', [ProfileController::class, 'patchCoowner']);
    Route::delete('{member}/coowner', [ProfileController::class, 'deleteCoowner']);

    Route::post('{member}/email', [ProfileController::class, 'updateEmail']);
    Route::post('{member}/password', [ProfileController::class, 'updatePassword']);
});

Route::get('profil', fn () => 'member authed')->middleware('auth:member')->name('profil');
