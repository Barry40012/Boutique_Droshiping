@extends('layouts.app')

@section('title', 'Produits')

@section('content')
    <h1 class="text-3xl font-bold mb-6">Tous nos Produits</h1>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($products as $product)
            <div class="bg-white border rounded-lg overflow-hidden shadow-sm p-4 flex flex-col">
                <h3 class="font-semibold text-lg mb-2">{{ $product->name }}</h3>
                <p class="text-gray-600 text-sm line-clamp-2 flex-1">{{ $product->description }}</p>
                <div class="mt-4 flex items-center justify-between">
                    <div>
                        <p class="text-2xl font-bold text-blue-600">${{ number_format($product->selling_price, 2) }}</p>
                        <p class="text-sm text-gray-500 line-through">${{ number_format($product->supplier_price, 2) }}</p>
                    </div>
                </div>
                <div class="mt-4 flex gap-2">
                    <a href="{{ url('/products/'.$product->id) }}" class="flex-1 text-center border rounded-lg py-2 hover:bg-gray-50">
                        Détails
                    </a>
                    <form action="{{ url('/cart/add/'.$product->id) }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit" class="w-full bg-blue-600 text-white rounded-lg py-2 hover:bg-blue-700">
                            Ajouter
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <p class="text-gray-600">Aucun produit.</p>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $products->links() }}
    </div>
@endsection

