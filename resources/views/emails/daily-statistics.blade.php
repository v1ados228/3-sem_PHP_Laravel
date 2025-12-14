<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #0d6efd;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 0 0 5px 5px;
        }
        .stat-box {
            background-color: white;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
            border-left: 4px solid #0d6efd;
        }
        .stat-number {
            font-size: 32px;
            font-weight: bold;
            color: #0d6efd;
        }
        .stat-label {
            color: #666;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #0d6efd;
            color: white;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Статистика использования сайта</h1>
            <p>За {{ $date }}</p>
        </div>
        
        <div class="content">
            <h2>Общая статистика</h2>
            
            <div class="stat-box">
                <div class="stat-number">{{ $viewsCount }}</div>
                <div class="stat-label">Просмотров статей</div>
            </div>
            
            <div class="stat-box">
                <div class="stat-number">{{ $commentsCount }}</div>
                <div class="stat-label">Новых комментариев</div>
            </div>
            
            @if($articleViews->count() > 0)
            <h2>Просмотры по статьям</h2>
            <table>
                <thead>
                    <tr>
                        <th>Статья</th>
                        <th>Количество просмотров</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($articleViews as $view)
                    <tr>
                        <td>{{ $view['title'] }}</td>
                        <td>{{ $view['views'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p>За сегодня не было просмотров статей.</p>
            @endif
            
            <p style="margin-top: 20px; color: #666; font-size: 12px;">
                Это автоматическое сообщение. Пожалуйста, не отвечайте на него.
            </p>
        </div>
    </div>
</body>
</html>

