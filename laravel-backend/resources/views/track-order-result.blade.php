@extends('layouts.store')

@section('title', 'Résultat du suivi' . ($store ? ' - ' . $store->name : ''))

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
<div class="track-result-page" style="background: {{ $pageBgColor }};">
    <div class="container" style="max-width: 800px; margin: 4rem auto; padding: 2rem;">
        <div class="track-result-card">
            <h1 class="page-title">
                <i class="fas fa-box"></i>
                Statut de votre commande
            </h1>
            
            <div class="tracking-info">
                <div class="info-row">
                    <span class="info-label">Numéro de suivi:</span>
                    <span class="info-value">{{ $tracking->tracking_number }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Statut:</span>
                    <span class="status-badge status-{{ $tracking->status }}">
                        @if($tracking->status === 'pending')
                            En attente
                        @elseif($tracking->status === 'processing')
                            En traitement
                        @elseif($tracking->status === 'shipped')
                            Expédiée
                        @elseif($tracking->status === 'delivered')
                            Livrée
                        @elseif($tracking->status === 'cancelled')
                            Annulée
                        @else
                            {{ $tracking->status }}
                        @endif
                    </span>
                </div>
                @if($tracking->status_updated_at)
                    <div class="info-row">
                        <span class="info-label">Dernière mise à jour:</span>
                        <span class="info-value">{{ $tracking->status_updated_at->format('d/m/Y à H:i') }}</span>
                    </div>
                @endif
                @if($tracking->notes)
                    <div class="info-row">
                        <span class="info-label">Notes:</span>
                        <span class="info-value">{{ $tracking->notes }}</span>
                    </div>
                @endif
            </div>

            @if($order)
                <div class="order-details">
                    <h2>Détails de la commande</h2>
                    <div class="detail-row">
                        <span>Numéro de commande:</span>
                        <span>{{ $order->id }}</span>
                    </div>
                    <div class="detail-row">
                        <span>Montant total:</span>
                        <span>{{ number_format($order->total_amount, 2) }} {{ $order->store->currency ?? 'USD' }}</span>
                    </div>
                    <div class="detail-row">
                        <span>Date de commande:</span>
                        <span>{{ $order->created_at->format('d/m/Y à H:i') }}</span>
                    </div>
                </div>
            @endif

            <div class="action-buttons">
                <a href="{{ route('track.order') }}" class="btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Nouvelle recherche
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.track-result-page {
    min-height: 60vh;
    background: #f9fafb;
}

.track-result-card {
    background: white;
    padding: 3rem;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}

.page-title {
    font-size: 2rem;
    font-weight: 700;
    color: #111827;
    margin: 0 0 2rem 0;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.page-title i {
    color: {{ $btnBgColor }};
}

.tracking-info {
    background: #f9fafb;
    padding: 2rem;
    border-radius: 8px;
    margin-bottom: 2rem;
}

.info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 0;
    border-bottom: 1px solid #e5e7eb;
}

.info-row:last-child {
    border-bottom: none;
}

.info-label {
    font-weight: 600;
    color: #6b7280;
}

.info-value {
    color: #111827;
    font-weight: 500;
}

.status-badge {
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-weight: 600;
    font-size: 0.9rem;
}

.status-pending {
    background: #fef3c7;
    color: #92400e;
}

.status-processing {
    background: #dbeafe;
    color: #1e40af;
}

.status-shipped {
    background: #e0e7ff;
    color: #3730a3;
}

.status-delivered {
    background: #d1fae5;
    color: #065f46;
}

.status-cancelled {
    background: #fee2e2;
    color: #991b1b;
}

.order-details {
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 2px solid #e5e7eb;
}

.order-details h2 {
    font-size: 1.5rem;
    color: #111827;
    margin: 0 0 1.5rem 0;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    padding: 0.75rem 0;
    border-bottom: 1px solid #f3f4f6;
}

.detail-row:last-child {
    border-bottom: none;
}

.action-buttons {
    margin-top: 2rem;
    display: flex;
    gap: 1rem;
}

.btn-secondary {
    padding: 0.875rem 1.5rem;
    background: #f3f4f6;
    color: #374151;
    border: none;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: background 0.2s;
}

.btn-secondary:hover {
    background: #e5e7eb;
}
</style>
@endsection

