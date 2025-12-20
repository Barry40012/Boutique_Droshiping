<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\WebAuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MerchantController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Auth Web
Route::get('/login', [WebAuthController::class, 'showLogin'])->name('login');
Route::post('/login', [WebAuthController::class, 'login']);
Route::post('/logout', [WebAuthController::class, 'logout']);
Route::get('/register', [WebAuthController::class, 'showRegister']);
Route::post('/register', [WebAuthController::class, 'register']);

// Vitrine (Blade + React UMD pour le design)
Route::get('/', [ShopController::class, 'home']);
Route::get('/products', [ShopController::class, 'products']);
Route::get('/products/{id}', [ShopController::class, 'productShow'])->name('products.show');
Route::post('/cart/add/{id}', [ShopController::class, 'addToCart'])->name('cart.add');
Route::get('/cart', [ShopController::class, 'cart'])->name('cart');
Route::post('/cart/update/{id}', [ShopController::class, 'updateCart'])->name('cart.update');
Route::post('/cart/remove/{id}', [ShopController::class, 'removeFromCart'])->name('cart.remove');
Route::get('/checkout', [ShopController::class, 'checkout'])->name('checkout');
Route::post('/checkout', [ShopController::class, 'checkoutSubmit'])->name('checkout.submit');
Route::get('/checkout/success', [ShopController::class, 'checkoutSuccess'])->name('checkout.success');

// Webhook paiement (appelé par Korapay/DPO)
Route::post('/webhooks/payment', [ShopController::class, 'paymentWebhook'])->name('webhooks.payment');

// Boutiques publiques des merchants
Route::get('/store/{slug}', [ShopController::class, 'storePublic'])->name('store.public');
Route::get('/store/{slug}/products/{id}', [ShopController::class, 'storeProductShow'])->name('store.product.show');

// Route publique pour changer la langue d'une boutique
Route::post('/store/{slug}/language', [ShopController::class, 'updateLanguage'])->name('store.language.update');

// Suivi de commande (public)
Route::get('/track-order', [ShopController::class, 'showTrackOrder'])->name('track.order');
Route::post('/track-order', [ShopController::class, 'trackOrder'])->name('track.order.submit');

// Avis produits (public)
Route::post('/store/{slug}/products/{id}/review', [ShopController::class, 'submitReview'])->name('product.review.submit');

// Admin (protégé) - Administrateur de la plateforme uniquement
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/products', [AdminController::class, 'products'])->name('products');
    Route::get('/orders', [AdminController::class, 'orders'])->name('orders');
    Route::get('/suppliers', [AdminController::class, 'suppliers'])->name('suppliers');
});

// Merchant Dashboard (protégé) - Pour les utilisateurs avec abonnement
Route::middleware('auth')->prefix('merchant')->name('merchant.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [MerchantController::class, 'dashboard'])->name('dashboard');
    Route::get('/subscription', [MerchantController::class, 'subscription'])->name('subscription');
    Route::get('/subscription/required', [MerchantController::class, 'subscriptionRequired'])->name('subscription.required');
    
    // Création de boutique
    Route::get('/store/questionnaire', [MerchantController::class, 'showQuestionnaire'])->name('store.questionnaire');
    Route::get('/store/create', [MerchantController::class, 'createStore'])->name('store.create');
    Route::post('/store', [MerchantController::class, 'storeStore'])->name('store.store');
    
    // Nouvelles pages du dashboard
    Route::get('/sales', [MerchantController::class, 'sales'])->name('sales');
    Route::get('/revenue', [MerchantController::class, 'revenue'])->name('revenue');
    Route::get('/margin', [MerchantController::class, 'margin'])->name('margin');
    Route::get('/analytics', [MerchantController::class, 'analytics'])->name('analytics');
    Route::get('/marketing', [MerchantController::class, 'marketing'])->name('marketing');
    Route::get('/settings', [MerchantController::class, 'settings'])->name('settings');
    
    // Gestion Wallets Fournisseurs
    Route::get('/wallets', [MerchantController::class, 'wallets'])->name('wallets');
    Route::post('/wallets/{walletId}/deposit', [MerchantController::class, 'depositWallet'])->name('wallets.deposit');
    Route::get('/wallets/{walletId}/transactions', [MerchantController::class, 'walletTransactions'])->name('wallets.transactions');
    
    // Gestion Fournisseurs
    Route::get('/suppliers', [MerchantController::class, 'suppliers'])->name('suppliers');
    Route::post('/suppliers/wallet/create', [MerchantController::class, 'createWallet'])->name('suppliers.wallet.create');
    
    // Personnalisation
    Route::get('/store/customize', [MerchantController::class, 'customize'])->name('store.customize');
    Route::post('/store/customize', [MerchantController::class, 'updateCustomization'])->name('store.update');
    
    // Produits
    Route::get('/products', [MerchantController::class, 'products'])->name('products');
    Route::post('/products/upload-image', [MerchantController::class, 'uploadImage'])->name('products.upload-image');
    Route::get('/products/create', [MerchantController::class, 'createProduct'])->name('products.create');
    Route::post('/products', [MerchantController::class, 'storeProduct'])->name('products.store');
    Route::get('/products/{id}/edit', [MerchantController::class, 'editProduct'])->name('products.edit');
    Route::post('/products/{id}', [MerchantController::class, 'updateProduct'])->name('products.update');
    Route::post('/products/{id}/delete', [MerchantController::class, 'deleteProduct'])->name('products.delete');
    
    // Commandes
    Route::get('/orders', [MerchantController::class, 'orders'])->name('orders');
    Route::get('/orders/{id}', [MerchantController::class, 'showOrder'])->name('orders.show');
    
    // Paiement
    Route::get('/payment/settings', [MerchantController::class, 'paymentSettings'])->name('payment.settings');
    Route::post('/payment/settings', [MerchantController::class, 'updatePaymentSettings'])->name('payment.update');
    
    // Prévisualisation
    Route::get('/store/preview', [MerchantController::class, 'preview'])->name('store.preview');
});
