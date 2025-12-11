@extends('layouts.app')

@section('title', 'Accueil - Boutique Dropshipping')

@section('content')
    <div class="bg-gradient-to-r from-[var(--primary)] to-[var(--secondary)] text-white rounded-2xl p-10 mb-12 shadow-lg">
        <div class="max-w-3xl">
            <p class="uppercase text-sm tracking-[0.2em] text-white/80 mb-3">Dropshipping automatisé</p>
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Lance ta boutique en quelques minutes</h1>
            <p class="text-lg text-white/90 mb-6">Paiement carte, automatisation fournisseur, dashboard admin. Tout est prêt.</p>
            <a href="{{ url('/products') }}" class="inline-block bg-white text-[var(--primary)] font-semibold px-6 py-3 rounded-lg hover:opacity-90 transition">
                Voir tous les produits
            </a>
        </div>
    </div>

    <h2 class="text-2xl font-bold mb-6 text-slate-900">Produits populaires</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($products as $product)
            <div class="bg-white border rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition">
                <div class="p-5 flex flex-col h-full">
                    <h3 class="font-semibold text-lg mb-2 text-slate-900">{{ $product->name }}</h3>
                    <p class="text-gray-600 text-sm line-clamp-2 flex-1">{{ $product->description }}</p>
                    <div class="mt-4 flex items-center justify-between">
                        <div>
                            <p class="text-2xl font-bold text-[var(--primary)]">${{ number_format($product->selling_price, 2) }}</p>
                            <p class="text-sm text-gray-500 line-through">${{ number_format($product->supplier_price, 2) }}</p>
                        </div>
                    </div>
                    <div class="mt-4 flex gap-2">
                        <a href="{{ url('/products/'.$product->id) }}" class="flex-1 text-center border border-slate-200 rounded-lg py-2 hover:bg-slate-50">
                            Détails
                        </a>
                        <form action="{{ url('/cart/add/'.$product->id) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full bg-[var(--primary)] text-white rounded-lg py-2 hover:bg-blue-700">
                                Ajouter
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-gray-600 col-span-full text-center">Aucun produit pour le moment.</p>
        @endforelse
    </div>
@endsection

