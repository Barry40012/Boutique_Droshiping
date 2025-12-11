<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ShopController extends Controller
{
    public function home()
    {
        $products = Product::orderByDesc('created_at')->limit(9)->get();
        return view('home', compact('products'));
    }

    public function products()
    {
        $products = Product::orderByDesc('created_at')->paginate(12);
        return view('products.index', compact('products'));
    }

    public function productShow(string $id)
    {
        $product = Product::findOrFail($id);
        return view('products.show', compact('product'));
    }

    public function addToCart(Request $request, string $id)
    {
        $product = Product::findOrFail($id);
        $quantity = max(1, (int)($request->input('quantity', 1)));

        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] += $quantity;
        } else {
            $cart[$id] = [
                'product' => $product,
                'quantity' => $quantity,
            ];
        }

        session()->put('cart', $cart);

        return redirect()->back()->with('success', 'Produit ajouté au panier.');
    }

    public function cart()
    {
        $cart = session()->get('cart', []);
        $total = collect($cart)->reduce(function ($carry, $item) {
            return $carry + $item['product']->selling_price * $item['quantity'];
        }, 0);

        return view('cart.index', compact('cart', 'total'));
    }

    public function updateCart(Request $request, string $id)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$id])) {
            $qty = max(1, (int)$request->input('quantity', 1));
            $cart[$id]['quantity'] = $qty;
            session()->put('cart', $cart);
        }
        return redirect()->back();
    }

    public function removeFromCart(string $id)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }
        return redirect()->back();
    }

    public function checkout()
    {
        $cart = session()->get('cart', []);
        $total = collect($cart)->reduce(function ($carry, $item) {
            return $carry + $item['product']->selling_price * $item['quantity'];
        }, 0);

        return view('checkout.index', compact('cart', 'total'));
    }

    public function checkoutSubmit(Request $request)
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect('/cart')->withErrors(['cart' => 'Panier vide']);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'street' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'postalCode' => 'required|string|max:50',
        ]);

        $total = collect($cart)->reduce(function ($carry, $item) {
            return $carry + $item['product']->selling_price * $item['quantity'];
        }, 0);

        $shippingAddress = [
            'name' => $data['name'],
            'street' => $data['street'],
            'city' => $data['city'],
            'country' => $data['country'],
            'postalCode' => $data['postalCode'],
            'phone' => $data['phone'],
        ];

        DB::transaction(function () use ($cart, $total, $shippingAddress) {
            $orderId = Str::uuid()->toString();

            $supplierCost = 0;
            $margin = 0;
            foreach ($cart as $item) {
                $supplierCost += $item['product']->supplier_price * $item['quantity'];
                $margin += ($item['product']->selling_price - $item['product']->supplier_price) * $item['quantity'];
            }

            Order::create([
                'id' => $orderId,
                'total_amount' => $total,
                'supplier_cost' => $supplierCost,
                'margin' => $margin,
                'status' => 'pending',
                'payment_status' => 'pending',
                'shipping_address' => $shippingAddress,
            ]);

            foreach ($cart as $item) {
                OrderItem::create([
                    'id' => Str::uuid()->toString(),
                    'order_id' => $orderId,
                    'product_id' => $item['product']->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['product']->selling_price,
                    'supplier_price' => $item['product']->supplier_price,
                ]);
            }
        });

        session()->forget('cart');

        return redirect('/checkout/success');
    }

    public function checkoutSuccess()
    {
        return view('checkout.success');
    }
}

