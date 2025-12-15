<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ArticleController extends Controller
{
    /**
     * Получить список статей с пагинацией.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Article::class);
        
        $page = $request->get('page', 1);
        
        $articles = Cache::remember('api_articles_page_' . $page, 3600, function () {
            return Article::latest()->paginate(5);
        });
        
        return response()->json([
            'data' => $articles,
        ]);
    }
    
    /**
     * Создать новую статью.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Article::class);
        
        $request->validate([
            'date' => 'required|date',
            'title' => 'required|min:10',
            'text' => 'max:100',
        ]);
        
        $article = new Article();
        $article->date_public = $request->date;
        $article->title = $request->title;
        $article->text = $request->text;
        $article->users_id = auth()->id();
        $article->save();
        
        $article->refresh();
        $article->load('user');
        
        // Очистка кэша списка статей
        Cache::forget('api_articles_page_' . $request->get('page', 1));
        
        return response()->json([
            'message' => 'Статья создана успешно',
            'article' => $article,
        ], 201);
    }
    
    /**
     * Показать статью.
     */
    public function show(Article $article)
    {
        $this->authorize('view', $article);
        
        $cachedArticle = Cache::rememberForever('api_article_' . $article->id, function () use ($article) {
            $article->refresh();
            $article->load(['comments' => function ($query) {
                $query->where('is_approved', true)->with('user');
            }]);
            return $article;
        });
        
        return response()->json([
            'data' => $cachedArticle,
        ]);
    }
    
    /**
     * Обновить статью.
     */
    public function update(Request $request, Article $article)
    {
        $this->authorize('update', $article);
        
        $request->validate([
            'date' => 'required|date',
            'title' => 'required|min:10',
            'text' => 'max:100',
        ]);
        
        $article->date_public = $request->date;
        $article->title = $request->title;
        $article->text = $request->text;
        $article->users_id = auth()->id();
        $article->save();
        
        Cache::forget('api_article_' . $article->id);
        Cache::flush();
        
        return response()->json([
            'message' => 'Статья обновлена',
            'article' => $article,
        ]);
    }
    
    /**
     * Удалить статью.
     */
    public function destroy(Article $article)
    {
        $this->authorize('delete', $article);
        
        $article->delete();
        
        Cache::forget('api_article_' . $article->id);
        Cache::flush();
        
        return response()->json([
            'message' => 'Статья удалена',
        ]);
    }
}
