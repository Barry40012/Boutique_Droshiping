@extends('layouts.app')

@section('title', 'Abonnement requis - Merchant')

@section('content')
<div class="merchant-create-store-container">
    <div class="create-store-card" data-aos="fade-up">
        <div class="create-store-header">
            <h1 class="create-store-title">
                <i class="fas fa-lock icon-inline"></i>
                Abonnement requis
            </h1>
            <p class="create-store-subtitle">Active ton abonnement pour accéder au dashboard merchant.</p>
        </div>

        <div class="space-y-4 text-center">
            @if($store)
                <p class="text-slate-600">Boutique : <strong>{{ $store->name }}</strong></p>
            @else
                <p class="text-slate-600">Aucune boutique trouvée. Crée d'abord ta boutique.</p>
            @endif
        </div>

        <div class="form-actions" style="margin-top:24px; display:flex; flex-direction:column; gap:12px;">
            @if(!$store)
                <a class="btn-primary btn-large text-center" href="{{ route('merchant.store.create') }}">
                    <i class="fas fa-store icon-inline"></i> Créer ma boutique
                </a>
            @endif
            <button class="btn-secondary btn-large" disabled>
                <i class="fas fa-credit-card icon-inline"></i> Activation abonnement (à configurer)
            </button>
            <a class="btn-link text-center" href="{{ route('merchant.dashboard') }}">
                <i class="fas fa-arrow-left icon-inline"></i> Retour
            </a>
        </div>
    </div>
</div>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init();
</script>
@endsection

