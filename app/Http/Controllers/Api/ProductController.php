<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Product::query()
            ->with(['category', 'color']);

        if ($request->has('status')) {
            $query->where('address_status', $request->status);
        }

        $products = $query->get();

        return response()->json(['data' => $products]);
    }

    public function show(Product $product): JsonResponse
    {
        $product->load(['category', 'color']);
        return response()->json(['data' => $product]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'required|string|regex:/^\d+\s+[A-Z\s]+,\s+[A-Z\s]+,\s+[A-Z]{2,3}$/',
            'category_id' => 'required|exists:product_categories,id',
            'product_color_id' => 'required|exists:product_colors,id',
        ]);

        $product = Product::create($validated);

        return response()->json(['data' => $product], 201);
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'sometimes|required|string|regex:/^\d+\s+[A-Z\s]+,\s+[A-Z\s]+,\s+[A-Z]{2,3}$/',
            'category_id' => 'sometimes|required|exists:product_categories,id',
            'product_color_id' => 'sometimes|required|exists:product_colors,id',
        ]);

        $product->update($validated);

        return response()->json(['data' => $product]);
    }

    public function destroy(Product $product): JsonResponse
    {
        $product->delete();
        return response()->json(null, 204);
    }
} 