<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use App\Services\PurchaseService;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function __construct(
        protected PurchaseService $purchaseService
    ) {}

    public function buyProduct(Request $request, Product $id_product)
    {
        $quantity = (int) $request->input('quantity', 1);

        try {
            $purchase = $this->purchaseService->makePurchase(
                $request->user(),
                $id_product,
                $quantity
            );

            return response()->json([
                'message' => 'Покупка успешно совершена',
                'purchase' => $purchase->load('product')
            ]);
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function userPurchases(Request $request, User $id_user)
    {
        $purchases = $this->purchaseService->getUserPurchases(
            $request->user(),
            $id_user
        );

        return response()->json($purchases);
    }
}
