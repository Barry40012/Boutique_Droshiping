<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\StoreTemplate;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\Product;
use App\Models\ProductImage;
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
                'name' => 'required|string|max:100',
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
        
        // Récupérer tous les produits actifs pour la sélection du produit mis en avant
        // Charger aussi les images pour la sélection des images de bannière
        $products = \App\Models\Product::where('store_id', $store->id)
            ->where('status', 'active')
            ->with('productImages') // Charger les images depuis product_images
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('merchant.store.customize', compact('store', 'templates', 'products'));
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
                'name' => 'required|string|max:100',
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
                // Bannière (Thème Vibrant)
                'settings.vibrant_banner_use_product_images' => 'nullable|boolean',
                'settings.vibrant_banner_selected_product_images' => 'array',
                'settings.vibrant_banner_selected_product_images.*' => 'nullable|string|max:255',
                'settings.vibrant_banner_custom_images' => 'array',
                'settings.vibrant_banner_custom_images.*' => 'nullable|string|max:255',
                'settings.faq' => 'array',
                'settings.faq.*.q' => 'nullable|string|max:255',
                'settings.faq.*.a' => 'nullable|string|max:500',
                // Textes de bannière
                'settings.banner_texts' => 'array',
                'settings.banner_texts.*.text' => 'nullable|string|max:255',
                'settings.banner_texts.*.effect' => 'nullable|string|max:32',
                'settings.footer_email' => 'nullable|email|max:255',
                // Boutons
                'button_text' => 'nullable|string|max:50',
                'button_animation' => 'nullable|in:none,bounce,pulse,shake,glow',
                'show_buy_button_on_card' => 'nullable|boolean',
                // Personnalisation bannière
                'banner_title_color' => 'nullable|string|max:7',
                'banner_title_animation' => 'nullable|in:none,fade,bounce,glow,slide',
                'banner_title_size' => 'nullable|string|max:20',
                'banner_text_color' => 'nullable|string|max:7',
                'banner_text_animation' => 'nullable|in:none,scroll,typewriter',
                'banner_text_font' => 'nullable|string|max:100',
                'banner_text_size' => 'nullable|string|max:20',
                'banner_text_speed' => 'nullable|integer|min:1|max:10',
                'banner_button_text' => 'nullable|string|max:50',
                'banner_button_color' => 'nullable|string|max:7',
                'banner_button_bg_color' => 'nullable|string|max:7',
                'banner_button_text_color' => 'nullable|string|max:7',
                // Personnalisation header
                'header_bg_color' => 'nullable|string|max:7',
                'header_text_color' => 'nullable|string|max:7',
                'header_name_color' => 'nullable|string|max:7',
                'header_name_font' => 'nullable|string|max:255',
                'header_nav_hover_color' => 'nullable|string|max:7',
                // Personnalisation footer
                'footer_bg_color' => 'nullable|string|max:7',
                'footer_text_color' => 'nullable|string|max:7',
                'footer_link_color' => 'nullable|string|max:7',
                'footer_title_color' => 'nullable|string|max:7',
                // Fond de page boutique
                'page_bg_color' => 'nullable|string|max:7',
                // Sections produit / avis
                'product_section_bg_color' => 'nullable|string|max:7',
                'product_section_rounded' => 'nullable|boolean',
                'reviews_section_bg_color' => 'nullable|string|max:7',
                'reviews_section_rounded' => 'nullable|boolean',
                // Mode multi-produits
                'allow_multiple_products' => 'nullable|boolean',
                'featured_product_id' => 'nullable|uuid|exists:products,id',
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
            $settings['nav_track_order'] = $request->has('settings.nav_track_order')
                ? $request->boolean('settings.nav_track_order')
                : false;
            $settings['section_about'] = $request->has('settings.section_about')
                ? $request->boolean('settings.section_about')
                : false;
            $settings['section_faq'] = $request->has('settings.section_faq')
                ? $request->boolean('settings.section_faq')
                : false;
            // Bannière (Thème Vibrant)
            $settings['vibrant_banner_use_product_images'] = $request->has('settings.vibrant_banner_use_product_images')
                ? $request->boolean('settings.vibrant_banner_use_product_images')
                : false;
            $settings['vibrant_banner_selected_product_images'] = array_values(array_filter(
                $request->input('settings.vibrant_banner_selected_product_images', []),
                function ($img) {
                    return !empty($img) && is_string($img);
                }
            ));
            $settings['vibrant_banner_custom_images'] = array_values(array_filter(
                $request->input('settings.vibrant_banner_custom_images', []),
                function ($img) {
                    return !empty($img) && is_string($img);
                }
            ));
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
            // Textes de bannière (messages défilants)
            $bannerTextInput = $request->input('settings.banner_texts', []);
            $settings['banner_texts'] = [];
            foreach ($bannerTextInput as $key => $value) {
                $text = $value['text'] ?? null;
                $effect = $value['effect'] ?? null;
                if (!empty($text)) {
                    $settings['banner_texts'][$key] = [
                        'text' => $text,
                        'effect' => $effect,
                    ];
                }
            }
            $settings['footer_email'] = $request->input('settings.footer_email', $settings['footer_email'] ?? ($store->user->email ?? null));
            $data['settings'] = $settings;

            // Boutons
            $data['button_text'] = $request->input('button_text', $store->button_text ?? 'Acheter maintenant');
            $data['button_animation'] = $request->input('button_animation', $store->button_animation ?? 'none');
            $data['button_bg_color'] = $request->input('button_bg_color', $store->button_bg_color ?? ($store->primary_color ?? '#111827'));
            $data['button_text_color'] = $request->input('button_text_color', $store->button_text_color ?? '#ffffff');
            $data['show_buy_button_on_card'] = $request->has('show_buy_button_on_card')
                ? $request->boolean('show_buy_button_on_card')
                : ($store->show_buy_button_on_card ?? true);

            // Personnalisation bannière
            $data['banner_title_color'] = $request->input('banner_title_color', $store->banner_title_color ?? '#ffffff');
            $data['banner_title_animation'] = $request->input('banner_title_animation', $store->banner_title_animation ?? 'none');
            $data['banner_title_size'] = $request->input('banner_title_size', $store->banner_title_size ?? '3rem');
            $data['banner_text_color'] = $request->input('banner_text_color', $store->banner_text_color ?? '#ffffff');
            $data['banner_text_animation'] = $request->input('banner_text_animation', $store->banner_text_animation ?? 'scroll');
            $data['banner_text_font'] = $request->input('banner_text_font', $store->banner_text_font ?? 'inherit');
            $data['banner_text_size'] = $request->input('banner_text_size', $store->banner_text_size ?? '1.25rem');
            $data['banner_text_speed'] = $request->input('banner_text_speed', $store->banner_text_speed ?? 5);
            $data['banner_button_text'] = $request->input('banner_button_text', $store->banner_button_text ?? 'Voir le produit');
            $data['banner_button_color'] = $request->input('banner_button_color', $store->banner_button_color ?? ($store->primary_color ?? '#3b82f6'));
            $data['banner_button_bg_color'] = $request->input('banner_button_bg_color', $store->banner_button_bg_color ?? ($store->primary_color ?? '#3b82f6'));
            $data['banner_button_text_color'] = $request->input('banner_button_text_color', $store->banner_button_text_color ?? '#ffffff');

            // Personnalisation header
            $data['header_bg_color'] = $request->input('header_bg_color', $store->header_bg_color ?? '#ffffff');
            $data['header_text_color'] = $request->input('header_text_color', $store->header_text_color ?? '#4b5563');
            $data['header_name_color'] = $request->input('header_name_color', $store->header_name_color ?? '#1f2937');
            $data['header_name_font'] = $request->input('header_name_font', $store->header_name_font ?? 'inherit');
            $data['header_nav_hover_color'] = $request->input('header_nav_hover_color', $store->header_nav_hover_color ?? ($store->primary_color ?? '#0ea5e9'));

            // Personnalisation footer
            $data['footer_bg_color'] = $request->input('footer_bg_color', $store->footer_bg_color ?? '#1f2937');
            $data['footer_text_color'] = $request->input('footer_text_color', $store->footer_text_color ?? '#9ca3af');
            $data['footer_link_color'] = $request->input('footer_link_color', $store->footer_link_color ?? '#ffffff');
            $data['footer_title_color'] = $request->input('footer_title_color', $store->footer_title_color ?? '#ffffff');

            // Fond de page boutique
            $data['page_bg_color'] = $request->input('page_bg_color', $store->page_bg_color ?? '#ffffff');

            // Sections produit / avis (fond + coins arrondis)
            $data['product_section_bg_color'] = $request->input('product_section_bg_color', $store->product_section_bg_color ?? '#ffffff');
            $data['product_section_rounded'] = $request->has('product_section_rounded')
                ? $request->boolean('product_section_rounded')
                : ($store->product_section_rounded ?? true);

            $data['reviews_section_bg_color'] = $request->input('reviews_section_bg_color', $store->reviews_section_bg_color ?? '#ffffff');
            $data['reviews_section_rounded'] = $request->has('reviews_section_rounded')
                ? $request->boolean('reviews_section_rounded')
                : ($store->reviews_section_rounded ?? true);

            // Langue
            $data['language'] = $request->input('language', $store->language ?? 'fr');

            // Gestion spécifique PostgreSQL pour les colonnes booléennes nouvellement ajoutées
            $booleanRawUpdates = [];
            if (config('database.default') === 'pgsql') {
                if (array_key_exists('product_section_rounded', $data)) {
                    $booleanRawUpdates['product_section_rounded'] = DB::raw(
                        $data['product_section_rounded'] ? 'true' : 'false'
                    );
                    unset($data['product_section_rounded']);
                }
                if (array_key_exists('reviews_section_rounded', $data)) {
                    $booleanRawUpdates['reviews_section_rounded'] = DB::raw(
                        $data['reviews_section_rounded'] ? 'true' : 'false'
                    );
                    unset($data['reviews_section_rounded']);
                }
            }

            // Mettre à jour tous les champs (les booléens gérés ci-dessus pour PostgreSQL)
            $store->update($data);

            // Appliquer les mises à jour booléennes spécifiques PostgreSQL si nécessaire
            if (!empty($booleanRawUpdates)) {
                DB::table('stores')
                    ->where('id', $store->id)
                    ->update($booleanRawUpdates);
            }
            
            // Mode multi-produits : mettre à jour séparément avec DB::raw() pour PostgreSQL
            $allowMultipleProducts = $request->has('allow_multiple_products') 
                ? $request->boolean('allow_multiple_products') 
                : false;
            
            if (config('database.default') === 'pgsql') {
                DB::table('stores')
                    ->where('id', $store->id)
                    ->update([
                        'allow_multiple_products' => DB::raw($allowMultipleProducts ? 'true' : 'false')
                    ]);
            } else {
                $store->update(['allow_multiple_products' => $allowMultipleProducts]);
            }
            
            // Gérer le produit mis en avant (featured_product_id) - uniquement en mode monoproduit
            if (!$allowMultipleProducts) {
                $featuredProductId = $request->input('featured_product_id');
                // Vérifier que le produit appartient bien à cette boutique
                if ($featuredProductId) {
                    $product = \App\Models\Product::where('store_id', $store->id)
                        ->where('id', $featuredProductId)
                        ->first();
                    if ($product) {
                        $store->update(['featured_product_id' => $featuredProductId]);
                    } else {
                        // Si le produit n'existe pas ou n'appartient pas à la boutique, réinitialiser
                        $store->update(['featured_product_id' => null]);
                    }
                } else {
                    $store->update(['featured_product_id' => null]);
                }
            } else {
                // En mode multi-produits, réinitialiser featured_product_id
                $store->update(['featured_product_id' => null]);
            }

        return redirect()->route('merchant.store.customize')->with('success', 'Personnalisation mise à jour !');
    }

    /**
     * Gérer les produits
     */
    public function products(Request $request)
    {
        try {
            // Vérifier la connexion à la base de données
            DB::connection()->getPdo();
            
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
        } catch (\PDOException $e) {
            \Log::error('Erreur PDO liste produits: ' . $e->getMessage());
            $errorMsg = 'Erreur de connexion à la base de données.';
            if (str_contains($e->getMessage(), 'could not translate host name') || 
                str_contains($e->getMessage(), 'Unknown host') ||
                str_contains($e->getMessage(), 'Connection refused')) {
                $errorMsg .= ' Impossible de se connecter à Supabase. Vérifiez votre connexion internet.';
            }
            return view('merchant.products.index', [
                'store' => null,
                'products' => collect([]),
                'errors' => ['error' => $errorMsg]
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('Erreur Query liste produits: ' . $e->getMessage());
            $errorMsg = 'Erreur de connexion à la base de données. Vérifiez votre connexion internet et les paramètres Supabase.';
            return view('merchant.products.index', [
                'store' => null,
                'products' => collect([]),
                'errors' => ['error' => $errorMsg]
            ]);
        } catch (\Exception $e) {
            \Log::error('Erreur liste produits: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
            return view('merchant.products.index', [
                'store' => null,
                'products' => collect([]),
                'errors' => ['error' => 'Une erreur est survenue : ' . $e->getMessage()]
            ]);
        }
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
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'supplier_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'supplier_link' => 'nullable|url|max:255',
            'supplier_product_id' => 'nullable|string|max:255',
            'supplier_store_link' => 'nullable|url|max:255',
            'banner' => 'nullable|string|max:255',
            'images' => 'nullable|string', // liste d'URL séparées par des virgules
            'status' => 'required|in:active,inactive',
            // Variantes (optionnelles)
            'variant_sizes' => 'nullable|string|max:500',
            'variant_colors' => 'nullable|string|max:500',
            'variant_lengths' => 'nullable|string|max:500',
            // Promotion (optionnelle)
            'has_promotion' => 'nullable|boolean',
            'promo_percentage' => 'nullable|numeric|min:1|max:99',
            'promo_price' => 'nullable|numeric|min:0', // Calculé automatiquement depuis le pourcentage
            'promo_duration_days' => 'nullable|integer|min:1|max:365',
        ];

        // Valider les fichiers seulement s'ils sont présents
        if ($request->hasFile('image_files')) {
            $rules['image_files'] = 'array|max:6';
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
            // Accepter les URLs absolues ET les URLs relatives (chemins Storage)
            foreach ($urls as $url) {
                // Accepter si c'est une URL valide OU si ça commence par /storage/ ou http
                if (filter_var($url, FILTER_VALIDATE_URL) || 
                    strpos($url, '/storage/') === 0 || 
                    strpos($url, 'http://') === 0 || 
                    strpos($url, 'https://') === 0 ||
                    strpos($url, 'storage/') !== false) {
                    // Si c'est un chemin relatif, le convertir en URL absolue
                    if (strpos($url, '/storage/') === 0 || strpos($url, 'storage/') !== false) {
                        $url = asset($url);
                    }
                    $images[] = $url;
                }
            }
        }
        
        // Vérifier que le total ne dépasse pas 4 images
        if (count($images) > 6) {
            return back()
                ->withInput()
                ->withErrors(['images' => 'Vous ne pouvez pas ajouter plus de 6 images au total (fichiers uploadés + URLs).']);
        }
        
        // Limiter à 4 images (sécurité supplémentaire)
        $images = array_slice($images, 0, 6);

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

            // Construire l'objet variants
            $variants = [];
            if (!empty($data['variant_sizes'])) {
                $sizes = array_map('trim', explode(',', $data['variant_sizes']));
                $variants['sizes'] = array_values(array_filter($sizes));
            }
            if (!empty($data['variant_colors'])) {
                $colors = array_map('trim', explode(',', $data['variant_colors']));
                $variants['colors'] = array_values(array_filter($colors));
            }
            if (!empty($data['variant_lengths'])) {
                $lengths = array_map('trim', explode(',', $data['variant_lengths']));
                $variants['lengths'] = array_values(array_filter($lengths));
            }

            // Gérer la promotion
            $hasPromotion = $request->has('has_promotion') && $request->boolean('has_promotion');
            $promoPrice = null;
            $promoStartDate = null;
            $promoEndDate = null;

            if ($hasPromotion) {
                // Calculer le prix promo à partir du pourcentage ou utiliser le prix direct
                if (!empty($data['promo_percentage']) && $data['promo_percentage'] > 0 && $data['promo_percentage'] <= 99) {
                    $discount = ($data['selling_price'] * $data['promo_percentage']) / 100;
                    $promoPrice = $data['selling_price'] - $discount;
                } elseif (!empty($data['promo_price'])) {
                    $promoPrice = $data['promo_price'];
                }
                
                if ($promoPrice && $promoPrice > 0) {
                    $promoStartDate = now();
                    $promoDurationDays = $data['promo_duration_days'] ?? 7;
                    $promoEndDate = now()->addDays($promoDurationDays);
                }
            }

            // ✅ APPROCHE PRO: Utiliser la table product_images au lieu de la colonne images
            if (config('database.default') === 'pgsql') {
                // Créer le produit sans images (les images seront dans la table product_images)
                $product = Product::create([
                    'id' => $id,
                    'store_id' => $store->id,
                    'name' => $data['name'],
                    'description' => $data['description'] ?? null,
                    'supplier_price' => $data['supplier_price'],
                    'selling_price' => $data['selling_price'],
                    // 'margin' est une colonne générée (GENERATED ALWAYS AS), ne pas l'insérer
                    'banner' => $data['banner'] ?? null,
                    'supplier_link' => $data['supplier_link'] ?? null,
                    'supplier_product_id' => $data['supplier_product_id'] ?? null,
                    'supplier_store_link' => $data['supplier_store_link'] ?? null,
                    'status' => $data['status'],
                    'variants' => !empty($variants) ? $variants : null,
                    'promo_price' => $promoPrice,
                    'promo_start_date' => $promoStartDate,
                    'promo_end_date' => $promoEndDate,
                ]);
                
                // Mettre à jour has_promotion séparément avec DB::raw() pour PostgreSQL
                DB::table('products')
                    ->where('id', $product->id)
                    ->update([
                        'has_promotion' => DB::raw($hasPromotion ? 'true' : 'false')
                    ]);
                
                // ✅ Sauvegarder les images dans la table product_images
                $sortOrder = 0;
                foreach ($images as $imageUrl) {
                    // Extraire le chemin depuis l'URL
                    $imagePath = str_replace([asset(''), url('')], '', $imageUrl);
                    $imagePath = ltrim($imagePath, '/');
                    
                    // Si c'est une URL complète, extraire le chemin relatif
                    if (strpos($imagePath, 'storage/') === 0) {
                        $imagePath = str_replace('storage/', '', $imagePath);
                    }
                    
                    ProductImage::create([
                        'id' => (string) Str::uuid(),
                        'product_id' => $product->id,
                        'image_path' => $imagePath,
                        'image_url' => $imageUrl,
                        'sort_order' => $sortOrder++,
                    ]);
                }
                
                \Log::info('Product Creation - Images saved in product_images table', [
                    'product_id' => $product->id,
                    'images_count' => count($images)
                ]);
            } else {
                // Pour les autres bases de données, utiliser Eloquent normalement
                $product = Product::create([
                    'id' => $id,
                    'store_id' => $store->id,
                    'name' => $data['name'],
                    'description' => $data['description'] ?? null,
                    'supplier_price' => $data['supplier_price'],
                    'selling_price' => $data['selling_price'],
                    'banner' => $data['banner'] ?? null,
                    'supplier_link' => $data['supplier_link'] ?? null,
                    'supplier_product_id' => $data['supplier_product_id'] ?? null,
                    'supplier_store_link' => $data['supplier_store_link'] ?? null,
                    'status' => $data['status'],
                    'variants' => !empty($variants) ? $variants : null,
                    'has_promotion' => $hasPromotion,
                    'promo_price' => $promoPrice,
                    'promo_start_date' => $promoStartDate,
                    'promo_end_date' => $promoEndDate,
                ]);
                
                // ✅ Sauvegarder les images dans la table product_images
                $sortOrder = 0;
                foreach ($images as $imageUrl) {
                    // Extraire le chemin depuis l'URL
                    $imagePath = str_replace([asset(''), url('')], '', $imageUrl);
                    $imagePath = ltrim($imagePath, '/');
                    
                    // Si c'est une URL complète, extraire le chemin relatif
                    if (strpos($imagePath, 'storage/') === 0) {
                        $imagePath = str_replace('storage/', '', $imagePath);
                    }
                    
                    ProductImage::create([
                        'id' => (string) Str::uuid(),
                        'product_id' => $product->id,
                        'image_path' => $imagePath,
                        'image_url' => $imageUrl,
                        'sort_order' => $sortOrder++,
                    ]);
                }
            }

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
        try {
            // Vérifier la connexion à la base de données
            DB::connection()->getPdo();
            
            $user = Auth::user();
            $store = $user->store;
            if (!$store) {
                return redirect()->route('merchant.store.create');
            }

            $product = Product::where('store_id', $store->id)->findOrFail($id);
            
            // ✅ Charger les images depuis la table product_images
            $product->load('productImages');
            
            // ✅ MIGRATION AUTOMATIQUE: Si le produit a des images dans l'ancienne colonne mais pas assez dans product_images
            // Vérifier aussi si product_images contient moins d'images que l'ancienne colonne
            $rawImages = $product->getAttributes()['images'] ?? null;
            $legacyImagesCount = 0;
            if ($rawImages && $rawImages !== '{}' && is_string($rawImages)) {
                $content = trim($rawImages, '{}');
                if (!empty($content)) {
                    $items = explode(',', $content);
                    $legacyImagesCount = count(array_filter($items, function($item) {
                        return !empty(trim($item));
                    }));
                }
            }
            
            \Log::info('Product Edit - Migration check', [
                'product_id' => $product->id,
                'productImages_count' => $product->productImages->count(),
                'legacy_images_count' => $legacyImagesCount,
                'needs_migration' => $legacyImagesCount > $product->productImages->count()
            ]);
            
            if ($product->productImages->count() === 0 || ($legacyImagesCount > 0 && $legacyImagesCount > $product->productImages->count())) {
                // Essayer de récupérer depuis l'ancienne colonne images (PostgreSQL TEXT[])
                $rawImages = $product->getAttributes()['images'] ?? null;
                if ($rawImages && $rawImages !== '{}' && is_string($rawImages)) {
                    // Parser le format PostgreSQL TEXT[]
                    $content = trim($rawImages, '{}');
                    if (!empty($content)) {
                        $items = explode(',', $content);
                        $legacyImages = array_map(function($item) {
                            $item = trim($item);
                            $item = trim($item, '"');
                            $item = str_replace('\\"', '"', $item);
                            $item = str_replace('\\\\', '\\', $item);
                            return $item;
                        }, $items);
                        $legacyImages = array_values(array_filter($legacyImages, function($img) {
                            return !empty($img) && is_string($img);
                        }));
                        
                        // Migrer vers product_images
                        if (!empty($legacyImages)) {
                            $sortOrder = 0;
                            foreach ($legacyImages as $imageUrl) {
                                // Extraire le chemin depuis l'URL
                                $imagePath = str_replace([asset(''), url('')], '', $imageUrl);
                                $imagePath = ltrim($imagePath, '/');
                                
                                // Si c'est une URL complète, extraire le chemin relatif
                                if (strpos($imagePath, 'storage/') === 0) {
                                    $imagePath = str_replace('storage/', '', $imagePath);
                                }
                                
                                ProductImage::create([
                                    'id' => (string) Str::uuid(),
                                    'product_id' => $product->id,
                                    'image_path' => $imagePath,
                                    'image_url' => $imageUrl,
                                    'sort_order' => $sortOrder++,
                                ]);
                            }
                            
                            // Recharger la relation
                            $product->load('productImages');
                            
                            \Log::info('Product Edit - Images migrées depuis colonne images vers product_images', [
                                'product_id' => $product->id,
                                'migrated_count' => count($legacyImages)
                            ]);
                        }
                    }
                }
            }
            
            \Log::info('Product Edit - Images from product_images table (CONTROLLER)', [
                'product_id' => $product->id,
                'images_count' => $product->productImages->count(),
                'images' => $product->productImages->pluck('image_url')->toArray(),
                'productImages_raw' => $product->productImages->map(function($img) {
                    return [
                        'id' => $img->id,
                        'image_url' => $img->image_url,
                        'image_path' => $img->image_path,
                        'sort_order' => $img->sort_order
                    ];
                })->toArray()
            ]);
            
            return view('merchant.products.edit', compact('store', 'product'));
        } catch (\PDOException $e) {
            \Log::error('Erreur PDO édition produit: ' . $e->getMessage());
            $errorMsg = 'Erreur de connexion à la base de données.';
            if (str_contains($e->getMessage(), 'could not translate host name') || 
                str_contains($e->getMessage(), 'Unknown host') ||
                str_contains($e->getMessage(), 'Connection refused')) {
                $errorMsg .= ' Impossible de se connecter à Supabase. Vérifiez votre connexion internet.';
            }
            return redirect()->route('merchant.products')
                ->withErrors(['error' => $errorMsg]);
        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('Erreur Query édition produit: ' . $e->getMessage());
            $errorMsg = 'Erreur de connexion à la base de données. Vérifiez votre connexion internet et les paramètres Supabase.';
            return redirect()->route('merchant.products')
                ->withErrors(['error' => $errorMsg]);
        } catch (\Exception $e) {
            \Log::error('Erreur édition produit: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
            return redirect()->route('merchant.products')
                ->withErrors(['error' => 'Une erreur est survenue lors du chargement du produit: ' . $e->getMessage()]);
        }
    }

    /**
     * Mettre à jour un produit
     */
    public function updateProduct(Request $request, string $id)
    {
        try {
            // Vérifier la connexion à la base de données
            DB::connection()->getPdo();
            
            $user = Auth::user();
            $store = $user->store;
            if (!$store) {
                return redirect()->route('merchant.store.create');
            }

            $product = Product::where('store_id', $store->id)->findOrFail($id);
        } catch (\PDOException $e) {
            \Log::error('Erreur PDO récupération produit (update): ' . $e->getMessage());
            $errorMsg = 'Erreur de connexion à la base de données.';
            if (str_contains($e->getMessage(), 'could not translate host name') || 
                str_contains($e->getMessage(), 'Unknown host') ||
                str_contains($e->getMessage(), 'Connection refused')) {
                $errorMsg .= ' Impossible de se connecter à Supabase. Vérifiez votre connexion internet.';
            }
            return back()
                ->withInput()
                ->withErrors(['error' => $errorMsg]);
        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('Erreur Query récupération produit (update): ' . $e->getMessage());
            $errorMsg = 'Erreur de connexion à la base de données. Vérifiez votre connexion internet et les paramètres Supabase.';
            return back()
                ->withInput()
                ->withErrors(['error' => $errorMsg]);
        } catch (\Exception $e) {
            \Log::error('Erreur récupération produit (update): ' . $e->getMessage());
            return back()
                ->withInput()
                ->withErrors(['error' => 'Une erreur est survenue lors du chargement du produit: ' . $e->getMessage()]);
        }

        // Si on arrive ici, les variables sont définies
        try {
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
                'name' => 'required|string|max:100',
                'description' => 'nullable|string',
                'supplier_price' => 'required|numeric|min:0',
                'selling_price' => 'required|numeric|min:0',
                'supplier_link' => 'nullable|url|max:255',
                'supplier_product_id' => 'nullable|string|max:255',
                'supplier_store_link' => 'nullable|url|max:255',
                'banner' => 'nullable|string|max:255',
                'images' => 'nullable|string',
                'status' => 'required|in:active,inactive',
                // Variantes (optionnelles)
                'variant_sizes' => 'nullable|string|max:500',
                'variant_colors' => 'nullable|string|max:500',
                'variant_lengths' => 'nullable|string|max:500',
                // Promotion (optionnelle)
                'has_promotion' => 'nullable|boolean',
                'promo_percentage' => 'nullable|numeric|min:1|max:99',
                'promo_price' => 'nullable|numeric|min:0', // Calculé automatiquement depuis le pourcentage
                'promo_duration_days' => 'nullable|integer|min:1|max:365',
            ];

            // Valider les fichiers seulement s'ils sont présents
            if ($request->hasFile('image_files')) {
                $rules['image_files'] = 'array|max:6';
                $rules['image_files.*'] = 'image|mimes:jpeg,png,jpg,gif,webp|max:5120';
            }

            $data = $request->validate($rules);
            
            // Debug: logger ce qui est reçu AVANT validation
            \Log::info('Product Update - Form data received (BEFORE validation)', [
                'images_input_raw' => $request->input('images'),
                'images_input_raw_type' => gettype($request->input('images')),
                'images_input_raw_length' => is_string($request->input('images')) ? strlen($request->input('images')) : 0,
                'has_image_files' => $request->hasFile('image_files'),
                'image_files_count' => $request->hasFile('image_files') ? count($request->file('image_files')) : 0,
                'all_input_keys' => array_keys($request->all()),
            ]);
            
            \Log::info('Product Update - Form data received (AFTER validation)', [
                'images_input' => $data['images'] ?? 'NOT SET',
                'has_image_files' => $request->hasFile('image_files'),
                'image_files_count' => $request->hasFile('image_files') ? count($request->file('image_files')) : 0,
            ]);

            // ✅ Récupérer les images existantes depuis la table product_images
            $existingImagesFromDb = $product->productImages()
                ->orderBy('sort_order', 'asc')
                ->pluck('image_url')
                ->toArray();
            
            \Log::info('Product Update - Existing images from product_images table', [
                'count' => count($existingImagesFromDb),
                'images' => $existingImagesFromDb
            ]);
            
            // ✅ SOLUTION PRO: Fusionner images existantes + nouvelles images uploadées
            $images = [];
            $uploadedFilesCount = 0;
            
            // ÉTAPE 1: Récupérer les images existantes depuis le champ hidden
            // Ce champ contient les images qui étaient déjà enregistrées en base
            $existingImagesFromForm = [];
            if ($request->has('existing_images')) {
                $existingImagesJson = $request->input('existing_images');
                if (!empty($existingImagesJson)) {
                    $decoded = json_decode($existingImagesJson, true);
                    if (is_array($decoded)) {
                        // ✅ DÉDUPLIQUER : Supprimer les doublons
                        $existingImagesFromForm = array_values(array_unique(array_filter($decoded, function($img) {
                            return !empty($img) && is_string($img);
                        })));
                    }
                }
            }
            
            // Récupérer les images actuellement en DB pour comparer (pour suppression physique)
            $currentImagesInDb = $product->productImages()
                ->orderBy('sort_order', 'asc')
                ->get();
            
            \Log::info('Product Update - Existing images from form (deduplicated)', [
                'count' => count($existingImagesFromForm),
                'images' => $existingImagesFromForm
            ]);
            
            // ÉTAPE 2: Ajouter les images existantes au tableau final
            foreach ($existingImagesFromForm as $existingUrl) {
                // Normaliser l'URL (s'assurer qu'elle est absolue)
                $url = trim($existingUrl);
                if (!empty($url)) {
                    if (strpos($url, '/storage/') === 0) {
                        $url = asset($url);
                    } elseif (strpos($url, 'storage/') !== false && strpos($url, 'http') === false) {
                        $url = asset('/' . ltrim($url, '/'));
                    }
                    $images[] = $url;
                }
            }
            
            // ÉTAPE 2.5: Ajouter les NOUVELLES URLs du textarea (qui ne sont pas dans existing_images)
            // Le textarea peut contenir des nouvelles URLs ajoutées par l'utilisateur
            if (!empty($data['images'])) {
                $urlsFromTextarea = array_values(array_filter(array_map('trim', explode(',', $data['images']))));
                foreach ($urlsFromTextarea as $url) {
                    $url = trim($url);
                    if (!empty($url)) {
                        // Normaliser l'URL
                        if (strpos($url, '/storage/') === 0) {
                            $url = asset($url);
                        } elseif (strpos($url, 'storage/') !== false && strpos($url, 'http') === false) {
                            $url = asset('/' . ltrim($url, '/'));
                        }
                        // Ajouter seulement si ce n'est pas déjà dans les images existantes
                        if (!in_array($url, $images)) {
                            $images[] = $url;
                        }
                    }
                }
            }
            
            // ÉTAPE 3: Ajouter les NOUVELLES images uploadées (fichiers)
            $imagesBeforeUpload = $images;
            if ($request->hasFile('image_files')) {
                $files = $request->file('image_files');
                if (!is_array($files)) {
                    $files = [$files];
                }
                
                \Log::info('Product Update - Processing uploaded files', [
                    'files_count' => count($files),
                    'images_before_upload' => count($imagesBeforeUpload)
                ]);
                
                foreach ($files as $file) {
                    if ($file && $file->isValid()) {
                        $path = $file->store('products/' . $store->id, 'public');
                        $url = Storage::url($path);
                        if (!filter_var($url, FILTER_VALIDATE_URL)) {
                            $url = asset($url);
                        }
                        
                        \Log::info('Product Update - File uploaded', [
                            'file_name' => $file->getClientOriginalName(),
                            'path' => $path,
                            'url' => $url,
                            'images_count_before_add' => count($images),
                            'url_already_in_array' => in_array($url, $images)
                        ]);
                        
                        // Vérifier si l'URL n'est pas déjà dans le tableau (sécurité)
                        if (!in_array($url, $images)) {
                            $images[] = $url; // Ajouter à la fin (après les existantes)
                            $uploadedFilesCount++;
                            \Log::info('Product Update - File added to images array', [
                                'url' => $url,
                                'new_count' => count($images)
                            ]);
                        } else {
                            \Log::warning('Product Update - Uploaded file URL already exists in array', [
                                'url' => $url
                            ]);
                        }
                    } else {
                        \Log::warning('Product Update - Invalid file', [
                            'file' => $file ? $file->getClientOriginalName() : 'null',
                            'is_valid' => $file ? $file->isValid() : false
                        ]);
                    }
                }
            }
            
            // Log final pour debug
            \Log::info('Product Update - Images collected (FUSION)', [
                'existing_images_count' => count($existingImagesFromForm),
                'new_files_uploaded' => $uploadedFilesCount,
                'total_images' => count($images),
                'images_before_upload' => count($imagesBeforeUpload),
                'images_after_upload' => count($images),
                'images' => $images
            ]);
            
            // ✅ FUSION FINALE: $images contient déjà les images existantes + nouvelles images uploadées
            // NE JAMAIS écraser $images à ce stade !
            
            // Si aucune image n'a été fournie (ni existantes ni nouvelles), garder les images de la DB
            if (empty($images) && !empty($existingImagesFromDb)) {
                $images = $existingImagesFromDb;
                \Log::info('Product Update - No images in form, keeping DB images', [
                    'count' => count($images)
                ]);
            }
            
            // ✅ DÉDUPLIQUER : Supprimer les doublons AVANT de limiter
            $imagesBeforeDedup = $images;
            $images = array_values(array_unique($images));
            
            // Log pour voir si la déduplication supprime quelque chose
            \Log::info('Product Update - Images deduplication', [
                'product_id' => $product->id,
                'before_dedup_count' => count($imagesBeforeDedup),
                'after_dedup_count' => count($images),
                'duplicates_removed' => count($imagesBeforeDedup) - count($images),
                'images_before' => $imagesBeforeDedup,
                'images_after' => $images
            ]);
            
            // Sécurité: limiter à 6 images maximum (après déduplication)
            $imagesBeforeSlice = $images;
            $images = array_slice($images, 0, 6);
            
            // Log si on a dû tronquer
            if (count($imagesBeforeSlice) > 6) {
                \Log::warning('Product Update - Images truncated to 6', [
                    'product_id' => $product->id,
                    'before_slice_count' => count($imagesBeforeSlice),
                    'after_slice_count' => count($images),
                    'truncated_images' => array_slice($imagesBeforeSlice, 6)
                ]);
            }
            
            // Debug: logger les images avant sauvegarde (FUSION FINALE)
            \Log::info('Product Update - Images before save (FINAL FUSION)', [
                'product_id' => $product->id,
                'images_count' => count($images),
                'images' => $images,
                'existing_from_form' => count($existingImagesFromForm),
                'new_files_uploaded' => $uploadedFilesCount,
                'total_after_fusion' => count($images),
                'images_before_dedup' => count($imagesBeforeDedup),
                'images_after_dedup' => count($imagesBeforeSlice),
                'images_final' => count($images)
            ]);

            // Construire l'objet variants
            $variants = [];
            if (!empty($data['variant_sizes'])) {
                $sizes = array_map('trim', explode(',', $data['variant_sizes']));
                $variants['sizes'] = array_values(array_filter($sizes));
            }
            if (!empty($data['variant_colors'])) {
                $colors = array_map('trim', explode(',', $data['variant_colors']));
                $variants['colors'] = array_values(array_filter($colors));
            }
            if (!empty($data['variant_lengths'])) {
                $lengths = array_map('trim', explode(',', $data['variant_lengths']));
                $variants['lengths'] = array_values(array_filter($lengths));
            }

            // Gérer la promotion - forcer en boolean strict pour PostgreSQL
            $hasPromotion = $request->has('has_promotion') ? (bool) $request->boolean('has_promotion') : false;
            $promoPrice = null;
            $promoStartDate = null;
            $promoEndDate = null;

            if ($hasPromotion) {
                // Calculer le prix promo à partir du pourcentage ou utiliser le prix direct
                if (!empty($data['promo_percentage']) && $data['promo_percentage'] > 0 && $data['promo_percentage'] <= 99) {
                    $discount = ($data['selling_price'] * $data['promo_percentage']) / 100;
                    $promoPrice = $data['selling_price'] - $discount;
                } elseif (!empty($data['promo_price'])) {
                    $promoPrice = $data['promo_price'];
                }
                
                if ($promoPrice && $promoPrice > 0) {
                    // Si la promotion existait déjà et n'est pas expirée, garder la date de début
                    if ($product->has_promotion && $product->promo_start_date && $product->promo_end_date && $product->promo_end_date->isFuture()) {
                        $promoStartDate = $product->promo_start_date;
                        $promoDurationDays = $data['promo_duration_days'] ?? 7;
                        $promoEndDate = $promoStartDate->copy()->addDays($promoDurationDays);
                    } else {
                        $promoStartDate = now();
                        $promoDurationDays = $data['promo_duration_days'] ?? 7;
                        $promoEndDate = now()->addDays($promoDurationDays);
                    }
                }
            }

            // ✅ APPROCHE PRO: Utiliser la table product_images au lieu de la colonne images
            if (config('database.default') === 'pgsql') {
                // Mettre à jour tous les champs sauf has_promotion avec Eloquent
                $product->update([
                    'name' => $data['name'],
                    'description' => $data['description'] ?? null,
                    'supplier_price' => $data['supplier_price'],
                    'selling_price' => $data['selling_price'],
                    // 'margin' est une colonne générée (GENERATED ALWAYS AS), ne pas la mettre à jour
                    'banner' => $data['banner'] ?? $product->banner,
                    'supplier_link' => $data['supplier_link'] ?? $product->supplier_link,
                    'supplier_product_id' => $data['supplier_product_id'] ?? $product->supplier_product_id,
                    'supplier_store_link' => $data['supplier_store_link'] ?? $product->supplier_store_link,
                    'status' => $data['status'],
                    'variants' => !empty($variants) ? $variants : null,
                    'promo_price' => $promoPrice,
                    'promo_start_date' => $promoStartDate,
                    'promo_end_date' => $promoEndDate,
                ]);
                
                // Mettre à jour has_promotion séparément avec DB::raw() pour PostgreSQL
                DB::table('products')
                    ->where('id', $product->id)
                    ->update([
                        'has_promotion' => DB::raw($hasPromotion ? 'true' : 'false')
                    ]);
                
                // ✅ GESTION COMPLÈTE DES IMAGES : Suppression physique + Sauvegarde
                // 1. Identifier les images à supprimer (celles qui étaient en DB mais plus dans le formulaire)
                $imagesToKeep = array_map(function($url) {
                    // Normaliser pour comparaison
                    $url = trim($url);
                    if (strpos($url, '/storage/') === 0) {
                        $url = asset($url);
                    } elseif (strpos($url, 'storage/') !== false && strpos($url, 'http') === false) {
                        $url = asset('/' . ltrim($url, '/'));
                    }
                    return $url;
                }, $images);
                
                $imagesToDelete = [];
                foreach ($currentImagesInDb as $dbImage) {
                    $dbUrl = trim($dbImage->image_url);
                    // Normaliser pour comparaison
                    if (strpos($dbUrl, '/storage/') === 0) {
                        $dbUrl = asset($dbUrl);
                    } elseif (strpos($dbUrl, 'storage/') !== false && strpos($dbUrl, 'http') === false) {
                        $dbUrl = asset('/' . ltrim($dbUrl, '/'));
                    }
                    
                    if (!in_array($dbUrl, $imagesToKeep)) {
                        $imagesToDelete[] = $dbImage->image_url;
                    }
                }
                
                // 2. Supprimer physiquement les fichiers des images supprimées
                if (!empty($imagesToDelete)) {
                    $this->deleteImageFiles($imagesToDelete);
                    \Log::info('Product Update - Image files deleted', [
                        'product_id' => $product->id,
                        'deleted_count' => count($imagesToDelete),
                        'deleted_urls' => $imagesToDelete
                    ]);
                }
                
                // 3. Supprimer toutes les images de la table (on va les recréer)
                ProductImage::where('product_id', $product->id)->delete();
                
                // 4. Log AVANT sauvegarde pour voir exactement ce qui va être sauvegardé
                \Log::info('Product Update - About to save images (PostgreSQL)', [
                    'product_id' => $product->id,
                    'images_to_save_count' => count($images),
                    'images_to_save' => $images
                ]);
                
                // 5. Sauvegarder les nouvelles images (déjà dédupliquées et limitées à 6 dans $images)
                // NE PAS redédupliquer ici car $images est déjà traité (lignes 1202 et 1205)
                $savedImages = $this->saveProductImages($product->id, $images);
                
                // 6. Vérification finale
                $finalCount = ProductImage::where('product_id', $product->id)->count();
                $finalImages = ProductImage::where('product_id', $product->id)
                    ->orderBy('sort_order')
                    ->pluck('image_url')
                    ->toArray();
                
                \Log::info('Product Update - Images management complete (PostgreSQL)', [
                    'product_id' => $product->id,
                    'images_to_save_count' => count($images),
                    'saved_images_count' => $finalCount,
                    'saved_images_urls' => $savedImages,
                    'final_images_in_db' => $finalImages,
                    'files_deleted_count' => count($imagesToDelete),
                    'images_array' => $images
                ]);
                
                if ($finalCount !== count($images)) {
                    \Log::error('Product Update - Images count mismatch! (PostgreSQL)', [
                        'expected' => count($images),
                        'saved' => $finalCount,
                        'expected_images' => $images,
                        'saved_images' => $savedImages,
                        'final_images_in_db' => $finalImages
                    ]);
                }
            } else {
                // Pour les autres bases de données, utiliser Eloquent normalement
                $product->update([
                    'name' => $data['name'],
                    'description' => $data['description'] ?? null,
                    'supplier_price' => $data['supplier_price'],
                    'selling_price' => $data['selling_price'],
                    'banner' => $data['banner'] ?? $product->banner,
                    'supplier_link' => $data['supplier_link'] ?? $product->supplier_link,
                    'supplier_product_id' => $data['supplier_product_id'] ?? $product->supplier_product_id,
                    'supplier_store_link' => $data['supplier_store_link'] ?? $product->supplier_store_link,
                    'status' => $data['status'],
                    'variants' => !empty($variants) ? $variants : null,
                    'has_promotion' => $hasPromotion,
                    'promo_price' => $promoPrice,
                    'promo_start_date' => $promoStartDate,
                    'promo_end_date' => $promoEndDate,
                ]);
                
                // ✅ GESTION COMPLÈTE DES IMAGES : Suppression physique + Sauvegarde (else branch)
                // 1. Identifier les images à supprimer
                $imagesToKeep = array_map(function($url) {
                    $url = trim($url);
                    if (strpos($url, '/storage/') === 0) {
                        $url = asset($url);
                    } elseif (strpos($url, 'storage/') !== false && strpos($url, 'http') === false) {
                        $url = asset('/' . ltrim($url, '/'));
                    }
                    return $url;
                }, $images);
                
                $imagesToDelete = [];
                foreach ($currentImagesInDb as $dbImage) {
                    $dbUrl = trim($dbImage->image_url);
                    if (strpos($dbUrl, '/storage/') === 0) {
                        $dbUrl = asset($dbUrl);
                    } elseif (strpos($dbUrl, 'storage/') !== false && strpos($dbUrl, 'http') === false) {
                        $dbUrl = asset('/' . ltrim($dbUrl, '/'));
                    }
                    
                    if (!in_array($dbUrl, $imagesToKeep)) {
                        $imagesToDelete[] = $dbImage->image_url;
                    }
                }
                
                // 2. Supprimer physiquement les fichiers
                if (!empty($imagesToDelete)) {
                    $this->deleteImageFiles($imagesToDelete);
                }
                
                // 3. Supprimer toutes les images de la table
                ProductImage::where('product_id', $product->id)->delete();
                
                // 4. Sauvegarder les nouvelles images
                $uniqueImages = array_values(array_unique($images));
                $savedImages = $this->saveProductImages($product->id, $uniqueImages);
                
                // 5. Vérification finale
                $finalCount = ProductImage::where('product_id', $product->id)->count();
                
                \Log::info('Product Update - Images management complete (else branch)', [
                    'product_id' => $product->id,
                    'images_to_save_count' => count($images),
                    'unique_images_count' => count($uniqueImages),
                    'saved_images_count' => $finalCount,
                    'files_deleted_count' => count($imagesToDelete),
                    'duplicates_removed' => count($images) - count($uniqueImages)
                ]);
            }

            // ✅ Retourner une réponse avec le nombre total d'images
            $finalImageCount = ProductImage::where('product_id', $product->id)->count();
            
            return redirect()->route('merchant.products')
                ->with('success', "Produit mis à jour avec succès. {$finalImageCount} image(s) associée(s).");
        } catch (\PDOException $e) {
            \Log::error('Erreur PDO mise à jour produit: ' . $e->getMessage());
            $errorMsg = 'Erreur de connexion à la base de données.';
            if (str_contains($e->getMessage(), 'could not translate host name') || 
                str_contains($e->getMessage(), 'Unknown host') ||
                str_contains($e->getMessage(), 'Connection refused')) {
                $errorMsg .= ' Impossible de se connecter à Supabase. Vérifiez votre connexion internet.';
            } elseif (str_contains($e->getMessage(), 'invalid input syntax for type uuid')) {
                $errorMsg .= ' Erreur de format UUID. Veuillez réessayer.';
            }
            return back()
                ->withInput()
                ->withErrors(['error' => $errorMsg]);
        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('Erreur Query mise à jour produit: ' . $e->getMessage());
            $errorMsg = 'Erreur de connexion à la base de données. Vérifiez votre connexion internet et les paramètres Supabase.';
            if (str_contains($e->getMessage(), 'could not translate host name') || 
                str_contains($e->getMessage(), 'Unknown host') ||
                str_contains($e->getMessage(), 'Connection refused')) {
                $errorMsg = 'Erreur de connexion à la base de données. Impossible de se connecter à Supabase. Vérifiez votre connexion internet.';
            }
            return back()
                ->withInput()
                ->withErrors(['error' => $errorMsg]);
        } catch (\Exception $e) {
            \Log::error('Erreur mise à jour produit: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
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
     * Page de gestion des abonnements
     */
    public function subscription()
    {
        $user = Auth::user();
        $store = $user->store;
        
        // Récupérer tous les plans disponibles
        $plans = SubscriptionPlan::where('active', true)
            ->orderBy('price', 'asc')
            ->get();
        
        // Récupérer l'abonnement actif de l'utilisateur
        $activeSubscription = $user->activeSubscription();
        $currentPlan = $user->currentPlan();

        return view('merchant.subscription.index', compact('store', 'plans', 'activeSubscription', 'currentPlan'));
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
     * ✅ FONCTION HELPER : Calculer le total d'images (existantes + nouvelles)
     * Déduplique automatiquement et respecte la limite de 6
     */
    private function calculateTotalImages(array $existingImages, array $newUrls, array $uploadedFiles): array
    {
        $allImages = [];
        
        // 1. Ajouter les images existantes
        foreach ($existingImages as $url) {
            $url = trim($url);
            if (!empty($url)) {
                // Normaliser l'URL
                if (strpos($url, '/storage/') === 0) {
                    $url = asset($url);
                } elseif (strpos($url, 'storage/') !== false && strpos($url, 'http') === false) {
                    $url = asset('/' . ltrim($url, '/'));
                }
                $allImages[] = $url;
            }
        }
        
        // 2. Ajouter les nouvelles URLs (qui ne sont pas déjà dans les existantes)
        foreach ($newUrls as $url) {
            $url = trim($url);
            if (!empty($url)) {
                // Normaliser l'URL
                if (strpos($url, '/storage/') === 0) {
                    $url = asset($url);
                } elseif (strpos($url, 'storage/') !== false && strpos($url, 'http') === false) {
                    $url = asset('/' . ltrim($url, '/'));
                }
                // Ajouter seulement si ce n'est pas déjà présent
                if (!in_array($url, $allImages)) {
                    $allImages[] = $url;
                }
            }
        }
        
        // 3. Ajouter les fichiers uploadés (URLs générées)
        foreach ($uploadedFiles as $url) {
            $url = trim($url);
            if (!empty($url) && !in_array($url, $allImages)) {
                $allImages[] = $url;
            }
        }
        
        // 4. Dédupliquer
        $uniqueImages = array_values(array_unique($allImages));
        
        // 5. Limiter à 6
        $finalImages = array_slice($uniqueImages, 0, 6);
        
        return [
            'images' => $finalImages,
            'total' => count($finalImages),
            'duplicates_removed' => count($uniqueImages) - count($finalImages),
            'exceeded_limit' => count($uniqueImages) > 6
        ];
    }
    
    /**
     * ✅ FONCTION HELPER : Supprimer physiquement les fichiers d'images
     */
    private function deleteImageFiles(array $imageUrls): void
    {
        foreach ($imageUrls as $url) {
            try {
                // Extraire le chemin depuis l'URL
                $path = str_replace([asset(''), url(''), 'http://127.0.0.1:8000/', 'http://localhost:8000/'], '', $url);
                $path = ltrim($path, '/');
                
                // Si c'est un chemin storage, le convertir en chemin relatif
                if (strpos($path, 'storage/') === 0) {
                    $path = str_replace('storage/', '', $path);
                }
                
                // Supprimer le fichier s'il existe
                if (!empty($path) && Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                    \Log::info('Image file deleted', ['path' => $path]);
                }
            } catch (\Exception $e) {
                \Log::error('Error deleting image file', [
                    'url' => $url,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }
    
    /**
     * ✅ FONCTION HELPER : Sauvegarder les images dans product_images
     */
    private function saveProductImages(string $productId, array $imageUrls): array
    {
        $savedImages = [];
        $sortOrder = 0;
        
        foreach ($imageUrls as $imageUrl) {
            $imageUrl = trim($imageUrl);
            if (empty($imageUrl)) {
                continue;
            }
            
            // Extraire le chemin depuis l'URL
            $imagePath = str_replace([asset(''), url(''), 'http://127.0.0.1:8000/', 'http://localhost:8000/'], '', $imageUrl);
            $imagePath = ltrim($imagePath, '/');
            
            if (strpos($imagePath, 'storage/') === 0) {
                $imagePath = str_replace('storage/', '', $imagePath);
            }
            
            if (empty($imagePath)) {
                $imagePath = $imageUrl;
            }
            
            try {
                $productImage = ProductImage::create([
                    'id' => (string) Str::uuid(),
                    'product_id' => $productId,
                    'image_path' => $imagePath,
                    'image_url' => $imageUrl,
                    'sort_order' => $sortOrder++,
                ]);
                $savedImages[] = $productImage->image_url;
            } catch (\Exception $e) {
                \Log::error('Error saving product image', [
                    'product_id' => $productId,
                    'image_url' => $imageUrl,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        return $savedImages;
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
        
        // S'assurer que l'URL est absolue
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            $url = asset($url);
        }

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
            ->with('productImages') // ✅ Charger les images depuis product_images
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
