<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'text',
        'users_id',
        'date_public',
    ];

    /**
     * Получить комментарии к статье
     */
    public function comments()
    {
        return $this->hasMany(Comment::class, 'article_id');
    }

    /**
     * Получить пользователя, создавшего статью
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    /**
     * Получить просмотры статьи
     */
    public function views()
    {
        return $this->hasMany(ArticleView::class);
    }
}
