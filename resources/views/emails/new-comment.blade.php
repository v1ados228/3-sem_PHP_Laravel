<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Новый комментарий</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .email-container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            background-color: #0d6efd;
            color: #ffffff;
            padding: 20px;
            border-radius: 8px 8px 0 0;
            margin: -30px -30px 20px -30px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            margin: 20px 0;
        }
        .comment-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
            border-left: 4px solid #0d6efd;
        }
        .article-title {
            font-size: 18px;
            font-weight: bold;
            color: #0d6efd;
            margin-bottom: 10px;
        }
        .comment-meta {
            color: #666;
            font-size: 14px;
            margin: 10px 0;
        }
        .comment-text {
            margin-top: 15px;
            padding: 15px;
            background-color: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            font-style: italic;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #0d6efd;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .button:hover {
            background-color: #0b5ed7;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            color: #666;
            font-size: 12px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>Новый комментарий</h1>
        </div>
        
        <div class="content">
            <p>Здравствуйте!</p>
            <p>К статье был добавлен новый комментарий, который требует вашего внимания.</p>
            
            <div class="comment-info">
                <div class="article-title">Статья: {{ $comment->article->title }}</div>
                <div class="comment-meta">
                    <strong>Автор комментария:</strong> {{ $comment->user->name ?? 'Неизвестен' }}<br>
                    <strong>Email автора:</strong> {{ $comment->user->email ?? 'Не указан' }}<br>
                    <strong>Дата комментария:</strong> {{ $comment->created_at->format('d.m.Y H:i') }}<br>
                    @if($comment->article->user)
                    <strong>Автор статьи:</strong> {{ $comment->article->user->name ?? 'Неизвестен' }}
                    @endif
                </div>
                <div class="comment-text">
                    <strong>Текст комментария:</strong><br>
                    {{ $comment->text }}
                </div>
            </div>
            
            <p>Пожалуйста, проверьте комментарий и при необходимости выполните модерацию.</p>
            
            <a href="{{ url('/article/' . $comment->article->id) }}" class="button">Просмотреть комментарий</a>
        </div>
        
        <div class="footer">
            <p>Это автоматическое уведомление от системы {{ config('app.name') }}.</p>
            <p>Пожалуйста, не отвечайте на это письмо.</p>
        </div>
    </div>
</body>
</html>

