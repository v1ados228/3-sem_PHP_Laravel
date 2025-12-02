<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Модератор',
                'slug' => 'moderator',
            ],
            [
                'name' => 'Читатель',
                'slug' => 'reader',
            ],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['slug' => $role['slug']],
                $role
            );
        }
    }
}

