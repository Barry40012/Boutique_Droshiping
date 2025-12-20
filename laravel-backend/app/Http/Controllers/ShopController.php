<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderTracking;
use App\Models\ProductReview;
use App\Models\Store;
use App\Services\AutomationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ShopController extends Controller
{
    public function home()
    {
        try {
            $products = Product::with('productImages') // ✅ Charger les images depuis product_images
                ->orderByDesc('created_at')
                ->limit(9)
                ->get();
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
        $product = Product::with('productImages')->findOrFail($id);
        $quantity = max(1, (int)($request->input('quantity', 1)));

        $cart = session()->get('cart', []);

        // Stocker seulement l'ID du produit et la quantité pour éviter les problèmes de sérialisation
        if (isset($cart[$id])) {
            $cart[$id]['quantity'] += $quantity;
        } else {
            $cart[$id] = [
                'product_id' => $product->id,
                'quantity' => $quantity,
            ];
        }

        session()->put('cart', $cart);

        return redirect()->back()->with('success', 'Produit ajouté au panier.');
    }

    public function cart()
    {
        $cart = session()->get('cart', []);
        
        // Recharger les produits avec leurs images depuis la base de données
        $cartWithProducts = [];
        $total = 0;
        
        foreach ($cart as $productId => $item) {
            // Récupérer l'ID du produit (support ancien format avec 'product' et nouveau format avec 'product_id')
            $id = $item['product_id'] ?? ($item['product']->id ?? $productId);
            
            $product = Product::with('productImages')->find($id);
            if ($product) {
                $quantity = $item['quantity'] ?? 1;
                $cartWithProducts[$productId] = [
                    'product' => $product,
                    'quantity' => $quantity,
                ];
                $total += $product->selling_price * $quantity;
            }
        }

        // Récupérer le store depuis le premier produit
        $store = null;
        if (!empty($cartWithProducts)) {
            $firstProduct = reset($cartWithProducts)['product'];
            $store = $firstProduct->store;
        }

        // Convertir les objets Product en tableaux pour JSON (pour React)
        $cartArray = [];
        foreach ($cartWithProducts as $productId => $item) {
            $productImages = [];
            if ($item['product']->relationLoaded('productImages') && $item['product']->productImages->count() > 0) {
                $productImages = $item['product']->productImages->pluck('image_url')->toArray();
            } elseif (is_array($item['product']->images)) {
                $productImages = array_values(array_filter($item['product']->images, function($img) {
                    return !empty($img) && is_string($img);
                }));
            }
            
            $cartArray[$productId] = [
                'product' => [
                    'id' => $item['product']->id,
                    'name' => $item['product']->name,
                    'description' => $item['product']->description,
                    'selling_price' => $item['product']->selling_price,
                    'images' => $productImages,
                ],
                'quantity' => $item['quantity']
            ];
        }

        return view('cart.index', compact('cartWithProducts', 'total', 'cartArray', 'store'));
    }

    public function updateCart(Request $request, string $id)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$id])) {
            $qty = max(1, (int)$request->input('quantity', 1));
            $cart[$id]['quantity'] = $qty;
            // S'assurer que product_id est présent
            if (!isset($cart[$id]['product_id'])) {
                $cart[$id]['product_id'] = $id;
            }
            session()->put('cart', $cart);
        }
        return redirect()->route('cart')->with('success', 'Quantité mise à jour.');
    }

    public function removeFromCart(string $id)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }
        return redirect()->route('cart')->with('success', 'Produit retiré du panier.');
    }

    public function checkout()
    {
        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('cart')->withErrors(['cart' => 'Votre panier est vide.']);
        }
        
        // Recharger les produits depuis la base de données
        $cartWithProducts = [];
        $total = 0;
        
        foreach ($cart as $productId => $item) {
            $id = $item['product_id'] ?? ($item['product']->id ?? $productId);
            $product = Product::with('productImages')->find($id);
            if ($product) {
                $quantity = $item['quantity'] ?? 1;
                $cartWithProducts[$productId] = [
                    'product' => $product,
                    'quantity' => $quantity,
                ];
                $total += $product->selling_price * $quantity;
            }
        }
        
        if (empty($cartWithProducts)) {
            return redirect()->route('cart')->withErrors(['cart' => 'Aucun produit valide dans votre panier.']);
        }

        // Récupérer le store depuis le premier produit
        $store = null;
        if (!empty($cartWithProducts)) {
            $firstProduct = reset($cartWithProducts)['product'];
            $store = $firstProduct->store;
        }

        return view('checkout.index', compact('cartWithProducts', 'total', 'store'));
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

        // Recharger les produits depuis la base de données pour calculer le total
        $total = 0;
        foreach ($cart as $productId => $item) {
            $id = $item['product_id'] ?? ($item['product']->id ?? $productId);
            $product = Product::find($id);
            if ($product) {
                $quantity = $item['quantity'] ?? 1;
                $total += $product->selling_price * $quantity;
            }
        }

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
            $cartProducts = [];
            
            // Recharger les produits depuis la base de données
            foreach ($cart as $productId => $item) {
                $id = $item['product_id'] ?? ($item['product']->id ?? $productId);
                $product = Product::find($id);
                if ($product) {
                    $quantity = $item['quantity'] ?? 1;
                    $cartProducts[] = ['product' => $product, 'quantity' => $quantity];
                    $supplierCost += $product->supplier_price * $quantity;
                    $margin += ($product->selling_price - $product->supplier_price) * $quantity;
                    // Récupérer le store_id du premier produit
                    if (!$storeId && $product->store_id) {
                        $storeId = $product->store_id;
                    }
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

            foreach ($cartProducts as $item) {
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

        $template = $store->template;
        $templateSlug = $template ? $template->slug : 'classic';

        // ✅ Gérer les deux modes : 1 produit vs multi-produits
        // Debug: logger pour vérifier
        \Log::info('ShopController storePublic', [
            'store_id' => $store->id,
            'store_slug' => $store->slug,
            'allow_multiple_products' => $store->allow_multiple_products,
            'allow_multiple_products_type' => gettype($store->allow_multiple_products),
            'template_slug' => $templateSlug,
        ]);
        
        if ($store->allow_multiple_products) {
            // Mode multi-produits : afficher tous les produits en grille
            $products = Product::where('store_id', $store->id)
                ->where('status', 'active')
                ->with('productImages')
                ->orderBy('created_at', 'desc')
                ->paginate(12);
            
            \Log::info('ShopController - Mode multiproduit', [
                'products_count' => $products->count(),
                'products_total' => $products->total(),
            ]);
            
            return view("store.templates.{$templateSlug}", compact('store', 'products'));
        } else {
            // Mode 1 produit : afficher le produit mis en avant (featured_product_id) ou le premier produit actif
            $product = null;
            
            // Si un produit est mis en avant, l'utiliser
            if ($store->featured_product_id) {
                $product = Product::where('store_id', $store->id)
                    ->where('id', $store->featured_product_id)
                    ->where('status', 'active')
                    ->with('productImages')
                    ->first();
            }
            
            // Si aucun produit mis en avant ou si le produit mis en avant n'est plus actif, prendre le premier produit actif
            if (!$product) {
                $product = Product::where('store_id', $store->id)
                    ->where('status', 'active')
                    ->with('productImages')
                    ->orderBy('created_at', 'desc')
                    ->first();
            }
            
            // Si aucun produit, retourner une vue vide
            if (!$product) {
                $products = collect([]);
                return view("store.templates.{$templateSlug}", compact('store', 'products'));
            }
            
            // Pour le mode 1 produit, on passe le produit unique
            $products = collect([$product]);
            return view("store.templates.{$templateSlug}", compact('store', 'products'));
        }
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

        // Récupérer le produit de cette boutique avec ses images
        // Permettre de voir même les produits inactifs (pour prévisualisation depuis le dashboard)
        $product = Product::where('store_id', $store->id)
            ->where('id', $id)
            ->with('productImages') // ✅ Charger les images depuis product_images
            ->firstOrFail();

        $template = $store->template;
        $templateSlug = $template ? $template->slug : 'classic';

        // Récupérer les avis approuvés du produit
        // Pour PostgreSQL, utiliser whereRaw pour comparer correctement le boolean
        if (config('database.default') === 'pgsql') {
            $reviews = ProductReview::where('product_id', $product->id)
                ->whereRaw('is_approved::boolean = true')
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $reviews = ProductReview::where('product_id', $product->id)
                ->where('is_approved', true)
                ->orderBy('created_at', 'desc')
                ->get();
        }
        
        // Passer les reviews à la vue
        $store->reviews = $reviews;

        // Utiliser le template de la boutique directement pour que le produit s'affiche
        // dans le même thème que la boutique principale
        // Passer le produit unique dans une collection pour compatibilité avec les templates
        return view("store.templates.{$templateSlug}", [
            'store' => $store,
            'product' => $product, // Produit unique pour les templates qui le détectent
            'products' => collect([$product]), // Collection pour compatibilité avec les templates existants
            'reviews' => $reviews, // Avis du produit
        ]);
    }

    /**
     * Afficher le formulaire de suivi de commande
     */
    public function showTrackOrder()
    {
        // Essayer de récupérer le store depuis la session du panier
        $store = null;
        $cart = session()->get('cart', []);
        if (!empty($cart)) {
            foreach ($cart as $productId => $item) {
                $id = $item['product_id'] ?? ($item['product']->id ?? $productId);
                $product = Product::with('store')->find($id);
                if ($product && $product->store) {
                    $store = $product->store;
                    break;
                }
            }
        }
        
        // Si pas de store dans le panier, essayer de récupérer depuis l'URL (si on vient d'une boutique)
        if (!$store && request()->has('store_slug')) {
            $store = Store::where('slug', request('store_slug'))->first();
        }
        
        return view('track-order', compact('store'));
    }

    /**
     * Traiter le suivi de commande
     */
    public function trackOrder(Request $request)
    {
        $request->validate([
            'tracking_number' => 'required|string',
        ]);

        $tracking = OrderTracking::where('tracking_number', $request->tracking_number)->first();

        if (!$tracking) {
            return back()->withErrors(['tracking_number' => 'Numéro de suivi introuvable.'])->withInput();
        }

        $order = $tracking->order;
        $store = $order->store ?? null;
        
        return view('track-order-result', compact('tracking', 'order', 'store'));
    }

    /**
     * Soumettre un avis sur un produit
     */
    public function submitReview(Request $request, string $slug, string $id)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $store = Store::where('slug', $slug)->firstOrFail();
        $product = Product::where('store_id', $store->id)->where('id', $id)->firstOrFail();

        $images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('reviews', 'public');
                $images[] = Storage::url($path);
            }
        }

        $review = ProductReview::create([
            'product_id' => $product->id,
            'store_id' => $store->id,
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'images' => $images,
            'is_approved' => false, // Nécessite approbation par le merchant
        ]);

        return back()->with('success', 'Votre avis a été soumis et sera publié après validation.');
    }

    /**
     * Mettre à jour la langue d'une boutique (route publique)
     */
    public function updateLanguage(Request $request, string $slug)
    {
        $store = Store::where('slug', $slug)->firstOrFail();
        
        $request->validate([
            'language' => 'required|in:fr,en',
        ]);

        $store->update(['language' => $request->language]);

        return response()->json(['success' => true, 'language' => $request->language]);
    }
}

