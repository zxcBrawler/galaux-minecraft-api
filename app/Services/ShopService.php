<?php
namespace App\Services;

use App\Models\Server;
use App\Models\Shop;
use Illuminate\Validation\ValidationException;

class ShopService
{
    public function getShopWithProducts(Server $server): Shop
    {
        $shop = $server->shop()->with('products')->first();

        if (!$shop) {
            throw ValidationException::withMessages(['shop' => 'Магазин для этого сервера не найден.']);
        }

        return $shop;
    }

    public function createShop(Server $server): Shop
    {
        if ($server->shop()->exists()) {
            throw ValidationException::withMessages(['shop' => 'Магазин для этого сервера уже существует.']);
        }

        return $server->shop()->create();
    }
}
