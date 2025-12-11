@extends('layouts.app')

@section('title', 'Admin - Dashboard')

@section('content')
    <h1 class="text-3xl font-bold mb-6">Dashboard Admin</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white border rounded-lg p-4">
            <p class="text-sm text-gray-500">Revenus Total</p>
            <p class="text-2xl font-bold">${{ number_format($stats['revenue']['total'] ?? 0, 2) }}</p>
        </div>
        <div class="bg-white border rounded-lg p-4">
            <p class="text-sm text-gray-500">Marge</p>
            <p class="text-2xl font-bold">${{ number_format($stats['revenue']['margin'] ?? 0, 2) }}</p>
        </div>
        <div class="bg-white border rounded-lg p-4">
            <p class="text-sm text-gray-500">Commandes</p>
            <p class="text-2xl font-bold">{{ $stats['orders']['total'] ?? 0 }}</p>
            <p class="text-sm text-gray-500">{{ $stats['orders']['paid'] ?? 0 }} payées</p>
        </div>
        <div class="bg-white border rounded-lg p-4">
            <p class="text-sm text-gray-500">Produits actifs</p>
            <p class="text-2xl font-bold">{{ $stats['products']['active'] ?? 0 }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-8">
        <a href="{{ url('/admin/products') }}" class="bg-white border rounded-lg p-4 hover:shadow">
            <h3 class="text-lg font-semibold mb-2">Produits</h3>
            <p class="text-gray-600 text-sm">Gérer le catalogue</p>
        </a>
        <a href="{{ url('/admin/orders') }}" class="bg-white border rounded-lg p-4 hover:shadow">
            <h3 class="text-lg font-semibold mb-2">Commandes</h3>
            <p class="text-gray-600 text-sm">Suivre les commandes</p>
        </a>
        <a href="{{ url('/admin/suppliers') }}" class="bg-white border rounded-lg p-4 hover:shadow">
            <h3 class="text-lg font-semibold mb-2">Fournisseurs</h3>
            <p class="text-gray-600 text-sm">Gérer les fournisseurs</p>
        </a>
    </div>
@endsection

