@extends('layouts.app')

@section('title', 'Accueil - Boutique Dropshipping')

@section('content')
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold mb-4">Bienvenue dans votre Boutique</h1>
        <p class="text-gray-600 text-lg">Découvrez notre sélection de produits livrés directement chez vous.</p>
        <a href="{{ url('/products') }}" class="mt-6 inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">
            Voir tous les produits
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($products as $product)
            <div class="bg-white border rounded-lg overflow-hidden shadow-sm">
                <div class="p-4">
                    <h3 class="font-semibold text-lg mb-2">{{ $product->name }}</h3>
                    <p class="text-gray-600 text-sm line-clamp-2 mb-4">{{ $product->description }}</p>
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-2xl font-bold text-blue-600">${{ number_format($product->selling_price, 2) }}</p>
                            <p class="text-sm text-gray-500 line-through">${{ number_format($product->supplier_price, 2) }}</p>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ url('/products/'.$product->id) }}" class="flex-1 text-center border rounded-lg py-2 hover:bg-gray-50">
                            Détails
                        </a>
                        <form action="{{ url('/cart/add/'.$product->id) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full bg-blue-600 text-white rounded-lg py-2 hover:bg-blue-700">
                                Ajouter au panier
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

