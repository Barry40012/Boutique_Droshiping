@extends('layouts.app')

@section('title', 'Accueil - Boutique Dropshipping')

@section('content')
    <div class="hero">
        <div class="hero-content">
            <p class="hero-kicker">Dropshipping automatisé</p>
            <h1 class="hero-title">Lance ta boutique en quelques minutes</h1>
            <p class="hero-subtitle">Paiement carte, automatisation fournisseur, dashboard admin. Tout est prêt.</p>
            <a href="{{ url('/products') }}" class="btn-primary">
                Voir tous les produits
            </a>
        </div>
    </div>

    <h2 class="section-title">Produits populaires</h2>
    <div class="grid">
        @forelse($products as $product)
            <div class="card">
                <h3 class="card-title">{{ $product->name }}</h3>
                <p class="card-text">{{ $product->description }}</p>
                <div class="card-price">
                    <div class="price-main">${{ number_format($product->selling_price, 2) }}</div>
                    <div class="price-old">${{ number_format($product->supplier_price, 2) }}</div>
                </div>
                <div class="card-actions">
                    <a href="{{ url('/products/'.$product->id) }}" class="btn-ghost">Détails</a>
                    <form action="{{ url('/cart/add/'.$product->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-primary">Ajouter</button>
                    </form>
                </div>
            </div>
        @empty
            <p class="text-gray-600 col-span-full text-center">Aucun produit pour le moment.</p>
        @endforelse
    </div>
@endsection

