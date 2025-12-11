<?php

namespace App\Http\Controllers\Inertia;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function __invoke()
    {
        $products = Product::orderByDesc('created_at')->limit(9)->get();
        return Inertia::render('Home', [
            'products' => $products,
        ]);
    }
}

