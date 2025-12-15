<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\StoreTemplate;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MerchantController extends Controller
{
    // Middleware pour vérifier l'abonnement actif
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Dashboard principal du merchant
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // Vérifier si l'utilisateur a une boutique
        $store = $user->store;
        $currentPlan = $user->currentPlan();
        
        // Si pas de boutique, rediriger vers le questionnaire
        if (!$store) {
            return redirect()->route('merchant.store.questionnaire');
        }

        // TODO: Réactiver la vérification d'abonnement après développement
        // Vérifier l'abonnement actif
        // if (!$user->hasActiveSubscription()) {
        //     return redirect()->route('merchant.subscription.required');
        // }

        // Statistiques (seulement si une boutique existe)
        try {
            if ($store) {
                $stats = [
                    'products' => [
                        'total' => Product::where('store_id', $store->id)->count(),
                        'active' => Product::where('store_id', $store->id)->where('status', 'active')->count(),
                    ],
                    'orders' => [
                        'total' => Order::where('store_id', $store->id)->count(),
                        'pending' => Order::where('store_id', $store->id)->where('status', 'pending')->count(),
                        'paid' => Order::where('store_id', $store->id)->where('payment_status', 'paid')->count(),
                    ],
                    'revenue' => [
                        'total' => Order::where('store_id', $store->id)->where('payment_status', 'paid')->sum('total_amount') ?? 0,
                        'margin' => Order::where('store_id', $store->id)->where('payment_status', 'paid')->sum('margin') ?? 0,
                    ],
                ];

                // Commandes récentes
                $recentOrders = Order::where('store_id', $store->id)
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();
            } else {
                $stats = [
                    'products' => ['total' => 0, 'active' => 0],
                    'orders' => ['total' => 0, 'pending' => 0, 'paid' => 0],
                    'revenue' => ['total' => 0, 'margin' => 0],
                ];
                $recentOrders = collect([]);
            }
        } catch (\Exception $e) {
            // En cas d'erreur de connexion, utiliser des valeurs par défaut
            \Log::error('Erreur connexion DB: ' . $e->getMessage());
            $stats = [
                'products' => ['total' => 0, 'active' => 0],
                'orders' => ['total' => 0, 'pending' => 0, 'paid' => 0],
                'revenue' => ['total' => 0, 'margin' => 0],
            ];
            $recentOrders = collect([]);
        }

        return view('merchant.dashboard', compact('store', 'stats', 'recentOrders', 'currentPlan'));
    }

    /**
     * Afficher le questionnaire préalable ou la création de boutique
     */
    public function createStore()
    {
        $user = Auth::user();
        
        // Vérifier si l'utilisateur a déjà une boutique
        if ($user->store) {
            // Vérifier les restrictions du plan Free
            $currentPlan = $user->currentPlan();
            // Vérifier que $currentPlan est un objet avant d'accéder à ->slug
            if ($currentPlan && is_object($currentPlan) && isset($currentPlan->slug) && $currentPlan->slug === 'free') {
                return redirect()->route('merchant.dashboard')
                    ->with('info', 'Vous êtes sur le plan Free. Vous ne pouvez créer qu\'une seule boutique. Passez au plan Pro pour créer plus de boutiques.');
            }
        }

        // Récupérer les templates disponibles
        if (config('database.default') === 'pgsql') {
            $templates = StoreTemplate::whereRaw('is_active::boolean = true')
                ->orderBy('sort_order')
                ->get();
        } else {
            $templates = StoreTemplate::active()->orderBy('sort_order')->get();
        }

        return view('merchant.store.create', compact('templates'));
    }

    /**
     * Afficher le questionnaire préalable
     */
    public function showQuestionnaire()
    {
        $user = Auth::user();
        
        // Si l'utilisateur a déjà une boutique, rediriger
        if ($user->store) {
            return redirect()->route('merchant.dashboard');
        }

        return view('merchant.store.questionnaire');
    }

    /**
     * Enregistrer la boutique
     */
    public function storeStore(Request $request)
    {
        $user = Auth::user();

        // TODO: Pour les tests, on permet de créer plusieurs boutiques
        // Plus tard, on restreindra cela aux utilisateurs avec abonnement payant
        // if ($user->store && !$user->hasActiveSubscription()) {
        //     return redirect()->route('merchant.dashboard')->with('error', 'Vous avez déjà une boutique. Abonnez-vous pour en créer une autre.');
        // }

        try {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'category' => 'required|string|max:255',
                'description' => 'required|string',
                'banner' => 'required|string|max:255',
                'experience_level' => 'required|in:beginner,intermediate,professional',
                'country' => 'required|string|max:255',
                'currency' => 'required|string|max:10',
                'template_id' => 'nullable|exists:store_templates,id',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Si c'est une requête AJAX, retourner JSON
            if ($request->wantsJson() || $request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'errors' => $e->errors()
                ], 422);
            }
            
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        }

        // Si aucun template_id n'est fourni, essayer d'utiliser le premier template disponible
        // Mais ne PAS bloquer la création si aucun thème n'est disponible
        // Le thème sera assigné plus tard lors de la personnalisation
        if (empty($data['template_id'])) {
            if (config('database.default') === 'pgsql') {
                $defaultTemplate = StoreTemplate::whereRaw('is_active::boolean = true')
                    ->orderBy('sort_order')
                    ->first();
            } else {
                $defaultTemplate = StoreTemplate::active()->orderBy('sort_order')->first();
            }
            
            if ($defaultTemplate) {
                $data['template_id'] = $defaultTemplate->id;
                \Log::info('Thème par défaut assigné:', ['template_id' => $defaultTemplate->id]);
            } else {
                // Pas de thème disponible - on continue quand même, le thème sera assigné plus tard
                \Log::warning('Aucun thème disponible, création de boutique sans thème (sera assigné plus tard)');
                $data['template_id'] = null; // Permettre null temporairement
            }
        }

        // Vérifier les restrictions du plan Free
        $currentPlan = $user->currentPlan();
        // Vérifier que $currentPlan est un objet avant d'accéder à ->slug
        if ($currentPlan && is_object($currentPlan) && isset($currentPlan->slug) && $currentPlan->slug === 'free') {
            // Vérifier si l'utilisateur a déjà une boutique
            if ($user->store) {
                return redirect()->back()->withErrors(['store' => 'Vous êtes sur le plan Free. Vous ne pouvez créer qu\'une seule boutique.']);
            }
        }

        // Convertir l'ID de bannière en URL de bannière
        $bannerUrl = $data['banner'];

        try {
            // S'assurer que template_id est null si vide (pour PostgreSQL)
            $templateId = !empty($data['template_id']) ? $data['template_id'] : null;
            
            // Créer le store SANS is_active (sera ajouté après avec DB::raw pour PostgreSQL)
            // Cela évite que Laravel convertisse true en 1
            $store = new Store([
                'user_id' => $user->id,
                'template_id' => $templateId,
                'name' => $data['name'],
                'slug' => Str::slug($data['name']) . '-' . Str::random(6),
                'description' => $data['description'],
                'category' => $data['category'],
                'experience_level' => $data['experience_level'],
                'country' => $data['country'],
                'currency' => $data['currency'],
                'banner' => $bannerUrl,
            ]);
            
            // Sauvegarder sans is_active
            $store->save();
            
            // Maintenant mettre à jour is_active avec DB::raw pour PostgreSQL
            // Cela force PostgreSQL à recevoir un boolean et non un integer
            if (config('database.default') === 'pgsql') {
                DB::table('stores')
                    ->where('id', $store->id)
                    ->update(['is_active' => DB::raw('true')]);
            } else {
                // Pour MySQL, on peut utiliser directement
                $store->is_active = true;
                $store->save();
            }
            
            // Recharger le modèle pour avoir la bonne valeur
            $store->refresh();

            // Créer automatiquement un abonnement Free si l'utilisateur n'en a pas
            if (!$user->activeSubscription()) {
                // Récupérer le plan Free avec gestion PostgreSQL
                if (config('database.default') === 'pgsql') {
                    $freePlan = SubscriptionPlan::whereRaw('slug = ? AND is_active::boolean = true', ['free'])->first();
                } else {
                    $freePlan = SubscriptionPlan::where('slug', 'free')->where('is_active', true)->first();
                }
                
                if ($freePlan) {
                    Subscription::create([
                        'user_id' => $user->id,
                        'store_id' => $store->id,
                        'plan_id' => $freePlan->id,
                        'plan' => 'monthly',
                        'amount' => 0,
                        'status' => 'active',
                        'starts_at' => now(),
                        'ends_at' => now()->addDays($freePlan->trial_days),
                    ]);
                }
            }

            // TOUJOURS rediriger vers le dashboard, peu importe le type de requête
            // Laravel gérera la redirection automatiquement
            $redirectUrl = route('merchant.dashboard');
            
            \Log::info('========== STORE CREATED SUCCESSFULLY ==========');
            \Log::info('Store ID:', ['store_id' => $store->id]);
            \Log::info('User ID:', ['user_id' => $user->id]);
            \Log::info('Redirect URL:', ['url' => $redirectUrl]);
            \Log::info('About to redirect...');
            
            // Rediriger avec succès
            $response = redirect()->route('merchant.dashboard')
                ->with('success', 'Boutique créée avec succès ! Bienvenue sur votre dashboard.');
            
            \Log::info('Redirect response created', [
                'status_code' => $response->getStatusCode(),
                'headers' => $response->headers->all()
            ]);
            
            return $response;
                
        } catch (\Exception $e) {
            \Log::error('========== ERREUR LORS DE LA CRÉATION ==========');
            \Log::error('Message:', ['message' => $e->getMessage()]);
            \Log::error('File:', ['file' => $e->getFile()]);
            \Log::error('Line:', ['line' => $e->getLine()]);
            \Log::error('Stack trace:', ['trace' => $e->getTraceAsString()]);
            
            return redirect()->back()
                ->withErrors(['error' => 'Une erreur est survenue: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Personnaliser la boutique
     */
    public function customize()
    {
        $user = Auth::user();
        $store = $user->store;

        if (!$store) {
            return redirect()->route('merchant.store.create');
        }

        // Utiliser whereRaw pour forcer le type boolean avec PostgreSQL
        if (config('database.default') === 'pgsql') {
            $templates = StoreTemplate::whereRaw('is_active::boolean = true')
                ->orderBy('sort_order')
                ->get();
        } else {
            $templates = StoreTemplate::active()->orderBy('sort_order')->get();
        }
        return view('merchant.store.customize', compact('store', 'templates'));
    }

    /**
     * Mettre à jour la personnalisation
     */
    public function updateCustomization(Request $request)
    {
        $user = Auth::user();
        $store = $user->store;

        if (!$store) {
            return redirect()->route('merchant.store.create');
        }

            $data = $request->validate([
                'name' => 'required|string|max:255',
                'logo' => 'nullable|url',
                'banner' => 'nullable|string|max:255',
                'primary_color' => 'nullable|string|max:7',
                'secondary_color' => 'nullable|string|max:7',
                'accent_color' => 'nullable|string|max:7',
                'description' => 'nullable|string',
                'template_id' => 'nullable|exists:store_templates,id',
                // Réseaux sociaux
                'facebook_url' => 'nullable|url|max:255',
                'instagram_url' => 'nullable|url|max:255',
                'twitter_url' => 'nullable|url|max:255',
                'linkedin_url' => 'nullable|url|max:255',
                'youtube_url' => 'nullable|url|max:255',
                // Typographie
                'heading_font' => 'nullable|string|max:255',
                'body_font' => 'nullable|string|max:255',
                // Settings navigation / sections / FAQ / footer
                'settings' => 'array',
                'settings.nav_home' => 'nullable|boolean',
                'settings.nav_product' => 'nullable|boolean',
                'settings.nav_about' => 'nullable|boolean',
                'settings.nav_faq' => 'nullable|boolean',
                'settings.nav_cart' => 'nullable|boolean',
                'settings.section_about' => 'nullable|boolean',
                'settings.section_faq' => 'nullable|boolean',
                'settings.faq' => 'array',
                'settings.faq.*.q' => 'nullable|string|max:255',
                'settings.faq.*.a' => 'nullable|string|max:500',
                'settings.footer_email' => 'nullable|email|max:255',
                // Boutons
                'button_text' => 'nullable|string|max:50',
                'button_animation' => 'nullable|in:none,bounce,pulse,shake,glow',
                'show_buy_button_on_card' => 'nullable|boolean',
            ]);

            // Construire settings consolidé
            $settings = $store->settings ?? [];
            // Si la case est absente, on considère false (permet de décocher)
            $settings['nav_home'] = $request->has('settings.nav_home')
                ? $request->boolean('settings.nav_home')
                : false;
            $settings['nav_product'] = $request->has('settings.nav_product')
                ? $request->boolean('settings.nav_product')
                : false;
            $settings['nav_about'] = $request->has('settings.nav_about')
                ? $request->boolean('settings.nav_about')
                : false;
            $settings['nav_faq'] = $request->has('settings.nav_faq')
                ? $request->boolean('settings.nav_faq')
                : false;
            $settings['nav_cart'] = $request->has('settings.nav_cart')
                ? $request->boolean('settings.nav_cart')
                : false;
            $settings['section_about'] = $request->has('settings.section_about')
                ? $request->boolean('settings.section_about')
                : false;
            $settings['section_faq'] = $request->has('settings.section_faq')
                ? $request->boolean('settings.section_faq')
                : false;
            $faqInput = $request->input('settings.faq', []);
            $defaults = [
                1 => [
                    'q' => 'Quels sont les modes de paiement acceptés ?',
                    'a' => 'Carte bancaire, mobile money et autres méthodes sécurisées.',
                ],
                2 => [
                    'q' => 'Quels sont les délais de livraison ?',
                    'a' => 'Selon votre localisation, généralement 7 à 21 jours ouvrés.',
                ],
                3 => [
                    'q' => 'Puis-je retourner un produit ?',
                    'a' => 'Oui, 14 jours pour retourner un produit non utilisé.',
                ],
            ];
            $settings['faq'] = [];
            foreach ([1,2,3] as $i) {
                $settings['faq'][$i] = [
                    'q' => $faqInput[$i]['q'] ?? ($settings['faq'][$i]['q'] ?? $defaults[$i]['q']),
                    'a' => $faqInput[$i]['a'] ?? ($settings['faq'][$i]['a'] ?? $defaults[$i]['a']),
                ];
            }
            $settings['footer_email'] = $request->input('settings.footer_email', $settings['footer_email'] ?? ($store->user->email ?? null));
            $data['settings'] = $settings;

            // Boutons
            $data['button_text'] = $request->input('button_text', $store->button_text ?? 'Acheter maintenant');
            $data['button_animation'] = $request->input('button_animation', $store->button_animation ?? 'none');
            $data['show_buy_button_on_card'] = $request->has('show_buy_button_on_card')
                ? $request->boolean('show_buy_button_on_card')
                : ($store->show_buy_button_on_card ?? true);

            $store->update($data);

        return redirect()->route('merchant.store.customize')->with('success', 'Personnalisation mise à jour !');
    }

    /**
     * Gérer les produits
     */
    public function products(Request $request)
    {
        $user = Auth::user();
        $store = $user->store;

        if (!$store) {
            return redirect()->route('merchant.store.create');
        }

        $query = Product::where('store_id', $store->id);

        // Filtre par statut
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Recherche par nom ou description
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('description', 'ilike', "%{$search}%");
            });
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(20);
        $products->appends($request->query());

        return view('merchant.products.index', compact('store', 'products'));
    }

    /**
     * Formulaire création produit
     */
    public function createProduct()
    {
        $user = Auth::user();
        $store = $user->store;
        if (!$store) {
            return redirect()->route('merchant.store.create');
        }
        return view('merchant.products.create', compact('store'));
    }

    /**
     * Enregistrer un produit
     */
    public function storeProduct(Request $request)
    {
        $user = Auth::user();
        $store = $user->store;
        if (!$store) {
            return redirect()->route('merchant.store.create');
        }

        // Nettoyer/rogner les URLs fournisseur pour éviter les erreurs de longueur (AliExpress très long)
        $cleanSupplierLink = $request->input('supplier_link');
        if (!empty($cleanSupplierLink)) {
            $cleanSupplierLink = Str::before($cleanSupplierLink, '?');
            $cleanSupplierLink = substr($cleanSupplierLink, 0, 255);
            $request->merge(['supplier_link' => $cleanSupplierLink]);
        }
        $cleanStoreLink = $request->input('supplier_store_link');
        if (!empty($cleanStoreLink)) {
            $cleanStoreLink = Str::before($cleanStoreLink, '?');
            $cleanStoreLink = substr($cleanStoreLink, 0, 255);
            $request->merge(['supplier_store_link' => $cleanStoreLink]);
        }

        // Validation personnalisée pour les fichiers images
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'supplier_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'supplier_link' => 'nullable|url|max:255',
            'supplier_product_id' => 'nullable|string|max:255',
            'supplier_store_link' => 'nullable|url|max:255',
            'banner' => 'nullable|string|max:255',
            'images' => 'nullable|string', // liste d'URL séparées par des virgules
            'status' => 'required|in:active,inactive',
        ];

        // Valider les fichiers seulement s'ils sont présents
        if ($request->hasFile('image_files')) {
            $rules['image_files'] = 'array|max:4';
            $rules['image_files.*'] = 'image|mimes:jpeg,png,jpg,gif,webp|max:5120'; // 5MB max
        }

        $data = $request->validate($rules);

        $images = [];
        
        // Debug: vérifier ce qui est reçu
        \Log::info('Product creation - Request data', [
            'has_files' => $request->hasFile('image_files'),
            'files_count' => $request->hasFile('image_files') ? count($request->file('image_files')) : 0,
            'images_input' => $data['images'] ?? 'N/A',
            'all_input' => $request->all()
        ]);
        
        // Gérer les fichiers uploadés
        if ($request->hasFile('image_files')) {
            foreach ($request->file('image_files') as $file) {
                if ($file->isValid()) {
                    $path = $file->store('products/' . $store->id, 'public');
                    // Générer une URL absolue
                    $url = Storage::url($path);
                    // S'assurer que c'est une URL absolue
                    if (!filter_var($url, FILTER_VALIDATE_URL)) {
                        $url = asset($url);
                    }
                    $images[] = $url;
                }
            }
        }
        
        // Ajouter les URLs si fournies
        if (!empty($data['images'])) {
            $urls = array_values(array_filter(array_map('trim', explode(',', $data['images']))));
            // Valider que ce sont bien des URLs valides
            foreach ($urls as $url) {
                if (filter_var($url, FILTER_VALIDATE_URL)) {
                    $images[] = $url;
                }
            }
        }
        
        // Vérifier que le total ne dépasse pas 4 images
        if (count($images) > 4) {
            return back()
                ->withInput()
                ->withErrors(['images' => 'Vous ne pouvez pas ajouter plus de 4 images au total (fichiers uploadés + URLs).']);
        }
        
        // Limiter à 4 images (sécurité supplémentaire)
        $images = array_slice($images, 0, 4);

        try {
            // Vérifier que la connexion DB fonctionne
            DB::connection()->getPdo();
            
            // Vérifier que le store_id est valide
            if (!$store->id) {
                return back()
                    ->withInput()
                    ->withErrors(['error' => 'Erreur : Boutique introuvable. Veuillez rafraîchir la page.']);
            }

            $id = (string) Str::uuid();

            Product::create([
                'id' => $id,
                'store_id' => $store->id,
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'supplier_price' => $data['supplier_price'],
                'selling_price' => $data['selling_price'],
                // 'margin' est une colonne générée (GENERATED ALWAYS AS), ne pas l'insérer
                'images' => $images,
                'banner' => $data['banner'] ?? null,
                'supplier_link' => $data['supplier_link'] ?? null,
                'supplier_product_id' => $data['supplier_product_id'] ?? null,
                'supplier_store_link' => $data['supplier_store_link'] ?? null,
                'status' => $data['status'],
            ]);

            return redirect()->route('merchant.products')->with('success', 'Produit créé avec succès.');
        } catch (\PDOException $e) {
            \Log::error('Erreur PDO création produit: ' . $e->getMessage());
            $errorMsg = 'Erreur de connexion à la base de données.';
            if (str_contains($e->getMessage(), 'could not translate host name')) {
                $errorMsg .= ' Impossible de se connecter à Supabase. Vérifiez votre connexion internet.';
            } elseif (str_contains($e->getMessage(), 'invalid input syntax for type uuid')) {
                $errorMsg .= ' Erreur de format UUID. Veuillez réessayer.';
            }
            return back()
                ->withInput()
                ->withErrors(['error' => $errorMsg]);
        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('Erreur Query création produit: ' . $e->getMessage());
            $errorMsg = 'Erreur lors de la création du produit.';
            if (str_contains($e->getMessage(), 'could not translate host name')) {
                $errorMsg = 'Erreur de connexion à la base de données. Vérifiez votre connexion internet et les paramètres Supabase.';
            }
            return back()
                ->withInput()
                ->withErrors(['error' => $errorMsg]);
        } catch (\Exception $e) {
            \Log::error('Erreur création produit: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
            return back()
                ->withInput()
                ->withErrors(['error' => 'Une erreur est survenue : ' . $e->getMessage()]);
        }
    }

    /**
     * Formulaire édition produit
     */
    public function editProduct(string $id)
    {
        $user = Auth::user();
        $store = $user->store;
        if (!$store) {
            return redirect()->route('merchant.store.create');
        }

        $product = Product::where('store_id', $store->id)->findOrFail($id);
        return view('merchant.products.edit', compact('store', 'product'));
    }

    /**
     * Mettre à jour un produit
     */
    public function updateProduct(Request $request, string $id)
    {
        $user = Auth::user();
        $store = $user->store;
        if (!$store) {
            return redirect()->route('merchant.store.create');
        }

        $product = Product::where('store_id', $store->id)->findOrFail($id);

        // Nettoyer/rogner les URLs fournisseur pour éviter les erreurs de longueur
        $cleanSupplierLink = $request->input('supplier_link');
        if (!empty($cleanSupplierLink)) {
            $cleanSupplierLink = Str::before($cleanSupplierLink, '?');
            $cleanSupplierLink = substr($cleanSupplierLink, 0, 255);
            $request->merge(['supplier_link' => $cleanSupplierLink]);
        }
        $cleanStoreLink = $request->input('supplier_store_link');
        if (!empty($cleanStoreLink)) {
            $cleanStoreLink = Str::before($cleanStoreLink, '?');
            $cleanStoreLink = substr($cleanStoreLink, 0, 255);
            $request->merge(['supplier_store_link' => $cleanStoreLink]);
        }

        // Validation personnalisée pour les fichiers images
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'supplier_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'supplier_link' => 'nullable|url|max:255',
            'supplier_product_id' => 'nullable|string|max:255',
            'supplier_store_link' => 'nullable|url|max:255',
            'banner' => 'nullable|string|max:255',
            'images' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ];

        // Valider les fichiers seulement s'ils sont présents
        if ($request->hasFile('image_files')) {
            $rules['image_files'] = 'array|max:4';
            $rules['image_files.*'] = 'image|mimes:jpeg,png,jpg,gif,webp|max:5120';
        }

        $data = $request->validate($rules);

        // Récupérer les images existantes (celles qui ne sont pas supprimées)
        $existingImages = $product->images ?? [];
        $images = [];
        
        // Gérer les nouveaux fichiers uploadés
        if ($request->hasFile('image_files')) {
            foreach ($request->file('image_files') as $file) {
                if ($file->isValid()) {
                    $path = $file->store('products/' . $store->id, 'public');
                    // Générer une URL absolue
                    $url = Storage::url($path);
                    // S'assurer que c'est une URL absolue
                    if (!filter_var($url, FILTER_VALIDATE_URL)) {
                        $url = asset($url);
                    }
                    $images[] = $url;
                }
            }
        }
        
        // Ajouter les URLs si fournies
        if (!empty($data['images'])) {
            $urls = array_values(array_filter(array_map('trim', explode(',', $data['images']))));
            // Valider que ce sont bien des URLs valides
            foreach ($urls as $url) {
                if (filter_var($url, FILTER_VALIDATE_URL)) {
                    $images[] = $url;
                }
            }
        }
        
        // Vérifier que le total ne dépasse pas 4 images
        if (count($images) > 4) {
            return back()
                ->withInput()
                ->withErrors(['images' => 'Vous ne pouvez pas ajouter plus de 4 images au total (fichiers uploadés + URLs).']);
        }
        
        // Limiter à 4 images (sécurité supplémentaire)
        $images = array_slice($images, 0, 4);

        try {
            $product->update([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'supplier_price' => $data['supplier_price'],
                'selling_price' => $data['selling_price'],
                // 'margin' est une colonne générée (GENERATED ALWAYS AS), ne pas la mettre à jour
                'images' => $images,
                'banner' => $data['banner'] ?? $product->banner,
                'supplier_link' => $data['supplier_link'] ?? $product->supplier_link,
                'supplier_product_id' => $data['supplier_product_id'] ?? $product->supplier_product_id,
                'supplier_store_link' => $data['supplier_store_link'] ?? $product->supplier_store_link,
                'status' => $data['status'],
            ]);

            return redirect()->route('merchant.products')->with('success', 'Produit mis à jour.');
        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('Erreur mise à jour produit: ' . $e->getMessage());
            return back()
                ->withInput()
                ->withErrors(['error' => 'Erreur de connexion à la base de données. Vérifiez votre connexion internet et les paramètres Supabase.']);
        } catch (\Exception $e) {
            \Log::error('Erreur mise à jour produit: ' . $e->getMessage());
            return back()
                ->withInput()
                ->withErrors(['error' => 'Une erreur est survenue lors de la mise à jour du produit: ' . $e->getMessage()]);
        }
    }

    /**
     * Supprimer/Désactiver un produit
     */
    public function deleteProduct(string $id)
    {
        $user = Auth::user();
        $store = $user->store;
        if (!$store) {
            return redirect()->route('merchant.store.create');
        }

        $product = Product::where('store_id', $store->id)->findOrFail($id);
        $product->delete();

        return redirect()->route('merchant.products')->with('success', 'Produit supprimé.');
    }

    /**
     * Gérer les commandes
     */
    public function orders(Request $request)
    {
        $user = Auth::user();
        $store = $user->store;

        if (!$store) {
            return redirect()->route('merchant.store.create');
        }

        $query = Order::where('store_id', $store->id)->with('items');

        // Filtre par statut
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filtre par statut de paiement
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Recherche par ID commande ou nom client
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', 'ilike', "%{$search}%")
                  ->orWhereJsonContains('shipping_address->name', $search);
            });
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20);
        $orders->appends($request->query());

        return view('merchant.orders.index', compact('store', 'orders'));
    }

    /**
     * Détail d'une commande
     */
    public function showOrder(string $id)
    {
        $user = Auth::user();
        $store = $user->store;

        if (!$store) {
            return redirect()->route('merchant.store.create');
        }

        $order = Order::where('store_id', $store->id)
            ->with(['items.product'])
            ->findOrFail($id);

        return view('merchant.orders.show', compact('store', 'order'));
    }

    /**
     * Configuration du paiement
     */
    public function paymentSettings()
    {
        $user = Auth::user();
        $store = $user->store;

        if (!$store) {
            return redirect()->route('merchant.store.create');
        }

        return view('merchant.payment.settings', compact('store'));
    }

    /**
     * Mettre à jour les paramètres de paiement
     */
    public function updatePaymentSettings(Request $request)
    {
        $user = Auth::user();
        $store = $user->store;

        if (!$store) {
            return redirect()->route('merchant.store.create');
        }

        $data = $request->validate([
            'payment_gateway' => 'required|in:korapay,dpo',
            'api_key' => 'required|string',
            'api_secret' => 'required|string',
        ]);

        $settings = $store->settings ?? [];
        $settings['payment'] = [
            'gateway' => $data['payment_gateway'],
            'api_key' => encrypt($data['api_key']), // Encrypter les clés sensibles
            'api_secret' => encrypt($data['api_secret']),
        ];

        $store->update(['settings' => $settings]);

        return redirect()->route('merchant.payment.settings')->with('success', 'Paramètres de paiement mis à jour !');
    }

    /**
     * Page d'information quand l'abonnement est requis
     */
    public function subscriptionRequired()
    {
        $user = Auth::user();
        $store = $user->store;

        return view('merchant.subscription.required', compact('store'));
    }

    /**
     * Upload d'image (AJAX)
     */
    public function uploadImage(Request $request)
    {
        $user = Auth::user();
        $store = $user->store;

        if (!$store) {
            return response()->json(['error' => 'Store not found'], 404);
        }

        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $path = $request->file('image')->store('products/' . $store->id, 'public');
        $url = Storage::url($path);

        return response()->json(['url' => $url]);
    }

    /**
     * Prévisualiser la boutique
     */
    public function preview()
    {
        $user = Auth::user();
        $store = $user->store;

        if (!$store) {
            return redirect()->route('merchant.store.create');
        }

        $products = Product::where('store_id', $store->id)
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        $template = $store->template;
        $templateSlug = $template ? $template->slug : 'classic';

        return view('merchant.store.preview', compact('store', 'products', 'template'));
    }

    /**
     * Page Ventes
     */
    public function sales()
    {
        $user = Auth::user();
        $store = $user->store;

        if (!$store) {
            return redirect()->route('merchant.store.questionnaire');
        }

        // Statistiques de ventes
        $salesStats = [
            'today' => Order::where('store_id', $store->id)
                ->whereDate('created_at', today())
                ->where('payment_status', 'paid')
                ->sum('total_amount') ?? 0,
            'week' => Order::where('store_id', $store->id)
                ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->where('payment_status', 'paid')
                ->sum('total_amount') ?? 0,
            'month' => Order::where('store_id', $store->id)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->where('payment_status', 'paid')
                ->sum('total_amount') ?? 0,
        ];

        $recentSales = Order::where('store_id', $store->id)
            ->where('payment_status', 'paid')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('merchant.sales', compact('store', 'salesStats', 'recentSales'));
    }

    /**
     * Page Revenu
     */
    public function revenue()
    {
        $user = Auth::user();
        $store = $user->store;

        if (!$store) {
            return redirect()->route('merchant.store.questionnaire');
        }

        return view('merchant.revenue', compact('store'));
    }

    /**
     * Page Marge
     */
    public function margin()
    {
        $user = Auth::user();
        $store = $user->store;

        if (!$store) {
            return redirect()->route('merchant.store.questionnaire');
        }

        return view('merchant.margin', compact('store'));
    }

    /**
     * Page Analytique
     */
    public function analytics()
    {
        $user = Auth::user();
        $store = $user->store;

        if (!$store) {
            return redirect()->route('merchant.store.questionnaire');
        }

        return view('merchant.analytics', compact('store'));
    }

    /**
     * Page Marketing
     */
    public function marketing()
    {
        $user = Auth::user();
        $store = $user->store;

        if (!$store) {
            return redirect()->route('merchant.store.questionnaire');
        }

        return view('merchant.marketing', compact('store'));
    }

    /**
     * Page Paramètres
     */
    public function settings()
    {
        $user = Auth::user();
        $store = $user->store;

        if (!$store) {
            return redirect()->route('merchant.store.questionnaire');
        }

        return view('merchant.settings', compact('store'));
    }

    /**
     * Page Gestion Wallets Fournisseurs
     */
    public function wallets()
    {
        $user = Auth::user();
        $store = $user->store;

        if (!$store) {
            return redirect()->route('merchant.store.questionnaire');
        }

        // Récupérer tous les wallets de la boutique
        $wallets = \App\Models\SupplierWallet::where('store_id', $store->id)
            ->with(['supplier', 'transactions' => function($q) {
                $q->orderBy('created_at', 'desc')->limit(10);
            }])
            ->get();

        // Statistiques globales
        $stats = [
            'total_balance' => $wallets->sum('balance'),
            'total_reserved' => $wallets->sum('reserved_balance'),
            'available_balance' => $wallets->sum(function($w) { return $w->availableBalance(); }),
            'low_balance_count' => $wallets->filter(function($w) { return $w->isLowBalance(); })->count(),
        ];

        return view('merchant.wallets', compact('store', 'wallets', 'stats'));
    }

    /**
     * Dépôt dans un wallet
     */
    public function depositWallet(Request $request, $walletId)
    {
        $user = Auth::user();
        $store = $user->store;

        if (!$store) {
            return redirect()->route('merchant.store.questionnaire');
        }

        $data = $request->validate([
            'amount' => 'required|numeric|min:1',
            'description' => 'nullable|string|max:255',
        ]);

        $wallet = \App\Models\SupplierWallet::where('id', $walletId)
            ->where('store_id', $store->id)
            ->firstOrFail();

        try {
            $automationService = new \App\Services\AutomationService();
            $automationService->depositToWallet(
                $wallet,
                $data['amount'],
                $data['description'] ?? 'Recharge manuelle',
                'DEP-' . time()
            );

            return redirect()->back()->with('success', 'Dépôt effectué avec succès !');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Erreur lors du dépôt: ' . $e->getMessage()]);
        }
    }

    /**
     * Historique des transactions d'un wallet
     */
    public function walletTransactions($walletId)
    {
        $user = Auth::user();
        $store = $user->store;

        if (!$store) {
            return redirect()->route('merchant.store.questionnaire');
        }

        $wallet = \App\Models\SupplierWallet::where('id', $walletId)
            ->where('store_id', $store->id)
            ->with('supplier')
            ->firstOrFail();

        $transactions = \App\Models\SupplierTransaction::where('supplier_wallet_id', $wallet->id)
            ->with('order')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('merchant.wallet-transactions', compact('store', 'wallet', 'transactions'));
    }

    /**
     * Page Gestion Fournisseurs
     */
    public function suppliers()
    {
        $user = Auth::user();
        $store = $user->store;

        if (!$store) {
            return redirect()->route('merchant.store.questionnaire');
        }

        // Récupérer tous les fournisseurs disponibles
        $allSuppliers = \App\Models\Supplier::active()->get();

        // Récupérer les wallets existants pour cette boutique
        $wallets = \App\Models\SupplierWallet::where('store_id', $store->id)
            ->with('supplier')
            ->get()
            ->keyBy('supplier_id');

        return view('merchant.suppliers', compact('store', 'allSuppliers', 'wallets'));
    }

    /**
     * Créer un wallet pour un fournisseur
     */
    public function createWallet(Request $request)
    {
        $user = Auth::user();
        $store = $user->store;

        if (!$store) {
            return redirect()->route('merchant.store.questionnaire');
        }

        $data = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'initial_balance' => 'nullable|numeric|min:0',
        ]);

        // Vérifier si un wallet existe déjà
        $existingWallet = \App\Models\SupplierWallet::where('store_id', $store->id)
            ->where('supplier_id', $data['supplier_id'])
            ->first();

        if ($existingWallet) {
            return redirect()->back()->withErrors(['error' => 'Un wallet existe déjà pour ce fournisseur']);
        }

        try {
            $wallet = \App\Models\SupplierWallet::create([
                'store_id' => $store->id,
                'supplier_id' => $data['supplier_id'],
                'balance' => $data['initial_balance'] ?? 0,
                'reserved_balance' => 0,
                'total_deposited' => $data['initial_balance'] ?? 0,
                'total_spent' => 0,
                'low_balance_threshold' => 50,
                'is_active' => true,
            ]);

            // Si un dépôt initial est fourni, créer la transaction
            if ($data['initial_balance'] ?? 0 > 0) {
                \App\Models\SupplierTransaction::create([
                    'supplier_wallet_id' => $wallet->id,
                    'store_id' => $store->id,
                    'type' => 'deposit',
                    'amount' => $data['initial_balance'],
                    'balance_before' => 0,
                    'balance_after' => $data['initial_balance'],
                    'status' => 'completed',
                    'description' => 'Dépôt initial',
                    'reference' => 'INIT-' . time(),
                ]);
            }

            return redirect()->back()->with('success', 'Wallet créé avec succès !');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Erreur lors de la création: ' . $e->getMessage()]);
        }
    }
}
