<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ArticleView;
use Symfony\Component\HttpFoundation\Response;

class LogArticleView
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        
        // Проверяем, что это запрос на просмотр статьи
        if ($request->routeIs('article.show') && $request->route('article')) {
            $article = $request->route('article');
            
            // Сохраняем просмотр статьи
            ArticleView::create([
                'article_id' => $article->id,
                'url' => $request->fullUrl(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'viewed_at' => now(),
            ]);
        }
        
        return $response;
    }
}

