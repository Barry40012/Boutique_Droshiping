@extends('layouts.app')

@section('title', 'Panier')

@section('content')
    <h1 class="text-3xl font-bold mb-6">Votre Panier</h1>

    @if(empty($cart))
        <div class="text-center text-gray-600">
            Votre panier est vide.
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-4">
                @foreach($cart as $item)
                    <div class="bg-white border rounded-lg p-4 flex items-center justify-between">
                        <div>
                            <div class="font-semibold">{{ $item['product']->name }}</div>
                            <div class="text-gray-600 text-sm">${{ number_format($item['product']->selling_price, 2) }}</div>
                        </div>
                        <div class="flex items-center gap-3">
                            <form action="{{ url('/cart/update/'.$item['product']->id) }}" method="POST" class="flex items-center gap-2">
                                @csrf
                                <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" class="w-20 border rounded px-2 py-1">
                                <button class="text-blue-600 hover:underline">Mettre à jour</button>
                            </form>
                            <form action="{{ url('/cart/remove/'.$item['product']->id) }}" method="POST">
                                @csrf
                                <button class="text-red-600 hover:underline">Supprimer</button>
                            </form>
                        </div>
                        <div class="font-bold">${{ number_format($item['product']->selling_price * $item['quantity'], 2) }}</div>
                    </div>
                @endforeach
            </div>
            <div>
                <div class="bg-white border rounded-lg p-6">
                    <h2 class="text-2xl font-semibold mb-4">Résumé</h2>
                    <div class="flex justify-between mb-2">
                        <span>Sous-total</span>
                        <span>${{ number_format($total, 2) }}</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span>Livraison</span>
                        <span class="text-green-600">Gratuite</span>
                    </div>
                    <div class="border-t pt-3 mt-3 flex justify-between text-xl font-bold">
                        <span>Total</span>
                        <span>${{ number_format($total, 2) }}</span>
                    </div>
                    <a href="{{ url('/checkout') }}" class="mt-4 inline-block w-full text-center bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700">
                        Passer la commande
                    </a>
                </div>
            </div>
        </div>
    @endif
@endsection

