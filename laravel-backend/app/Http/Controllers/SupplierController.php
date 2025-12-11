<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::orderByDesc('created_at')->get();
        return response()->json(['suppliers' => $suppliers]);
    }

    public function show(string $id)
    {
        $supplier = Supplier::find($id);
        if (! $supplier) {
            return response()->json(['error' => 'Fournisseur non trouvé'], 404);
        }
        return response()->json(['supplier' => $supplier]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'api_type' => 'required|string',
            'api_key' => 'nullable|string',
            'api_secret' => 'nullable|string',
            'wallet_balance' => 'nullable|numeric',
            'status' => 'nullable|in:active,inactive',
        ]);

        $data['id'] = Str::uuid()->toString();
        $data['status'] = $data['status'] ?? 'active';
        $data['wallet_balance'] = $data['wallet_balance'] ?? 0;

        $supplier = Supplier::create($data);

        return response()->json(['supplier' => $supplier], 201);
    }

    public function update(Request $request, string $id)
    {
        $supplier = Supplier::find($id);
        if (! $supplier) {
            return response()->json(['error' => 'Fournisseur non trouvé'], 404);
        }

        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'api_type' => 'sometimes|string',
            'api_key' => 'nullable|string',
            'api_secret' => 'nullable|string',
            'wallet_balance' => 'nullable|numeric',
            'status' => 'nullable|in:active,inactive',
        ]);

        $supplier->update($data);

        return response()->json(['supplier' => $supplier]);
    }
}

