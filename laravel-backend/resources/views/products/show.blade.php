@extends('layouts.app')

@section('title', $product->name . ' - Boutique')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div class="bg-white border rounded-lg p-4">
            <div class="w-full h-64 bg-gray-100 flex items-center justify-center text-gray-400">
                Image
            </div>
        </div>

        <div>
            <h1 class="text-4xl font-bold mb-4">{{ $product->name }}</h1>
            @if($product->description)
                <p class="text-gray-600 mb-4">{{ $product->description }}</p>
            @endif

            <div class="mb-6">
                <p class="text-4xl font-bold text-blue-600">${{ number_format($product->selling_price, 2) }}</p>
                <p class="text-lg text-gray-500 line-through">${{ number_format($product->supplier_price, 2) }}</p>
            </div>

            <form action="{{ url('/cart/add/'.$product->id) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium mb-1">Quantité</label>
                    <input type="number" name="quantity" value="1" min="1" class="w-24 border rounded px-3 py-2">
                </div>
                <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">
                    Ajouter au panier
                </button>
            </form>
        </div>
    </div>
@endsection

