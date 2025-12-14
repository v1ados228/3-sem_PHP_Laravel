<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ArticleView;
use App\Models\Comment;
use App\Models\Role;
use Illuminate\Support\Facades\Mail;
use App\Mail\DailyStatisticsMail;

class SendDailyStatistics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'statistics:daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily statistics to moderators';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Сбор статистики за день...');
        
        // Получаем начало и конец текущего дня
        $startOfDay = now()->startOfDay();
        $endOfDay = now()->endOfDay();
        
        // Получаем количество просмотров статей за сегодня
        $viewsCount = ArticleView::whereBetween('viewed_at', [$startOfDay, $endOfDay])
            ->count();
        
        // Получаем количество новых комментариев за сегодня
        $commentsCount = Comment::whereBetween('created_at', [$startOfDay, $endOfDay])
            ->count();
        
        // Получаем статистику по статьям (сколько раз каждая статья была просмотрена)
        $articleViews = ArticleView::whereBetween('viewed_at', [$startOfDay, $endOfDay])
            ->selectRaw('article_id, COUNT(*) as views')
            ->groupBy('article_id')
            ->with('article:id,title')
            ->get()
            ->map(function ($view) {
                return [
                    'title' => $view->article->title ?? 'Удаленная статья',
                    'views' => $view->views,
                ];
            });
        
        $this->info("Просмотров статей: {$viewsCount}");
        $this->info("Новых комментариев: {$commentsCount}");
        
        // Получаем всех модераторов
        $moderatorRole = Role::where('slug', 'moderator')->first();
        
        if (!$moderatorRole) {
            $this->error('Роль модератора не найдена!');
            return 1;
        }
        
        $moderators = $moderatorRole->users()->get();
        
        if ($moderators->isEmpty()) {
            $this->error('Модераторы не найдены!');
            return 1;
        }
        
        // Отправляем статистику каждому модератору
        foreach ($moderators as $moderator) {
            try {
                Mail::to($moderator->email)->send(new DailyStatisticsMail(
                    $viewsCount,
                    $commentsCount,
                    $articleViews,
                    $startOfDay->format('d.m.Y')
                ));
                
                $this->info("Статистика отправлена на {$moderator->email}");
            } catch (\Exception $e) {
                $this->error("Ошибка при отправке на {$moderator->email}: " . $e->getMessage());
            }
        }
        
        $this->info('Статистика успешно отправлена всем модераторам!');
        
        return 0;
    }
}

