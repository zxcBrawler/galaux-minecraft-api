<?php

namespace Database\Seeders;

use App\Models\Server;
use App\Models\ServerImage;
use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ServerContentSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $tagsData = ['Мини-игры', 'Инклюзивные', 'Без голоса', 'С родителями', 'Для новичков', 'Выживание'];
        $tags = collect($tagsData)->map(fn($name) => Tag::updateOrCreate(['slug' => Str::slug($name)], ['name' => $name]));

        $allMods = \App\Models\Mod::factory(20)->create();

        Server::factory(12)->create()->each(function ($server) use ($tags, $allMods) {
            $server->tags()->attach($tags->random(rand(1, 3))->pluck('id_tag'));

            $randomMods = $allMods->random(rand(2, 5));

            foreach ($randomMods as $mod) {
                $server->mods()->attach($mod->id_mod, [
                    'mod_version' => '1.20.1',
                    'is_required' => (bool)rand(0, 1),
                    'created_at' => now(),
                ]);
            }

            for ($i = 0; $i < 3; $i++) {
                ServerImage::create([
                    'server_id' => $server->id_server,
                    'path' => 'https://picsum.photos/seed/' . Str::random(10) . '/800/600',
                    'is_main' => ($i === 0),
                ]);
            }
        });
    }
}
