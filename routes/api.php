<?php

use App\Http\Controllers\Api\EnquiryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('enquiries', EnquiryController::class)->except(['update']);
    Route::patch('enquiries/{id}', [EnquiryController::class, 'update']);
});