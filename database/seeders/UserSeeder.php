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
        // Создаем пользователя-модератора
        $moderator = User::firstOrCreate(
            ['email' => 'moderator@example.com'],
            [
                'name' => 'Модератор',
                'email' => 'moderator@example.com',
                'password' => Hash::make('password'),
            ]
        );

        // Получаем роль модератора
        $moderatorRole = Role::where('slug', 'moderator')->first();

        // Присваиваем роль модератора пользователю (если еще не присвоена)
        if ($moderatorRole && !$moderator->hasRole('moderator')) {
            $moderator->roles()->attach($moderatorRole->id);
        }
    }
}

