<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ArticleController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Auth routes (API)
Route::prefix('auth')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('api.auth.register.show');
    Route::post('/register', [AuthController::class, 'register'])->name('api.auth.register');
    Route::get('/login', [AuthController::class, 'showLogin'])->name('api.auth.login.show');
    Route::post('/login', [AuthController::class, 'login'])->name('api.auth.login');
    
    Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout'])->name('api.auth.logout');
});

// Article routes (API, защищены sanctum)
Route::middleware('auth:sanctum')->prefix('articles')->group(function () {
    Route::get('/', [ArticleController::class, 'index'])->name('api.article.index');
    Route::post('/', [ArticleController::class, 'store'])->name('api.article.store');
    Route::get('/{article}', [ArticleController::class, 'show'])->name('api.article.show');
    Route::put('/{article}', [ArticleController::class, 'update'])->name('api.article.update');
    Route::patch('/{article}', [ArticleController::class, 'update'])->name('api.article.update');
    Route::delete('/{article}', [ArticleController::class, 'destroy'])->name('api.article.destroy');
});
