<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Server;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $productService
    ) {}

    public function getProducts(Server $id_server)
    {
        return response()->json($this->productService->getProductsByServer($id_server));
    }

    public function getProductById(Product $id_product)
    {
        return response()->json($id_product);
    }

    public function addProductToServer(Request $request, Server $id_server)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'cost' => 'required|numeric|min:0',
            'item_id' => 'required|string|max:255',
            'is_active' => 'sometimes|boolean'
        ]);

        $product = $this->productService->createProduct($id_server, $validated, $request->user());

        return response()->json($product, 201);
    }

    public function updateProduct(Request $request, Product $id_product)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'cost' => 'sometimes|numeric|min:0',
            'is_active' => 'sometimes|boolean'
        ]);

        $product = $this->productService->updateProduct($id_product, $validated, $request->user());

        return response()->json($product);
    }

    public function deleteProduct(Request $request, Product $id_product)
    {
        $this->productService->deleteProduct($id_product, $request->user());

        return response()->json(['message' => 'Товар удален']);
    }
}
