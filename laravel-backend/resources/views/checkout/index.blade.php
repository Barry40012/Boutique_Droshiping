@extends('layouts.store')

@section('title', 'Checkout - ' . ($store->name ?? 'Dropshipping Platform'))

@section('content')
@php
    // Récupérer les couleurs de la boutique
    $primary = $store->primary_color ?? '#111827';
    $secondary = $store->secondary_color ?? '#1f2937';
    $accent = $store->accent_color ?? '#f59e0b';
    $btnBgColor = $store->button_bg_color ?? ($store->primary_color ?? '#111827');
    $btnTextColor = $store->button_text_color ?? '#ffffff';
    $pageBgColor = $store->page_bg_color ?? '#ffffff';
@endphp
<div class="checkout-page-wrapper" style="background: {{ $pageBgColor }};">
    <div class="container">
        <h1 class="page-title">
            <i class="fas fa-lock icon-inline"></i>
            Finaliser votre commande
        </h1>

        @if($errors->any())
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle icon-inline"></i>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('checkout.submit') }}" method="POST" class="checkout-form">
            @csrf
            <div class="checkout-content">
                <div class="checkout-form-section">
                    <h2 class="section-title">
                        <i class="fas fa-user icon-inline"></i>
                        Informations de livraison
                    </h2>
                    
                    <div class="form-group">
                        <label for="name" class="form-label">Nom complet *</label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               class="form-input" 
                               value="{{ old('name') }}" 
                               required>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone" class="form-label">Téléphone *</label>
                        <input type="tel" 
                               id="phone" 
                               name="phone" 
                               class="form-input" 
                               value="{{ old('phone') }}" 
                               required>
                    </div>
                    
                    <div class="form-group">
                        <label for="street" class="form-label">Adresse *</label>
                        <input type="text" 
                               id="street" 
                               name="street" 
                               class="form-input" 
                               value="{{ old('street') }}" 
                               required>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="city" class="form-label">Ville *</label>
                            <input type="text" 
                                   id="city" 
                                   name="city" 
                                   class="form-input" 
                                   value="{{ old('city') }}" 
                                   required>
                        </div>
                        
                        <div class="form-group">
                            <label for="postalCode" class="form-label">Code postal *</label>
                            <input type="text" 
                                   id="postalCode" 
                                   name="postalCode" 
                                   class="form-input" 
                                   value="{{ old('postalCode') }}" 
                                   required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="country" class="form-label">Pays *</label>
                        <input type="text" 
                               id="country" 
                               name="country" 
                               class="form-input" 
                               value="{{ old('country') }}" 
                               required>
                    </div>
                </div>
                
                <div class="checkout-summary-section">
                    <h2 class="section-title">
                        <i class="fas fa-shopping-bag icon-inline"></i>
                        Récapitulatif de la commande
                    </h2>
                    
                    <div class="order-items">
                        @foreach($cartWithProducts ?? [] as $productId => $item)
                            @php
                                $product = $item['product'];
                                $quantity = $item['quantity'];
                                $subtotal = $product->selling_price * $quantity;
                                
                                $productImage = null;
                                if ($product->relationLoaded('productImages') && $product->productImages->count() > 0) {
                                    $productImage = $product->productImages->first()->image_url;
                                } elseif (is_array($product->images) && !empty($product->images)) {
                                    $productImage = array_values(array_filter($product->images, function($img) {
                                        return !empty($img) && is_string($img);
                                    }))[0] ?? null;
                                }
                            @endphp
                            <div class="order-item">
                                <div class="order-item-image">
                                    @if($productImage)
                                        <img src="{{ $productImage }}" alt="{{ $product->name }}">
                                    @else
                                        <div class="order-item-placeholder">
                                            <i class="fas fa-image"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="order-item-info">
                                    <h4 class="order-item-name">{{ $product->name }}</h4>
                                    <div class="order-item-details">
                                        <span>Quantité: {{ $quantity }}</span>
                                        <span class="order-item-price">{{ number_format($product->selling_price, 2) }} {{ $product->store->currency ?? 'USD' }}</span>
                                    </div>
                                </div>
                                <div class="order-item-subtotal">
                                    {{ number_format($subtotal, 2) }} {{ $product->store->currency ?? 'USD' }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="order-summary">
                        <div class="summary-row">
                            <span class="summary-label">Sous-total</span>
                            <span class="summary-value">{{ number_format($total, 2) }} {{ $cartWithProducts[array_key_first($cartWithProducts ?? [])]['product']->store->currency ?? 'USD' }}</span>
                        </div>
                        <div class="summary-row">
                            <span class="summary-label">Livraison</span>
                            <span class="summary-value">À calculer</span>
                        </div>
                        <div class="summary-row total-row">
                            <span class="summary-label">Total</span>
                            <span class="summary-value">{{ number_format($total, 2) }} {{ $cartWithProducts[array_key_first($cartWithProducts ?? [])]['product']->store->currency ?? 'USD' }}</span>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-submit-order" style="background: {{ $btnBgColor }}; color: {{ $btnTextColor }};">
                        <i class="fas fa-lock icon-inline"></i>
                        Confirmer la commande
                    </button>
                    
                    <a href="{{ route('cart') }}" class="btn-back-to-cart">
                        <i class="fas fa-arrow-left icon-inline"></i>
                        Retour au panier
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
.checkout-page-wrapper {
    padding: 2rem 0;
    min-height: 60vh;
}

.page-title {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 2rem;
    color: #1f2937;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.alert {
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
}

.alert-error {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fecaca;
}

.alert-error ul {
    margin: 0.5rem 0 0 0;
    padding-left: 1.5rem;
}

.checkout-content {
    display: grid;
    grid-template-columns: 1fr 400px;
    gap: 2rem;
    margin-top: 2rem;
}

.checkout-form-section,
.checkout-summary-section {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.section-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #111827;
    margin: 0 0 1.5rem 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #e5e7eb;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.form-label {
    display: block;
    font-weight: 500;
    color: #374151;
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
}

.form-input {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 1rem;
    transition: border-color 0.2s;
}

.form-input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.order-items {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin-bottom: 1.5rem;
    max-height: 400px;
    overflow-y: auto;
}

.order-item {
    display: flex;
    gap: 1rem;
    padding: 1rem;
    background: #f9fafb;
    border-radius: 8px;
}

.order-item-image {
    width: 60px;
    height: 60px;
    border-radius: 6px;
    overflow: hidden;
    background: white;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.order-item-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.order-item-placeholder {
    color: #9ca3af;
    font-size: 1.5rem;
}

.order-item-info {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.order-item-name {
    font-size: 0.875rem;
    font-weight: 600;
    color: #111827;
    margin: 0;
}

.order-item-details {
    display: flex;
    gap: 1rem;
    font-size: 0.75rem;
    color: #6b7280;
}

.order-item-price {
    color: #059669;
    font-weight: 600;
}

.order-item-subtotal {
    font-weight: 700;
    color: #111827;
    font-size: 0.875rem;
    align-self: center;
}

.order-summary {
    padding-top: 1.5rem;
    border-top: 2px solid #e5e7eb;
    margin-bottom: 1.5rem;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.75rem;
}

.summary-label {
    color: #6b7280;
    font-size: 0.875rem;
}

.summary-value {
    font-weight: 600;
    color: #111827;
}

.total-row {
    font-size: 1.125rem;
    padding-top: 0.75rem;
    border-top: 1px solid #e5e7eb;
    margin-top: 0.75rem;
}

.total-row .summary-label,
.total-row .summary-value {
    font-size: 1.25rem;
    font-weight: 700;
    color: #059669;
}

.btn-submit-order {
    width: 100%;
    padding: 1rem;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.btn-submit-order:hover {
    opacity: 0.9;
    filter: brightness(0.95);
}

.btn-back-to-cart {
    width: 100%;
    padding: 0.75rem;
    background: #f3f4f6;
    color: #374151;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    text-decoration: none;
    text-align: center;
    font-weight: 600;
    transition: background 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.btn-back-to-cart:hover {
    background: #e5e7eb;
}

@media (max-width: 1024px) {
    .checkout-content {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection
