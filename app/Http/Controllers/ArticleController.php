<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Role;
use App\Mail\NewArticleNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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
        
        // Загружаем связь с пользователем для email
        $article->load('user');
        
        // Находим всех модераторов и отправляем им уведомления
        $moderatorRole = Role::where('slug', 'moderator')->first();
        if ($moderatorRole) {
            $moderators = $moderatorRole->users()->get();
            foreach ($moderators as $moderator) {
                Mail::to($moderator->email)->send(new NewArticleNotification($article));
            }
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
