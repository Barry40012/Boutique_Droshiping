@extends('layouts.merchant')

@section('title', 'Fournisseurs - Dashboard Merchant')

@section('content')
<div class="merchant-suppliers-container">
    <div class="suppliers-header" data-aos="fade-down">
        <h1 class="suppliers-title">
            <i class="fas fa-truck icon-inline"></i>
            Fournisseurs
        </h1>
        <p class="suppliers-subtitle">Configurez vos fournisseurs pour l'automatisation des commandes</p>
    </div>

    <!-- Liste des Fournisseurs -->
    <div class="suppliers-grid" data-aos="fade-up">
        @foreach($allSuppliers as $supplier)
            @php
                $wallet = $wallets->get($supplier->id);
            @endphp
            <div class="supplier-card {{ $wallet ? 'supplier-active' : '' }}" data-aos="fade-up">
                <div class="supplier-header">
                    <div class="supplier-icon">
                        @if($supplier->type === 'aliexpress')
                            <i class="fas fa-shopping-bag"></i>
                        @elseif($supplier->type === 'cj_dropshipping')
                            <i class="fas fa-box"></i>
                        @elseif($supplier->type === 'temu')
                            <i class="fas fa-store"></i>
                        @else
                            <i class="fas fa-cog"></i>
                        @endif
                    </div>
                    <div class="supplier-info">
                        <h3 class="supplier-name">{{ $supplier->name }}</h3>
                        <p class="supplier-type">{{ ucfirst(str_replace('_', ' ', $supplier->type)) }}</p>
                    </div>
                    @if($wallet)
                        <span class="supplier-badge badge-success">
                            <i class="fas fa-check-circle"></i>
                            Configuré
                        </span>
                    @endif
                </div>

                <p class="supplier-description">{{ $supplier->description }}</p>

                <div class="supplier-features">
                    @if($supplier->auto_fulfill)
                        <span class="feature-badge">
                            <i class="fas fa-robot"></i>
                            Automatisation
                        </span>
                    @endif
                    @if($supplier->is_active)
                        <span class="feature-badge">
                            <i class="fas fa-check"></i>
                            Actif
                        </span>
                    @endif
                </div>

                @if($wallet)
                    <div class="supplier-wallet-info">
                        <div class="wallet-info-item">
                            <span class="wallet-label">Balance:</span>
                            <span class="wallet-value">${{ number_format($wallet->balance, 2) }}</span>
                        </div>
                        <div class="wallet-info-item">
                            <span class="wallet-label">Disponible:</span>
                            <span class="wallet-value">${{ number_format($wallet->availableBalance(), 2) }}</span>
                        </div>
                    </div>
                    <div class="supplier-actions">
                        <a href="{{ route('merchant.wallets') }}" class="btn-primary btn-sm">
                            <i class="fas fa-wallet icon-inline"></i>
                            Gérer wallet
                        </a>
                    </div>
                @else
                    <form action="{{ route('merchant.suppliers.wallet.create') }}" method="POST" class="supplier-form">
                        @csrf
                        <input type="hidden" name="supplier_id" value="{{ $supplier->id }}">
                        <div class="form-group">
                            <label class="form-label">Balance initiale (optionnel)</label>
                            <input type="number" 
                                   name="initial_balance" 
                                   class="form-input" 
                                   step="0.01" 
                                   min="0" 
                                   placeholder="0.00">
                        </div>
                        <button type="submit" class="btn-primary btn-block">
                            <i class="fas fa-plus icon-inline"></i>
                            Créer un wallet
                        </button>
                    </form>
                @endif
            </div>
        @endforeach
    </div>
</div>
@endsection

