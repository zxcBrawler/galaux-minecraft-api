<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Server;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductService
{
    public function getProductsByServer(Server $server, int $perPage = 12): LengthAwarePaginator
    {
        $shop = $server->shop;

        if (!$shop) {
            throw new NotFoundHttpException('Магазин для этого сервера не найден');
        }

        return $shop->products()
            ->latest()
            ->paginate($perPage);
    }

    public function createProduct(Server $server, array $data, User $actor): Product
    {
        $this->authorizeAction($server, $actor);

        $shop = $server->shop;
        if (!$shop) {
            throw new NotFoundHttpException('У сервера нет магазина для добавления товаров');
        }

        return $shop->products()->create($data);
    }

    public function updateProduct(Product $product, array $data, User $actor): Product
    {
        $server = $product->shop->server;
        $this->authorizeAction($server, $actor);

        $product->update($data);
        return $product;
    }

    public function deleteProduct(Product $product, User $actor): void
    {
        $server = $product->shop->server;
        $this->authorizeAction($server, $actor);

        $product->delete();
    }

    private function authorizeAction(Server $server, User $actor): void
    {
        if (!$actor->isServerOwner($server->id_server) && $actor->role !== UserRole::ADMIN) {
            throw new AuthorizationException('У вас нет прав для управления товарами этого сервера.');
        }
    }
}
