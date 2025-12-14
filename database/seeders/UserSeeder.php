<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Получаем роль модератора
        $moderatorRole = Role::where('slug', 'moderator')->first();
        
        if (!$moderatorRole) {
            return;
        }

        // Ищем существующего модератора по роли
        $existingModerator = User::whereHas('roles', function ($query) use ($moderatorRole) {
            $query->where('roles.id', $moderatorRole->id);
        })->first();

        if ($existingModerator) {
            // Обновляем email существующего модератора
            $existingModerator->email = 'maryon096@mail.ru';
            $existingModerator->save();
            $moderator = $existingModerator;
        } else {
            // Создаем нового пользователя-модератора
            $moderator = User::firstOrCreate(
                ['email' => 'maryon096@mail.ru'],
                [
                    'name' => 'Модератор',
                    'email' => 'maryon096@mail.ru',
                    'password' => Hash::make('password'),
                ]
            );
        }

        // Присваиваем роль модератора пользователю (если еще не присвоена)
        if (!$moderator->hasRole('moderator')) {
            $moderator->roles()->attach($moderatorRole->id);
        }
        
        // Создаем пользователя с ролью reader
        $readerRole = Role::where('slug', 'reader')->first();
        
        if ($readerRole) {
            // Ищем существующего читателя по роли
            $existingReader = User::whereHas('roles', function ($query) use ($readerRole) {
                $query->where('roles.id', $readerRole->id);
            })->first();

            if (!$existingReader) {
                // Создаем нового пользователя-читателя
                $reader = User::firstOrCreate(
                    ['email' => 'reader@test.com'],
                    [
                        'name' => 'Читатель',
                        'email' => 'reader@test.com',
                        'password' => Hash::make('password'),
                    ]
                );

                // Присваиваем роль читателя пользователю
                if (!$reader->hasRole('reader')) {
                    $reader->roles()->attach($readerRole->id);
                }
            }
        }
    }
}

