<?php

use App\Http\Controllers\AdhesionController;
use App\Http\Controllers\Api\MailController;
use App\Http\Controllers\Api\MemberController;
use App\Http\Controllers\Api\ScoopController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::get('accept-adhesion/{requestor}', [AdhesionController::class, 'acceptAdhesion']);
Route::get('decline-adhesion/{requestor}', [AdhesionController::class, 'declineAdhesion']);
Route::apiResource('member', MemberController::class);

Route::get('scoop/{scoop}/toggle-visibility', [ScoopController::class, 'toggleVisibility']);
Route::delete('scoop/{scoop}/delete-attachment/{attachment}', [ScoopController::class, 'deleteAttachment']);
Route::apiResource('scoop', ScoopController::class);

Route::get('mail/{mail}/send', [MailController::class, 'send']);
Route::delete('mail/{mail}/delete-attachment/{attachment}', [MailController::class, 'deleteAttachment']);
Route::apiResource('mail', MailController::class);
