<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function dashboard()
    {
        $store = Auth::user()?->store;
        
        try {
            $orders = DB::table('orders')->get();
            $products = DB::table('products')->get();
            $suppliers = DB::table('suppliers')->get();
        } catch (\Exception $e) {
            // En cas d'erreur de connexion, utiliser des collections vides
            \Log::error('Erreur connexion DB: ' . $e->getMessage());
            $orders = collect([]);
            $products = collect([]);
            $suppliers = collect([]);
        }

        $stats = [
            'revenue' => [
                'total' => $orders->where('payment_status', 'paid')->sum('total_amount'),
                'margin' => $orders->where('payment_status', 'paid')->sum('margin'),
            ],
            'orders' => [
                'total' => $orders->count(),
                'paid' => $orders->where('payment_status', 'paid')->count(),
                'pending' => $orders->where('payment_status', 'pending')->count(),
            ],
            'products' => [
                'total' => $products->count(),
                'active' => $products->where('status', 'active')->count(),
            ],
            'suppliers' => [
                'total' => $suppliers->count(),
                'total_wallet_balance' => $suppliers->sum('wallet_balance'),
            ],
        ];

        return view('admin.dashboard', compact('stats', 'store'));
    }

    public function products()
    {
        $products = DB::table('products')->orderByDesc('created_at')->paginate(20);
        return view('admin.products', compact('products'));
    }

    public function orders()
    {
        $orders = DB::table('orders')->orderByDesc('created_at')->paginate(20);
        return view('admin.orders', compact('orders'));
    }

    public function suppliers()
    {
        $suppliers = DB::table('suppliers')->orderByDesc('created_at')->paginate(20);
        return view('admin.suppliers', compact('suppliers'));
    }
}

