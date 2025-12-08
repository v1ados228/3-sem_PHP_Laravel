<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Article;
use App\Models\User;
use App\Models\Role;
use App\Mail\NewCommentNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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
     * Показать комментарии, ожидающие модерации (только для модераторов)
     */
    public function moderate()
    {
        // Проверяем, что пользователь авторизован
        if (!auth()->check()) {
            abort(403, 'Доступ запрещен. Необходима авторизация.');
        }

        // Проверяем, что пользователь является модератором
        if (!auth()->user()->isModerator()) {
            abort(403, 'Доступ запрещен. Только модераторы могут просматривать эту страницу.');
        }

        $comments = Comment::where('is_approved', false)
            ->with(['user', 'article'])
            ->latest()
            ->paginate(10);

        return view('comment.moderate', ['comments' => $comments]);
    }

    /**
     * Одобрить комментарий
     */
    public function approve(Comment $comment)
    {
        // Проверяем, что пользователь является модератором
        if (!auth()->user()->isModerator()) {
            abort(403, 'Доступ запрещен. Только модераторы могут одобрять комментарии.');
        }

        $comment->is_approved = true;
        $comment->save();

        return redirect()->route('comment.moderate')
            ->with('message', 'Комментарий одобрен и опубликован.');
    }

    /**
     * Отклонить комментарий
     */
    public function reject(Comment $comment)
    {
        // Проверяем, что пользователь является модератором
        if (!auth()->user()->isModerator()) {
            abort(403, 'Доступ запрещен. Только модераторы могут отклонять комментарии.');
        }

        $comment->delete();

        return redirect()->route('comment.moderate')
            ->with('message', 'Комментарий отклонен и удален.');
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
            $comment->is_approved = false; // Комментарий требует модерации
            $comment->save();
            
            // Загружаем связи для email
            $comment->load('user', 'article.user');
            
            // Находим всех модераторов и отправляем им уведомления
            $moderatorRole = Role::where('slug', 'moderator')->first();
            if ($moderatorRole) {
                $moderators = $moderatorRole->users()->get();
                foreach ($moderators as $moderator) {
                    Mail::to($moderator->email)->send(new NewCommentNotification($comment));
                }
            }

            return redirect()->route('article.show', ['article' => $article->id])
                ->with('message', 'Ваш комментарий отправлен на модерацию. Он будет опубликован после проверки модератором.');
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
        
        // Проверка прав через политику (пользователь может редактировать только свой комментарий)
        $this->authorize('update', $comment);
        
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

        // Проверка прав через политику (пользователь может обновлять только свой комментарий)
        $this->authorize('update', $comment);

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

        // Проверка прав через политику (пользователь может удалять только свой комментарий)
        $this->authorize('delete', $comment);

        $comment->delete();

        return redirect()->route('article.show', ['article' => $article->id])
            ->with('message', 'Comment deleted successfully');
    }
}

