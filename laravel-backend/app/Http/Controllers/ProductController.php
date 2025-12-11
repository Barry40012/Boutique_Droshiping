<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::orderByDesc('created_at')->get();
        return response()->json(['products' => $products]);
    }

    public function show(string $id)
    {
        $product = Product::find($id);
        if (! $product) {
            return response()->json(['error' => 'Produit non trouvé'], 404);
        }
        return response()->json(['product' => $product]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'supplier_price' => 'required|numeric',
            'selling_price' => 'required|numeric',
            'images' => 'nullable|array',
            'images.*' => 'string',
            'supplier_id' => 'nullable|string',
            'supplier_product_id' => 'nullable|string',
            'status' => 'nullable|in:active,inactive,out_of_stock',
        ]);

        $data['id'] = Str::uuid()->toString();
        $data['margin'] = ($data['selling_price'] ?? 0) - ($data['supplier_price'] ?? 0);
        $data['status'] = $data['status'] ?? 'active';

        $product = Product::create($data);

        return response()->json(['product' => $product], 201);
    }

    public function update(Request $request, string $id)
    {
        $product = Product::find($id);
        if (! $product) {
            return response()->json(['error' => 'Produit non trouvé'], 404);
        }

        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'supplier_price' => 'nullable|numeric',
            'selling_price' => 'nullable|numeric',
            'images' => 'nullable|array',
            'images.*' => 'string',
            'supplier_id' => 'nullable|string',
            'supplier_product_id' => 'nullable|string',
            'status' => 'nullable|in:active,inactive,out_of_stock',
        ]);

        if (isset($data['selling_price']) || isset($data['supplier_price'])) {
            $selling = $data['selling_price'] ?? $product->selling_price;
            $supplier = $data['supplier_price'] ?? $product->supplier_price;
            $data['margin'] = $selling - $supplier;
        }

        $product->update($data);

        return response()->json(['product' => $product]);
    }

    public function destroy(string $id)
    {
        $product = Product::find($id);
        if (! $product) {
            return response()->json(['error' => 'Produit non trouvé'], 404);
        }

        $product->delete();
        return response()->json(['message' => 'Produit supprimé']);
    }
}

