<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Role;
use App\Jobs\VeryLongJob;
use App\Events\NewArticleEvent;
use App\Notifications\NewArticleNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Проверка прав через политику
        $this->authorize('viewAny', Article::class);
        
        $articles = Article::latest()->paginate(5);
        return view('/article/article', ['articles'=>$articles]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Проверка прав через политику
        $this->authorize('create', Article::class);
        
        return view('article.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Проверка прав через политику
        $this->authorize('create', Article::class);
        
        $request->validate([
            'date'=>'required|date',
            'title'=>'required|min:10',
            'text'=>'max:100'
        ]);
        $article = new Article;
        $article->date_public = $request->date;
        $article->title = request('title');
        $article->text = $request->text;
        $article->users_id = auth()->id();
        $article->save();
        
        // Перезагружаем модель из базы данных для корректной сериализации
        $article->refresh();
        $article->load('user');
        
        // Отправляем уведомления всем зарегистрированным пользователям
        // (зарегистрированным, но не аутентифицированным в данной сессии)
        // Не отправляем создателю статьи
        try {
            // Получаем всех пользователей
            $allUsers = \App\Models\User::all();
            $notifiedCount = 0;
            $currentUserId = auth()->id();
            \Log::info('Current user ID (article creator): ' . $currentUserId);
            \Log::info('Total users in system: ' . $allUsers->count());
            
            // Отправляем уведомления всем пользователям, кроме создателя статьи
            foreach ($allUsers as $user) {
                // Проверяем, что это не текущий пользователь (создатель статьи)
                if ($user->id !== $currentUserId) {
                    try {
                        \Log::info('Attempting to send notification to user ID: ' . $user->id . ' (' . $user->email . ')');
                        
                        // Проверяем количество уведомлений до отправки
                        $notificationsBefore = DB::table('notifications')
                            ->where('notifiable_type', 'App\Models\User')
                            ->where('notifiable_id', $user->id)
                            ->count();
                        
                        $user->notify(new NewArticleNotification($article));
                        
                        // Проверяем количество уведомлений после отправки
                        $notificationsAfter = DB::table('notifications')
                            ->where('notifiable_type', 'App\Models\User')
                            ->where('notifiable_id', $user->id)
                            ->count();
                        
                        if ($notificationsAfter > $notificationsBefore) {
                            $notifiedCount++;
                            \Log::info('✓ Notification successfully sent and saved to DB for user ID: ' . $user->id . ' (' . $user->email . ')');
                        } else {
                            \Log::warning('⚠ Notification sent but NOT saved to DB for user ID: ' . $user->id);
                        }
                    } catch (\Exception $e) {
                        \Log::error('✗ Error sending notification to user ID ' . $user->id . ': ' . $e->getMessage());
                        \Log::error('Stack trace: ' . $e->getTraceAsString());
                    }
                } else {
                    \Log::info('Skipping user ID: ' . $user->id . ' (is article creator)');
                }
            }
            
            \Log::info('=== Summary: Notifications sent to ' . $notifiedCount . ' out of ' . ($allUsers->count() - 1) . ' users (excluding creator) for article ID: ' . $article->id . ' ===');
        } catch (\Exception $e) {
            \Log::error('Error sending notifications: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
        }
        
        // Отправляем событие для онлайн-уведомления пользователей
        // Небольшая задержка, чтобы событие успело отправиться перед redirect
        try {
            event(new NewArticleEvent($article));
            \Log::info('NewArticleEvent broadcasted for article ID: ' . $article->id);
            // Небольшая задержка для отправки события через Pusher
            usleep(500000); // 0.5 секунды
        } catch (\Exception $e) {
            \Log::error('Error broadcasting NewArticleEvent: ' . $e->getMessage());
        }
        
        // Отправляем задание в очередь для отправки уведомлений
        try {
            // Проверяем конфигурацию очереди
            $queueConnection = config('queue.default');
            $queueDriver = config("queue.connections.{$queueConnection}.driver");
            
            \Log::info("Queue config - Connection: {$queueConnection}, Driver: {$queueDriver}");
            
            // Отправляем задание через dispatch с явным указанием очереди
            $job = new VeryLongJob($article);
            
            // Используем dispatch с явным указанием, что задание должно быть в очереди
            dispatch($job)->onConnection('database')->onQueue('default');
            
            \Log::info('Job dispatched to database queue for article ID: ' . $article->id);
            
            // Проверяем сразу после отправки
            $jobsCount = DB::table('jobs')->count();
            \Log::info("Jobs in queue immediately after dispatch: {$jobsCount}");
            
            // Проверяем еще раз через небольшую задержку
            usleep(100000); // 0.1 секунды
            $jobsCount2 = DB::table('jobs')->count();
            \Log::info("Jobs in queue after 0.1s: {$jobsCount2}");
            
        } catch (\Exception $e) {
            \Log::error('Error dispatching job: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
        }
        
        return redirect()->route('article.index')->with('message','Create successful');
    }

    /**
     * Display the specified resource.
     */
    public function show(Article $article)
    {
        // Проверка прав через политику
        $this->authorize('view', $article);
        
        // Загружаем только одобренные комментарии с пользователями
        $article->load(['comments' => function ($query) {
            $query->where('is_approved', true)->with('user');
        }]);
        return view('article.show', ['article'=> $article]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Article $article)
    {
        // Проверка прав через политику
        $this->authorize('update', $article);
        
        return view('article.edit', ['article'=> $article]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Article $article)
    {
        // Проверка прав через политику
        $this->authorize('update', $article);
        
        $request->validate([
            'date'=>'required|date',
            'title'=>'required|min:10',
            'text'=>'max:100'
        ]);
        $article->date_public = $request->date;
        $article->title = request('title');
        $article->text = $request->text;
        $article->users_id = auth()->id();
        $article->save();
        return redirect()->route('article.show', ['article'=>$article->id])->with('message','Update successful');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Article $article)
    {
        // Проверка прав через политику
        $this->authorize('delete', $article);
        
        $article->delete();
        return redirect()->route('article.index')->with('message','Delete successful');
    }
}
