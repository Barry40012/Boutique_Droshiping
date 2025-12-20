@extends('layouts.store')

@section('title', 'Suivre ma commande' . ($store ? ' - ' . $store->name : ''))

@section('content')
@php
    // Récupérer les couleurs de la boutique si disponible
    $primary = $store->primary_color ?? '#111827';
    $secondary = $store->secondary_color ?? '#1f2937';
    $accent = $store->accent_color ?? '#f59e0b';
    $btnBgColor = $store->button_bg_color ?? ($store->primary_color ?? '#3b82f6');
    $btnTextColor = $store->button_text_color ?? '#ffffff';
    $pageBgColor = $store->page_bg_color ?? '#f9fafb';
@endphp
<div class="track-order-page" style="background: {{ $pageBgColor }};">
    <div class="container" style="max-width: 600px; margin: 4rem auto; padding: 2rem;">
        <div class="track-order-card">
            <h1 class="page-title">
                <i class="fas fa-truck"></i>
                Suivre ma commande
            </h1>
            <p class="page-subtitle">Entrez votre numéro de suivi pour connaître le statut de votre commande</p>
            
            @if(session('error'))
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('track.order.submit') }}" method="POST" class="track-form">
                @csrf
                <div class="form-group">
                    <label for="tracking_number">Numéro de suivi *</label>
                    <input 
                        type="text" 
                        id="tracking_number" 
                        name="tracking_number" 
                        class="form-input"
                        placeholder="Ex: TRACK123456789"
                        required
                        value="{{ old('tracking_number') }}"
                    >
                    @error('tracking_number')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
                
                <button type="submit" class="submit-btn" style="background: {{ $btnBgColor }}; color: {{ $btnTextColor }};">
                    <i class="fas fa-search"></i>
                    Suivre la commande
                </button>
            </form>
        </div>
    </div>
</div>

<style>
.track-order-page {
    min-height: 60vh;
    background: #f9fafb;
}

.track-order-card {
    background: white;
    padding: 3rem;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}

.page-title {
    font-size: 2rem;
    font-weight: 700;
    color: #111827;
    margin: 0 0 0.5rem 0;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.page-title i {
    color: {{ $btnBgColor }};
}

.page-subtitle {
    color: #6b7280;
    margin: 0 0 2rem 0;
    font-size: 1rem;
}

.alert {
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.alert-error {
    background: #fef2f2;
    color: #991b1b;
    border: 1px solid #fecaca;
}

.track-form {
    margin-top: 2rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: #374151;
}

.form-input {
    width: 100%;
    padding: 0.875rem 1rem;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 1rem;
    transition: border-color 0.2s;
}

.form-input:focus {
    outline: none;
    border-color: {{ $btnBgColor }};
}

.error-message {
    display: block;
    color: #dc2626;
    font-size: 0.875rem;
    margin-top: 0.5rem;
}

.submit-btn {
    width: 100%;
    padding: 1rem 2rem;
    border: none;
    border-radius: 8px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    transition: all 0.2s;
}

.submit-btn:hover {
    opacity: 0.9;
    filter: brightness(0.95);
}
</style>
@endsection

