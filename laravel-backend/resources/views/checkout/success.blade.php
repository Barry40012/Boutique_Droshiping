@extends('layouts.app')

@section('title', 'Commande confirmée')

@section('content')
    <div class="max-w-2xl mx-auto text-center bg-white border rounded-lg p-8 shadow">
        <div class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
            ✓
        </div>
        <h1 class="text-3xl font-bold mb-2">Commande confirmée</h1>
        <p class="text-gray-600 mb-6">Merci pour votre achat. Nous traitons votre commande.</p>
        <a href="{{ url('/products') }}" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">
            Continuer vos achats
        </a>
    </div>
@endsection

