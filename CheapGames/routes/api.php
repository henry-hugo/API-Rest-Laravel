<?php
use App\Http\Controllers\Api\UserController; 
use App\Http\Controllers\Api\Auth\AuthController;

//Route::apiResource('usuarios', 'Api\UserController');

Route::resources([
    'usuario' => UserController::class,
]);

Route::post('/login', [AuthController::class, 'auth']);