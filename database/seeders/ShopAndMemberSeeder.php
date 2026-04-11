<?php

namespace Database\Seeders;

use App\Models\Server;
use App\Models\Shop;
use App\Models\Product;
use App\Models\User;
use App\Enums\ServerRole;
use Illuminate\Database\Seeder;

class ShopAndMemberSeeder extends Seeder
{
    public function run(): void
    {
        $servers = Server::all();
        $users = User::all();

        if ($servers->isEmpty() || $users->isEmpty()) {
            $this->command->error('Сначала запусти UserSeeder и ServerSeeder!');
            return;
        }

        foreach ($servers as $server) {
            $shop = Shop::create(['server_id' => $server->id_server]);
            Product::factory(rand(3, 7))->create(['shop_id' => $shop->id_shop]);

            $owner = $users->random();

            $server->members()->create([
                'user_id'   => $owner->id_user,
                'role'      => ServerRole::OWNER,
                'joined_at' => now()
            ]);

            $otherUsers = $users->where('id_user', '!=', $owner->id_user)->random(rand(5, 12));

            foreach ($otherUsers as $user) {
                $server->members()->create([
                    'user_id'   => $user->id_user,
                    'role'      => fake()->randomElement([
                        ServerRole::ADMIN,
                        ServerRole::MODERATOR,
                        ServerRole::BUILDER
                    ]),
                    'joined_at' => fake()->dateTimeBetween('-1 month')
                ]);
            }
        }
    }
}
