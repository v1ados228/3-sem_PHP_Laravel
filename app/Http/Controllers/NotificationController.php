<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Отметить уведомление как прочитанное и перенаправить на статью
     */
    public function markAsReadAndRedirect($notificationId)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('auth.login.show');
        }
        
        $notification = $user->notifications()->find($notificationId);
        
        if ($notification) {
            $notification->markAsRead();
            
            // Получаем ID статьи из данных уведомления
            $articleId = $notification->data['article_id'] ?? null;
            
            if ($articleId) {
                // Проверяем, существует ли статья
                $article = Article::find($articleId);
                
                if ($article) {
                    return redirect()->route('article.show', ['article' => $articleId]);
                } else {
                    // Статья удалена - показываем сообщение и перенаправляем на главную
                    return redirect()->route('main.index')
                        ->with('error', 'Статья была удалена');
                }
            }
        }
        
        return redirect()->route('main.index');
    }
    
    /**
     * Получить непрочитанные уведомления (для AJAX)
     */
    public function getUnread()
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['notifications' => []]);
        }
        
        // Получаем все непрочитанные уведомления
        $allNotifications = $user->unreadNotifications()
            ->latest()
            ->take(10)
            ->get();
        
        // Собираем все ID статей из уведомлений
        $articleIds = $allNotifications->map(function ($notification) {
            return $notification->data['article_id'] ?? null;
        })->filter()->unique()->values()->toArray();
        
        // Проверяем существование всех статей одним запросом (только если есть ID)
        $existingArticleIds = [];
        if (!empty($articleIds)) {
            $existingArticleIds = Article::whereIn('id', $articleIds)->pluck('id')->toArray();
        }
        
        // Формируем список уведомлений с информацией о существовании статьи
        $notifications = $allNotifications->map(function ($notification) use ($existingArticleIds) {
            $articleId = $notification->data['article_id'] ?? null;
            $articleExists = $articleId && in_array($articleId, $existingArticleIds);
            
            return [
                'id' => $notification->id,
                'article_id' => $articleId,
                'article_title' => $notification->data['article_title'] ?? 'Новая статья',
                'message' => $notification->data['message'] ?? 'Новое уведомление',
                'created_at' => $notification->created_at->diffForHumans(),
                'article_exists' => $articleExists,
            ];
        });
        
        // Подсчитываем общее количество непрочитанных уведомлений
        $totalCount = $user->unreadNotifications()->count();
        
        return response()->json([
            'notifications' => $notifications,
            'count' => $totalCount
        ]);
    }
}

