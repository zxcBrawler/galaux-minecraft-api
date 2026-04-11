<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Главный Админ',
            'login' => 'admin',
            'email' => 'admin@galaux.test',
            'role' => 'admin',
            'money' => 9999.99,
        ]);

        User::factory()->create([
            'name' => 'Иван Модер',
            'login' => 'moderator',
            'role' => 'moderator',
        ]);

        $parent = User::factory()->create([
            'name' => 'Отец Серверный',
            'login' => 'parent_user',
            'is_child' => false,
        ]);

        User::factory()->create([
            'name' => 'Младший Игрок',
            'login' => 'child_user',
            'is_child' => true,
            'parent_id' => $parent->id_user,
            'parent_settings' => json_encode(['play_time_limit' => 120, 'chat_enabled' => false]),
        ]);

        User::factory(10)->create();
    }
}
