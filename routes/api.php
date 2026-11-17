<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ProjectController;
use App\Http\Controllers\Api\V1\TaskController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

  // Routes publiques (pas besoin d'être connecté)
  // throttle : 6 tentatives par minute et par IP, contre le brute-force
  Route::prefix('auth')->middleware('throttle:6,1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login',    [AuthController::class, 'login']);
  });

  // Routes protégées (token requis)
  Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    Route::apiResource('projects', ProjectController::class);
    Route::apiResource('tasks',    TaskController::class);
    Route::get('users', [UserController::class, 'index']);
    Route::patch('users/{id}/role', [UserController::class, 'updateRole']);
  });
});
