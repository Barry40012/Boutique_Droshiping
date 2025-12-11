<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\WebAuthController;
use App\Http\Controllers\AdminController;

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
Route::get('/products/{id}', [ShopController::class, 'productShow']);
Route::post('/cart/add/{id}', [ShopController::class, 'addToCart']);
Route::get('/cart', [ShopController::class, 'cart']);
Route::post('/cart/update/{id}', [ShopController::class, 'updateCart']);
Route::post('/cart/remove/{id}', [ShopController::class, 'removeFromCart']);
Route::get('/checkout', [ShopController::class, 'checkout']);
Route::post('/checkout', [ShopController::class, 'checkoutSubmit']);
Route::get('/checkout/success', [ShopController::class, 'checkoutSuccess']);

// Admin (protégé)
Route::middleware('auth')->group(function () {
    Route::get('/admin', [AdminController::class, 'dashboard']);
    Route::get('/admin/products', [AdminController::class, 'products']);
    Route::get('/admin/orders', [AdminController::class, 'orders']);
    Route::get('/admin/suppliers', [AdminController::class, 'suppliers']);
});
