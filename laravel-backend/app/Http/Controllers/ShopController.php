<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Store;
use App\Services\AutomationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ShopController extends Controller
{
    public function home()
    {
        try {
            $products = Product::orderByDesc('created_at')->limit(9)->get();
        } catch (\Exception $e) {
            // En cas d'erreur de connexion, retourner un tableau vide
            \Log::error('Erreur connexion DB: ' . $e->getMessage());
            $products = collect([]);
        }
        return view('home', compact('products'));
    }

    public function products()
    {
        try {
            $products = Product::orderByDesc('created_at')->paginate(12);
            $pagination = [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
                'links' => $products->linkCollection()->toArray()
            ];
        } catch (\Exception $e) {
            \Log::error('Erreur connexion DB: ' . $e->getMessage());
            $products = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 12, 1);
            $pagination = [
                'current_page' => 1,
                'last_page' => 1,
                'per_page' => 12,
                'total' => 0,
                'links' => []
            ];
        }
        return view('products.index', compact('products', 'pagination'));
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

        // Convertir les objets Product en tableaux pour JSON
        $cartArray = [];
        foreach ($cart as $productId => $item) {
            $cartArray[$productId] = [
                'product' => [
                    'id' => $item['product']->id,
                    'name' => $item['product']->name,
                    'description' => $item['product']->description,
                    'selling_price' => $item['product']->selling_price,
                    'images' => $item['product']->images ?? [],
                ],
                'quantity' => $item['quantity']
            ];
        }

        return view('cart.index', compact('cart', 'total', 'cartArray'));
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

        // Convertir les objets Product en tableaux pour JSON
        $cartArray = [];
        foreach ($cart as $productId => $item) {
            $cartArray[$productId] = [
                'product' => [
                    'id' => $item['product']->id,
                    'name' => $item['product']->name,
                    'description' => $item['product']->description,
                    'selling_price' => $item['product']->selling_price,
                    'images' => $item['product']->images ?? [],
                ],
                'quantity' => $item['quantity']
            ];
        }

        return view('checkout.index', compact('cart', 'total', 'cartArray'));
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
        ], [
            'name.required' => 'Le nom est requis.',
            'phone.required' => 'Le téléphone est requis.',
            'street.required' => 'L\'adresse est requise.',
            'city.required' => 'La ville est requise.',
            'country.required' => 'Le pays est requis.',
            'postalCode.required' => 'Le code postal est requis.',
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
            $storeId = null;
            foreach ($cart as $item) {
                $supplierCost += $item['product']->supplier_price * $item['quantity'];
                $margin += ($item['product']->selling_price - $item['product']->supplier_price) * $item['quantity'];
                // Récupérer le store_id du premier produit
                if (!$storeId && $item['product']->store_id) {
                    $storeId = $item['product']->store_id;
                }
            }

            Order::create([
                'id' => $orderId,
                'store_id' => $storeId,
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

    /**
     * Vue publique d'une boutique merchant
     */
    public function storePublic(string $slug)
    {
        // Utiliser whereRaw pour PostgreSQL boolean
        if (config('database.default') === 'pgsql') {
            $store = Store::where('slug', $slug)
                ->whereRaw('is_active::boolean = true')
                ->firstOrFail();
        } else {
            $store = Store::where('slug', $slug)
                ->where('is_active', '=', true)
                ->firstOrFail();
        }

        $products = Product::where('store_id', $store->id)
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        $template = $store->template;
        $templateSlug = $template ? $template->slug : 'classic';

        return view("store.templates.{$templateSlug}", compact('store', 'products'));
    }

    /**
     * Vue d'un produit individuel dans une boutique merchant
     */
    public function storeProductShow(string $slug, string $id)
    {
        // Récupérer la boutique
        if (config('database.default') === 'pgsql') {
            $store = Store::where('slug', $slug)
                ->whereRaw('is_active::boolean = true')
                ->firstOrFail();
        } else {
            $store = Store::where('slug', $slug)
                ->where('is_active', '=', true)
                ->firstOrFail();
        }

        // Récupérer le produit de cette boutique
        // Permettre de voir même les produits inactifs (pour prévisualisation depuis le dashboard)
        $product = Product::where('store_id', $store->id)
            ->where('id', $id)
            ->firstOrFail();

        $template = $store->template;
        $templateSlug = $template ? $template->slug : 'classic';

        // Utiliser le template de la boutique directement pour que le produit s'affiche
        // dans le même thème que la boutique principale
        // Passer le produit unique dans une collection pour compatibilité avec les templates
        return view("store.templates.{$templateSlug}", [
            'store' => $store,
            'product' => $product, // Produit unique pour les templates qui le détectent
            'products' => collect([$product]) // Collection pour compatibilité avec les templates existants
        ]);
    }
}

