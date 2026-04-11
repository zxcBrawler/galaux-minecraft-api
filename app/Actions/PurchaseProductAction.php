<?php

namespace App\Actions;

use App\Enums\UserAction;
use App\Models\User;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\UserLog;
use Illuminate\Support\Facades\DB;

class PurchaseProductAction
{
    /**
     * * @throws \Throwable
     */
    public function execute(User $user, Product $product, int $quantity = 1): Purchase
    {
        $totalCost = $product->cost * $quantity;

        if ((float)$user->money < $totalCost) {
            throw new \Exception('Недостаточно средств для покупки');
        }

        return DB::transaction(function () use ($user, $product, $quantity, $totalCost) {
            $user->decrement('money', $totalCost);

            $product->increment('bought_count', $quantity);

            $purchase = $user->purchases()->create([
                'product_id'   => $product->getKey(),
                'quantity'     => $quantity,
                'price_paid'   => $product->cost,
                'purchased_at' => now()
            ]);
            UserLog::create([
                'user_id'   => $user->id_user,
                'server_id' => $product->shop->server_id,
                'action'    => UserAction::ITEM_BOUGHT,
                'details'   => "Куплен товар: {$product->name} (x{$quantity}) за {$totalCost}. Баланс после: {$user->money}"
            ]);

            return $purchase;
        });
    }
}
