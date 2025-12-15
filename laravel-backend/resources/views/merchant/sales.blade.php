@extends('layouts.merchant')

@section('title', 'Ventes - Dashboard Merchant')

@section('content')
<div class="merchant-sales-container">
    <div class="sales-header" data-aos="fade-down">
        <h1 class="sales-title">
            <i class="fas fa-chart-line icon-inline"></i>
            Ventes
        </h1>
        <p class="sales-subtitle">Suivez vos ventes et performances</p>
    </div>

    <!-- Statistiques de ventes -->
    <div class="sales-stats-grid" data-aos="fade-up">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--primary), var(--secondary));">
                <i class="fas fa-calendar-day"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Aujourd'hui</div>
                <div class="stat-value">${{ number_format($salesStats['today'], 2) }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--accent), #ea580c);">
                <i class="fas fa-calendar-week"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Cette semaine</div>
                <div class="stat-value">${{ number_format($salesStats['week'], 2) }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Ce mois</div>
                <div class="stat-value">${{ number_format($salesStats['month'], 2) }}</div>
            </div>
        </div>
    </div>

    <!-- Ventes récentes -->
    <div class="recent-sales-section" data-aos="fade-up" data-aos-delay="100">
        <h2 class="section-title">
            <i class="fas fa-history icon-inline"></i>
            Ventes récentes
        </h2>
        
        @if($recentSales->count() > 0)
            <div class="sales-table-container">
                <table class="sales-table">
                    <thead>
                        <tr>
                            <th>ID Commande</th>
                            <th>Date</th>
                            <th>Montant</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentSales as $sale)
                        <tr>
                            <td>#{{ substr($sale->id, 0, 8) }}</td>
                            <td>{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                            <td class="amount">${{ number_format($sale->total_amount, 2) }}</td>
                            <td>
                                <span class="status-badge status-{{ $sale->payment_status }}">
                                    {{ ucfirst($sale->payment_status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('merchant.orders.show', $sale->id) }}" class="btn-link">
                                    <i class="fas fa-eye"></i>
                                    Voir
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-chart-line empty-icon"></i>
                <h3>Aucune vente pour le moment</h3>
                <p>Vos ventes apparaîtront ici une fois que vous commencerez à recevoir des commandes.</p>
            </div>
        @endif
    </div>
</div>
@endsection

