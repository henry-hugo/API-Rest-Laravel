<?php
use App\Http\Controllers\Api\UserController; 
use App\Http\Controllers\Api\CategoryController; 
use App\Http\Controllers\Api\PlatformController; 

//Route::apiResource('usuarios', 'Api\UserController');

Route::resources([
    'usuario' => UserController::class,
]);



Route::resources([
    'categoria' => CategoryController::class,
]);



Route::resources([
    'plataforma' => PlatformController::class,
]);