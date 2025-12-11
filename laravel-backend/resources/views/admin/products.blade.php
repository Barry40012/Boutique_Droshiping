@extends('layouts.app')

@section('title', 'Admin - Produits')

@section('content')
    <h1 class="text-3xl font-bold mb-6">Produits</h1>

    <div class="bg-white border rounded-lg overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prix</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Marge</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($products as $p)
                    <tr>
                        <td class="px-4 py-3">{{ $p->name }}</td>
                        <td class="px-4 py-3">${{ number_format($p->selling_price, 2) }}</td>
                        <td class="px-4 py-3 text-green-600">${{ number_format($p->margin, 2) }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded text-xs {{ $p->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700' }}">
                                {{ $p->status }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right text-sm text-gray-500">—</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-4 text-center text-gray-500">Aucun produit.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $products->links() }}
    </div>
@endsection

