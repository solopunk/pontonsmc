<?php

use App\Http\Controllers\AdhesionController;
use App\Http\Controllers\Api\MailController;
use App\Http\Controllers\Api\MemberController;
use App\Http\Controllers\Api\ScoopController;
use App\Http\Controllers\Api\TabController;
use App\Http\Controllers\HomepageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// member
Route::get('accept-adhesion/{requestor}', [AdhesionController::class, 'acceptAdhesion']);
Route::get('decline-adhesion/{requestor}', [AdhesionController::class, 'declineAdhesion']);
Route::apiResource('member', MemberController::class);

// scoop
Route::get('scoop/{scoop}/toggle-visibility', [ScoopController::class, 'toggleVisibility']);
Route::delete('scoop/{scoop}/delete-attachment/{attachment}', [ScoopController::class, 'deleteAttachment']);
Route::apiResource('scoop', ScoopController::class);

// mail
Route::get('mail/{mail}/send', [MailController::class, 'send']);
Route::delete('mail/{mail}/delete-attachment/{attachment}', [MailController::class, 'deleteAttachment']);
Route::get('mail/{mail}/copy', [MailController::class, 'copy']);
Route::apiResource('mail', MailController::class);

// tab
Route::patch('tab/{tab}', [TabController::class, 'update']);

// homepage
Route::patch('homepage', [HomepageController::class, 'update']);
