<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function index(): JsonResponse
    {
        $products = Product::all();

        return response()->json($products);
    }

    
    public function show(Product $product): JsonResponse
{
    return response()->json($product);
}

}


