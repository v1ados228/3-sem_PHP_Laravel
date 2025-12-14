<?php

namespace App\Http\Controllers;

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
                return redirect()->route('article.show', ['article' => $articleId]);
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
        
        $notifications = $user->unreadNotifications()
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'article_id' => $notification->data['article_id'] ?? null,
                    'article_title' => $notification->data['article_title'] ?? 'Новая статья',
                    'message' => $notification->data['message'] ?? 'Новое уведомление',
                    'created_at' => $notification->created_at->diffForHumans(),
                ];
            });
        
        return response()->json([
            'notifications' => $notifications,
            'count' => $user->unreadNotifications()->count()
        ]);
    }
}

