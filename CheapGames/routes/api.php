<?php
use App\Http\Controllers\Api\UserController; 
use App\Http\Controllers\Api\CategoryController; 
use App\Http\Controllers\Api\PlatformController; 
use App\Http\Controllers\Api\PostController; 
use App\Http\Controllers\Api\RatingController; 
use App\Http\Controllers\Api\AuthController;
// use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::resources([
        'usuario' => UserController::class,
        'categoria' => CategoryController::class,
        'plataforma' => PlatformController::class,
        'reacao' => RatingController::class,
        'post' => PostController::class,
    ]);
    Route::post('/logout', [AuthController::class, 'logout']);
});
Route::post('usuario', [UserController::class, 'store']);

Route::get('categoria', [CategoryController::class, 'index']);
Route::get('categoria/{categoria}', [CategoryController::class, 'show']);

Route::get('plataforma', [PlatformController::class, 'index']);
Route::get('plataforma/{plataforma}', [PlatformController::class, 'show']);

Route::get('post', [PostController::class, 'index']);
Route::get('post/{post}', [PostController::class, 'show']);



/*
Route::apiResource('usuarios', 'Api\UserController');

Route::resources([
    'usuario' => UserController::class,
]);


Route::resources([
    'categoria' => CategoryController::class,
]);



Route::resources([
    'plataforma' => PlatformController::class,
]);

Route::resources([
    'reacao' => RatingController::class,
]);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::resources([
        'post' => PostController::class,
    ]);
});




 Route::resources([
     'post' => PostController::class,
 ]);
 Route::resources([
     'usuario' => UserController::class,
 ]);
 Route::resources([
     'categoria' => CategoryController::class,
 ]);
 Route::resources([
     'plataforma' => PlatformController::class,
 ]);
 Route::resources([
     'post' => PostController::class,
 ]);

 Route::get('reacao', [RatingController::class, 'index']);
Route::get('reacao/{reacao}', [RatingController::class, 'show']);
*/