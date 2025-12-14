<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Jobs\VeryLongJob;
use App\Events\NewArticleEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\DB;

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
