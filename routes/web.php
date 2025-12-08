<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MainController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CommentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

//Auth - публичные маршруты (без аутентификации)
Route::get('/auth/register', [AuthController::class, 'showRegister'])->name('auth.register.show');
Route::post('/auth/register', [AuthController::class, 'register'])->name('auth.register');
Route::get('/auth/login', [AuthController::class, 'showLogin'])->name('auth.login.show');
Route::post('/auth/login', [AuthController::class, 'login'])->name('auth.login');
Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');

//Main - публичные маршруты
Route::get('/', [MainController::class, 'index'])->name('main.index');
Route::get('/full_image/{img}', [MainController::class, 'show'])->name('main.show');

//Article - защищенные маршруты (требуется аутентификация)
Route::middleware('auth:sanctum')->group(function () {
    Route::resource('/article', ArticleController::class);
    
    //Comments - защищенные маршруты
    Route::post('/article/{article}/comment', [CommentController::class, 'store'])->name('article.comment.store');
    Route::get('/article/{article}/comment/{comment}/edit', [CommentController::class, 'edit'])->name('article.comment.edit');
    Route::put('/article/{article}/comment/{comment}', [CommentController::class, 'update'])->name('article.comment.update');
    Route::patch('/article/{article}/comment/{comment}', [CommentController::class, 'update'])->name('article.comment.update');
    Route::delete('/article/{article}/comment/{comment}', [CommentController::class, 'destroy'])->name('article.comment.destroy');
    
    //Модерация комментариев (только для модераторов) - должно быть ПЕРЕД /comment/{comment}
    Route::get('/comment/moderate', [CommentController::class, 'moderate'])->name('comment.moderate');
    Route::patch('/comment/{comment}/approve', [CommentController::class, 'approve'])->name('comment.approve');
    Route::delete('/comment/{comment}/reject', [CommentController::class, 'reject'])->name('comment.reject');
    
    //Общие маршруты комментариев (должны быть после специфичных)
    Route::get('/comment/{comment}', [CommentController::class, 'show'])->name('comment.show');
    Route::get('/comment', [CommentController::class, 'index'])->name('comment.index');
});

Route::get('/about', function(){
    return view('main.about');
});

Route::get('/contact', function(){
    $array = [
        'name' => 'Moscow Polytech',
        'address' => 'B. Semenovskaya h.38',
        'email' => '..@mospolytech.ru',
        'phone' => '8(499)784-9396'
    ];
    return view('main.contact', ['contact' => $array]); 
});