<?php

use App\Http\Controllers\Api\AgentController;
use App\Http\Controllers\Api\CallController;
use App\Http\Controllers\Api\EnquiryController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [RegisteredUserController::class, 'store']);
Route::post('/login', [AuthenticatedSessionController::class, 'store']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', function (Request $request) {
        return response()->json([
            'user' => $request->user()
        ]);
    });

     // Enquiries CRUD
     Route::get('/enquiries', [EnquiryController::class, 'index']);
     Route::post('/enquiries', [EnquiryController::class, 'store']);
     Route::patch('/enquiries/{id}', [EnquiryController::class, 'update']);
     Route::delete('/enquiries/{id}', [EnquiryController::class, 'destroy']);
 
     // Agents
     Route::get('/agents', [AgentController::class, 'index']);
 
     // Calls (optional / journey modal)
     Route::get('/enquiries/{id}/calls', [CallController::class, 'index']);

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy']);
});