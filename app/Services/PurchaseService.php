<?php

namespace App\Services;

use App\Models\User;
use App\Models\Product;
use App\Models\Purchase;
use App\Enums\UserRole;
use App\Actions\PurchaseProductAction;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Auth\Access\AuthorizationException;

class PurchaseService
{
    public function __construct(
        protected PurchaseProductAction $purchaseAction
    ) {}

    /**
     * @throws \Throwable
     */
    public function makePurchase(User $user, Product $product, int $quantity): Purchase
    {
        return $this->purchaseAction->execute($user, $product, $quantity);
    }

    public function getUserPurchases(User $actor, User $targetUser, int $perPage = 20): LengthAwarePaginator
    {
        if ($actor->id_user !== $targetUser->id_user && $actor->role !== UserRole::ADMIN) {
            throw new AuthorizationException('Доступ к чужой истории покупок запрещен');
        }

        return $targetUser->purchases()
            ->with('product')
            ->latest('purchased_at')
            ->paginate($perPage);
    }
}
