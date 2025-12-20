@extends('layouts.store')

@section('title', 'Panier - ' . ($store->name ?? 'Dropshipping Platform'))

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
<div class="cart-page-wrapper" style="background: {{ $pageBgColor }}; --btn-bg-color: {{ $btnBgColor }}; --btn-text-color: {{ $btnTextColor }};">
    <div class="container">
        <h1 class="page-title">
            <i class="fas fa-shopping-cart icon-inline"></i>
            Mon Panier
        </h1>

        @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle icon-inline"></i>
                {{ session('success') }}
            </div>
        @endif

        @if(empty($cartWithProducts) || count($cartWithProducts) === 0)
            <div class="cart-empty">
                <div class="empty-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <h2>Votre panier est vide</h2>
                <p>Ajoutez des produits à votre panier pour commencer vos achats.</p>
                        <a href="{{ $store ? route('store.public', $store->slug) : '/' }}" class="btn-primary" style="background: {{ $btnBgColor }}; color: {{ $btnTextColor }};">
                            <i class="fas fa-arrow-left icon-inline"></i>
                            Continuer mes achats
                        </a>
            </div>
        @else
            <div class="cart-content">
                <div class="cart-items">
                    @foreach($cartWithProducts as $productId => $item)
                        @php
                            $product = $item['product'];
                            $quantity = $item['quantity'];
                            $subtotal = $product->selling_price * $quantity;
                            
                            // Récupérer l'image du produit
                            $productImage = null;
                            if ($product->relationLoaded('productImages') && $product->productImages->count() > 0) {
                                $productImage = $product->productImages->first()->image_url;
                            } elseif (is_array($product->images) && !empty($product->images)) {
                                $productImage = array_values(array_filter($product->images, function($img) {
                                    return !empty($img) && is_string($img);
                                }))[0] ?? null;
                            }
                        @endphp
                        <div class="cart-item" data-product-id="{{ $productId }}">
                            <div class="cart-item-image">
                                @if($productImage)
                                    <img src="{{ $productImage }}" alt="{{ $product->name }}">
                                @else
                                    <div class="cart-item-placeholder">
                                        <i class="fas fa-image"></i>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="cart-item-info">
                                <h3 class="cart-item-name">{{ $product->name }}</h3>
                                @if($product->description)
                                    <p class="cart-item-description">
                                        {{ Str::limit($product->description, 100) }}
                                    </p>
                                @endif
                                <div class="cart-item-price">
                                    <span class="price-label">Prix unitaire:</span>
                                    <span class="price-value">{{ number_format($product->selling_price, 2) }} {{ $product->store->currency ?? 'USD' }}</span>
                                </div>
                            </div>
                            
                            <div class="cart-item-quantity">
                                <label for="quantity_{{ $productId }}" class="quantity-label">Quantité</label>
                                <form action="{{ route('cart.update', $productId) }}" method="POST" class="quantity-form">
                                    @csrf
                                    <div class="quantity-controls">
                                        <button type="button" class="quantity-btn minus" onclick="changeCartQuantity('{{ $productId }}', -1)">-</button>
                                        <input type="number" 
                                               id="quantity_{{ $productId }}" 
                                               name="quantity" 
                                               value="{{ $quantity }}" 
                                               min="1" 
                                               class="quantity-input"
                                               onchange="updateCartQuantity('{{ $productId }}', this.value)">
                                        <button type="button" class="quantity-btn plus" onclick="changeCartQuantity('{{ $productId }}', 1)">+</button>
                                    </div>
                                    <button type="submit" class="btn-update-quantity" style="display: none;" id="update_btn_{{ $productId }}">
                                        <i class="fas fa-sync-alt icon-inline"></i>
                                        Mettre à jour
                                    </button>
                                </form>
                            </div>
                            
                            <div class="cart-item-subtotal">
                                <div class="subtotal-label">Sous-total</div>
                                <div class="subtotal-value" id="subtotal_{{ $productId }}">
                                    {{ number_format($subtotal, 2) }} {{ $product->store->currency ?? 'USD' }}
                                </div>
                            </div>
                            
                            <div class="cart-item-actions">
                                <form action="{{ route('cart.remove', $productId) }}" method="POST" class="remove-form" onsubmit="return confirm('Êtes-vous sûr de vouloir retirer ce produit du panier ?');">
                                    @csrf
                                    <button type="submit" class="btn-remove" title="Retirer du panier">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="cart-summary">
                    <div class="summary-header">
                        <h2>
                            <i class="fas fa-receipt icon-inline"></i>
                            Récapitulatif
                        </h2>
                    </div>
                    <div class="summary-content">
                        <div class="summary-row">
                            <span class="summary-label">Sous-total</span>
                            <span class="summary-value" id="cart-total">{{ number_format($total, 2) }} {{ $cartWithProducts[array_key_first($cartWithProducts)]['product']->store->currency ?? 'USD' }}</span>
                        </div>
                        <div class="summary-row">
                            <span class="summary-label">Livraison</span>
                            <span class="summary-value">À calculer</span>
                        </div>
                        <div class="summary-row total-row">
                            <span class="summary-label">Total</span>
                            <span class="summary-value" id="cart-grand-total">{{ number_format($total, 2) }} {{ $cartWithProducts[array_key_first($cartWithProducts)]['product']->store->currency ?? 'USD' }}</span>
                        </div>
                    </div>
                    <div class="summary-actions">
                        <a href="{{ $store ? route('store.public', $store->slug) : '/' }}" class="btn-secondary">
                            <i class="fas fa-arrow-left icon-inline"></i>
                            Continuer mes achats
                        </a>
                        <a href="{{ route('checkout') }}" class="btn-primary btn-checkout" style="background: {{ $btnBgColor }}; color: {{ $btnTextColor }};">
                            <i class="fas fa-lock icon-inline"></i>
                            Procéder au paiement
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<style>
.cart-page-wrapper {
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

.alert-success {
    background: #d1fae5;
    color: #065f46;
    border: 1px solid #6ee7b7;
}

.cart-empty {
    text-align: center;
    padding: 4rem 2rem;
}

.empty-icon {
    font-size: 4rem;
    color: #d1d5db;
    margin-bottom: 1rem;
}

.cart-empty h2 {
    font-size: 1.5rem;
    color: #374151;
    margin-bottom: 0.5rem;
}

.cart-empty p {
    color: #6b7280;
    margin-bottom: 2rem;
}

.cart-content {
    display: grid;
    grid-template-columns: 1fr 350px;
    gap: 2rem;
    margin-top: 2rem;
}

.cart-items {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.cart-item {
    display: grid;
    grid-template-columns: 120px 1fr 200px 150px 60px;
    gap: 1.5rem;
    padding: 1.5rem;
    background: white;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    align-items: center;
}

.cart-item-image {
    width: 120px;
    height: 120px;
    border-radius: 8px;
    overflow: hidden;
    background: #f3f4f6;
    display: flex;
    align-items: center;
    justify-content: center;
}

.cart-item-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.cart-item-placeholder {
    color: #9ca3af;
    font-size: 2rem;
}

.cart-item-info {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.cart-item-name {
    font-size: 1.125rem;
    font-weight: 600;
    color: #111827;
    margin: 0;
}

.cart-item-description {
    color: #6b7280;
    font-size: 0.875rem;
    margin: 0;
}

.cart-item-price {
    display: flex;
    gap: 0.5rem;
    font-size: 0.875rem;
    color: #374151;
}

.price-label {
    font-weight: 500;
}

.price-value {
    color: #059669;
    font-weight: 600;
}

.cart-item-quantity {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.quantity-label {
    font-size: 0.875rem;
    font-weight: 500;
    color: #374151;
}

.quantity-controls {
    display: inline-flex;
    align-items: center;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    overflow: hidden;
    background: #f9fafb;
}

.quantity-btn {
    background: transparent;
    border: none;
    color: #111827;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background 0.2s;
}

.quantity-btn:hover {
    background: #e5e7eb;
}

.quantity-input {
    width: 60px;
    text-align: center;
    border: none;
    background: transparent;
    color: #111827;
    font-weight: 500;
    padding: 0.5rem;
}

.btn-update-quantity {
    margin-top: 0.5rem;
    padding: 0.5rem 1rem;
    background: #3b82f6;
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 0.875rem;
    cursor: pointer;
    transition: background 0.2s;
}

.btn-update-quantity:hover {
    background: #2563eb;
}

.cart-item-subtotal {
    text-align: right;
}

.subtotal-label {
    font-size: 0.875rem;
    color: #6b7280;
    margin-bottom: 0.25rem;
}

.subtotal-value {
    font-size: 1.125rem;
    font-weight: 700;
    color: #059669;
}

.cart-item-actions {
    display: flex;
    justify-content: center;
}

.btn-remove {
    background: #fee2e2;
    color: #dc2626;
    border: none;
    width: 40px;
    height: 40px;
    border-radius: 8px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}

.btn-remove:hover {
    background: #fecaca;
    color: #b91c1c;
}

.cart-summary {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    height: fit-content;
    position: sticky;
    top: 2rem;
}

.summary-header h2 {
    font-size: 1.25rem;
    font-weight: 700;
    color: #111827;
    margin: 0 0 1.5rem 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.summary-content {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid #e5e7eb;
    margin-bottom: 1.5rem;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
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
    padding-top: 1rem;
    border-top: 2px solid #e5e7eb;
    margin-top: 0.5rem;
}

.total-row .summary-label,
.total-row .summary-value {
    font-size: 1.25rem;
    font-weight: 700;
    color: #059669;
}

.summary-actions {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.btn-primary,
.btn-secondary {
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    text-decoration: none;
    text-align: center;
    font-weight: 600;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.btn-primary {
    background: var(--btn-bg-color, #3b82f6);
    color: var(--btn-text-color, white);
}

.btn-primary:hover {
    opacity: 0.9;
    filter: brightness(0.95);
}

.btn-secondary {
    background: #f3f4f6;
    color: #374151;
    border: 1px solid #e5e7eb;
}

.btn-secondary:hover {
    background: #e5e7eb;
}

@media (max-width: 1024px) {
    .cart-content {
        grid-template-columns: 1fr;
    }
    
    .cart-summary {
        position: static;
    }
}

@media (max-width: 768px) {
    .cart-item {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .cart-item-image {
        width: 100%;
        height: 200px;
    }
    
    .cart-item-quantity,
    .cart-item-subtotal,
    .cart-item-actions {
        justify-self: start;
    }
}
</style>

<script>
// Prix unitaires des produits (pour calculer les sous-totaux)
const productPrices = {
    @foreach($cartWithProducts ?? [] as $productId => $item)
    '{{ $productId }}': {{ number_format($item['product']->selling_price, 2, '.', '') }},
    @endforeach
};

function changeCartQuantity(productId, delta) {
    const input = document.getElementById('quantity_' + productId);
    const currentValue = parseInt(input.value) || 1;
    const newValue = Math.max(1, currentValue + delta);
    input.value = newValue;
    updateCartQuantity(productId, newValue);
}

function updateCartQuantity(productId, quantity) {
    const qty = parseInt(quantity) || 1;
    const price = productPrices[productId] || 0;
    const subtotal = (price * qty).toFixed(2);
    
    // Mettre à jour le sous-total
    const subtotalEl = document.getElementById('subtotal_' + productId);
    if (subtotalEl) {
        const currency = '{{ $cartWithProducts[array_key_first($cartWithProducts ?? [])]["product"]->store->currency ?? "USD" }}';
        subtotalEl.textContent = subtotal + ' ' + currency;
    }
    
    // Afficher le bouton "Mettre à jour"
    const updateBtn = document.getElementById('update_btn_' + productId);
    if (updateBtn) {
        updateBtn.style.display = 'block';
    }
    
    // Recalculer le total du panier
    updateCartTotal();
}

function updateCartTotal() {
    let total = 0;
    document.querySelectorAll('.cart-item').forEach(item => {
        const productId = item.dataset.productId;
        const quantity = parseInt(document.getElementById('quantity_' + productId)?.value || 1);
        const price = productPrices[productId] || 0;
        total += price * quantity;
    });
    
    const currency = '{{ $cartWithProducts[array_key_first($cartWithProducts ?? [])]["product"]->store->currency ?? "USD" }}';
    const totalEl = document.getElementById('cart-total');
    const grandTotalEl = document.getElementById('cart-grand-total');
    
    if (totalEl) totalEl.textContent = total.toFixed(2) + ' ' + currency;
    if (grandTotalEl) grandTotalEl.textContent = total.toFixed(2) + ' ' + currency;
}
</script>
@endsection
