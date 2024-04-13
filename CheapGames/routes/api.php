<?php
use App\Http\Controllers\Api\UserController; 

//Route::apiResource('usuarios', 'Api\UserController');

Route::resources([
    'usuario' => UserController::class,
]);