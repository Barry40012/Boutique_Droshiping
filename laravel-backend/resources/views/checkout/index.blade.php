@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
    <h1 class="text-3xl font-bold mb-6">Checkout</h1>

    @if(empty($cart))
        <p class="text-gray-600">Votre panier est vide.</p>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <form action="{{ url('/checkout') }}" method="POST" class="space-y-4">
                @csrf
                <h2 class="text-xl font-semibold">Informations de livraison</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Nom *</label>
                        <input required name="name" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Téléphone *</label>
                        <input required name="phone" class="w-full border rounded px-3 py-2">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Adresse *</label>
                    <input required name="street" class="w-full border rounded px-3 py-2">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Ville *</label>
                        <input required name="city" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Pays *</label>
                        <input required name="country" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Code postal *</label>
                        <input required name="postalCode" class="w-full border rounded px-3 py-2">
                    </div>
                </div>
                <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">
                    Confirmer la commande
                </button>
            </form>

            <div class="bg-white border rounded-lg p-6 h-fit">
                <h2 class="text-xl font-semibold mb-4">Résumé</h2>
                <div class="space-y-2 mb-4">
                    @foreach($cart as $item)
                        <div class="flex justify-between text-sm">
                            <span>{{ $item['product']->name }} x{{ $item['quantity'] }}</span>
                            <span>${{ number_format($item['product']->selling_price * $item['quantity'], 2) }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="border-t pt-3 flex justify-between text-xl font-bold">
                    <span>Total</span>
                    <span>${{ number_format($total, 2) }}</span>
                </div>
            </div>
        </div>
    @endif
@endsection

