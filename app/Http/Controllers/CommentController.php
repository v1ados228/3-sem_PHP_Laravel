<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Article;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $comments = Comment::latest()->paginate(10);
        return view('comment.index', ['comments' => $comments]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Article $article)
    {
        return view('comment.create', ['article' => $article]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Article $article)
    {
        $request->validate([
            'text' => 'required|min:3|max:500',
        ]);

        try {
            // Получаем ID авторизованного пользователя
            $userId = auth()->id();
            
            if (!$userId) {
                return redirect()->route('article.show', ['article' => $article->id])
                    ->with('error', 'Вы должны быть авторизованы для добавления комментариев.');
            }

            $comment = new Comment;
            $comment->text = $request->text;
            $comment->article_id = $article->id;
            $comment->users_id = $userId;
            $comment->save();

            return redirect()->route('article.show', ['article' => $article->id])
                ->with('message', 'Comment created successfully');
        } catch (\Exception $e) {
            Log::error('Ошибка при сохранении комментария: ' . $e->getMessage());
            return redirect()->route('article.show', ['article' => $article->id])
                ->with('error', 'Ошибка при сохранении комментария: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Comment $comment)
    {
        return view('comment.show', ['comment' => $comment]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Article $article, Comment $comment)
    {
        // Проверяем, что комментарий принадлежит статье
        if ($comment->article_id !== $article->id) {
            abort(404);
        }
        
        // Проверка прав через шлюз (только модератор может редактировать)
        $this->authorize('is-moderator');
        
        return view('comment.edit', ['comment' => $comment, 'article' => $article]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Article $article, Comment $comment)
    {
        // Проверяем, что комментарий принадлежит статье
        if ($comment->article_id !== $article->id) {
            abort(404);
        }

        // Проверка прав через шлюз (только модератор может обновлять)
        $this->authorize('is-moderator');

        $request->validate([
            'text' => 'required|min:3|max:500',
        ]);

        $comment->text = $request->text;
        $comment->save();

        return redirect()->route('article.show', ['article' => $article->id])
            ->with('message', 'Comment updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Article $article, Comment $comment)
    {
        // Проверяем, что комментарий принадлежит статье
        if ($comment->article_id !== $article->id) {
            abort(404);
        }

        // Проверка прав через шлюз (только модератор может удалять)
        $this->authorize('is-moderator');

        $comment->delete();

        return redirect()->route('article.show', ['article' => $article->id])
            ->with('message', 'Comment deleted successfully');
    }
}

