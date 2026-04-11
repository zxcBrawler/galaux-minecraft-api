<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Server;
use App\Services\ShopService;
use Illuminate\Http\JsonResponse;

class ShopController extends Controller
{
    protected ShopService $shopService;

    public function __construct(ShopService $shopService)
    {
        $this->shopService = $shopService;
    }

    public function getShopWithProducts(Server $id_server)
    {
        $shop = $this->shopService->getShopWithProducts($id_server);

        return response()->json($shop);
    }

    public function createServerShop(Server $id_server)
    {
        $shop = $this->shopService->createShop($id_server);

        return response()->json($shop, 201);
    }
}
