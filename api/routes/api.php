<?php

use App\Http\Controllers\Api\MemberController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::apiResource('member', MemberController::class);
// Route::delete('member/{member}/delete-coowner', [MemberController::class, 'deleteCoowner']);