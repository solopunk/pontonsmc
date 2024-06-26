<?php

use App\Http\Controllers\AdhesionController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\HomepageController;
use App\Http\Controllers\Api\MailController;
use App\Http\Controllers\Api\MemberController;
use App\Http\Controllers\Api\ScoopController;
use App\Http\Controllers\Api\TabController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// auth
Route::post('login', [AuthController::class, 'login'])->middleware('guest')->defaults('for', 'admin');
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum')->defaults('for', 'admin');


Route::middleware('auth:sanctum')->group(function () {
    // member
    Route::get('accept-adhesion/{requestor}', [AdhesionController::class, 'acceptAdhesion']);
    Route::get('decline-adhesion/{requestor}', [AdhesionController::class, 'declineAdhesion']);
    Route::get('member/{member}/welcome', [MemberController::class, 'welcome']);
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
});
