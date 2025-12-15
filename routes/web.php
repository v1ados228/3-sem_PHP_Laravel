<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MainController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\NotificationController;
use App\Http\Middleware\LogArticleView;

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

//Main - публичные маршруты
Route::get('/', [MainController::class, 'index'])->name('main.index');
Route::get('/full_image/{img}', [MainController::class, 'show'])->name('main.show');

// Auth - публичные маршруты (для веб-форм)
Route::get('/auth/register', [AuthController::class, 'showRegister'])->name('auth.register.show');
Route::post('/auth/register', [AuthController::class, 'register'])->name('auth.register');
Route::get('/auth/login', [AuthController::class, 'showLogin'])->name('auth.login.show');
Route::post('/auth/login', [AuthController::class, 'login'])->name('auth.login');
Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');

// Article/Comment/Notification - веб-маршруты (для текущих blade-вьюх)
Route::middleware('auth:sanctum')->group(function () {
    // Статьи
    Route::get('/article', [ArticleController::class, 'index'])->name('article.index');
    Route::get('/article/create', [ArticleController::class, 'create'])->name('article.create');
    Route::post('/article', [ArticleController::class, 'store'])->name('article.store');
    Route::get('/article/{article}/edit', [ArticleController::class, 'edit'])->name('article.edit');
    Route::put('/article/{article}', [ArticleController::class, 'update'])->name('article.update');
    Route::patch('/article/{article}', [ArticleController::class, 'update'])->name('article.update');
    Route::delete('/article/{article}', [ArticleController::class, 'destroy'])->name('article.destroy');
    Route::get('/article/{article}', [ArticleController::class, 'show'])
        ->middleware(LogArticleView::class)
        ->name('article.show');

    // Комментарии
    Route::post('/article/{article}/comment', [CommentController::class, 'store'])->name('article.comment.store');
    Route::get('/article/{article}/comment/{comment}/edit', [CommentController::class, 'edit'])->name('article.comment.edit');
    Route::put('/article/{article}/comment/{comment}', [CommentController::class, 'update'])->name('article.comment.update');
    Route::patch('/article/{article}/comment/{comment}', [CommentController::class, 'update'])->name('article.comment.update');
    Route::delete('/article/{article}/comment/{comment}', [CommentController::class, 'destroy'])->name('article.comment.destroy');

    // Модерация комментариев (только для модераторов)
    Route::get('/comment/moderate', [CommentController::class, 'moderate'])->name('comment.moderate');
    Route::patch('/comment/{comment}/approve', [CommentController::class, 'approve'])->name('comment.approve');
    Route::delete('/comment/{comment}/reject', [CommentController::class, 'reject'])->name('comment.reject');

    // Общие маршруты комментариев
    Route::get('/comment/{comment}', [CommentController::class, 'show'])->name('comment.show');
    Route::get('/comment', [CommentController::class, 'index'])->name('comment.index');

    // Уведомления
    Route::get('/notification/{notification}/read', [NotificationController::class, 'markAsReadAndRedirect'])->name('notification.read');
    Route::get('/notifications/unread', [NotificationController::class, 'getUnread'])->name('notifications.unread');
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