<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    public function index()
    {
        $orders = DB::table('orders')->get();
        $products = DB::table('products')->get();
        $suppliers = DB::table('suppliers')->get();

        $totalRevenue = $orders->where('payment_status', 'paid')->sum('total_amount');
        $totalMargin = $orders->where('payment_status', 'paid')->sum('margin');
        $totalOrders = $orders->count();
        $paidOrders = $orders->where('payment_status', 'paid')->count();
        $activeProducts = $products->where('status', 'active')->count();
        $totalWalletBalance = $suppliers->sum('wallet_balance');

        return response()->json([
          'revenue' => [
              'total' => $totalRevenue,
              'margin' => $totalMargin,
          ],
          'orders' => [
              'total' => $totalOrders,
              'paid' => $paidOrders,
              'pending' => $totalOrders - $paidOrders,
          ],
          'products' => [
              'total' => $products->count(),
              'active' => $activeProducts,
          ],
          'suppliers' => [
              'total' => $suppliers->count(),
              'total_wallet_balance' => $totalWalletBalance,
          ],
        ]);
    }
}

