@extends('layouts.merchant')

@section('title', 'Historique Transactions - Dashboard Merchant')

@section('content')
<div class="merchant-transactions-container">
    <div class="transactions-header" data-aos="fade-down">
        <div>
            <h1 class="transactions-title">
                <i class="fas fa-history icon-inline"></i>
                Historique des Transactions
            </h1>
            <p class="transactions-subtitle">{{ $wallet->supplier->name }}</p>
        </div>
        <a href="{{ route('merchant.wallets') }}" class="btn-secondary">
            <i class="fas fa-arrow-left icon-inline"></i>
            Retour aux wallets
        </a>
    </div>

    <!-- Informations du Wallet -->
    <div class="wallet-summary-card" data-aos="fade-up">
        <div class="summary-item">
            <span class="summary-label">Balance totale</span>
            <span class="summary-value">${{ number_format($wallet->balance, 2) }}</span>
        </div>
        <div class="summary-item">
            <span class="summary-label">Disponible</span>
            <span class="summary-value summary-available">${{ number_format($wallet->availableBalance(), 2) }}</span>
        </div>
        <div class="summary-item">
            <span class="summary-label">Réservé</span>
            <span class="summary-value summary-reserved">${{ number_format($wallet->reserved_balance, 2) }}</span>
        </div>
    </div>

    <!-- Liste des Transactions -->
    <div class="transactions-section" data-aos="fade-up" data-aos-delay="100">
        <h2 class="section-title">Toutes les transactions</h2>
        
        @if($transactions->count() > 0)
            <div class="transactions-table-container">
                <table class="transactions-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Montant</th>
                            <th>Balance avant</th>
                            <th>Balance après</th>
                            <th>Statut</th>
                            <th>Description</th>
                            <th>Référence</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $transaction)
                        <tr>
                            <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <span class="transaction-type type-{{ $transaction->type }}">
                                    @if($transaction->type === 'deposit')
                                        <i class="fas fa-arrow-down"></i> Dépôt
                                    @elseif($transaction->type === 'payment')
                                        <i class="fas fa-arrow-up"></i> Paiement
                                    @elseif($transaction->type === 'withdrawal')
                                        <i class="fas fa-minus"></i> Retrait
                                    @else
                                        <i class="fas fa-exchange-alt"></i> {{ ucfirst($transaction->type) }}
                                    @endif
                                </span>
                            </td>
                            <td class="transaction-amount amount-{{ $transaction->type }}">
                                @if($transaction->type === 'deposit')
                                    +${{ number_format($transaction->amount, 2) }}
                                @else
                                    -${{ number_format($transaction->amount, 2) }}
                                @endif
                            </td>
                            <td>${{ number_format($transaction->balance_before, 2) }}</td>
                            <td>${{ number_format($transaction->balance_after, 2) }}</td>
                            <td>
                                <span class="status-badge status-{{ $transaction->status }}">
                                    {{ ucfirst($transaction->status) }}
                                </span>
                            </td>
                            <td>{{ $transaction->description ?? '-' }}</td>
                            <td>
                                @if($transaction->reference)
                                    <code class="reference-code">{{ $transaction->reference }}</code>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="pagination-container">
                {{ $transactions->links() }}
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-history empty-icon"></i>
                <h3>Aucune transaction</h3>
                <p>Les transactions apparaîtront ici une fois que vous commencerez à utiliser le wallet.</p>
            </div>
        @endif
    </div>
</div>
@endsection

