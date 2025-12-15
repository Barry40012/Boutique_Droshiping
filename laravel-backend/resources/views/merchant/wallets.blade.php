@extends('layouts.merchant')

@section('title', 'Wallets Fournisseurs - Dashboard Merchant')

@section('content')
<div class="merchant-wallets-container">
    <div class="wallets-header" data-aos="fade-down">
        <h1 class="wallets-title">
            <i class="fas fa-wallet icon-inline"></i>
            Wallets Fournisseurs
        </h1>
        <p class="wallets-subtitle">Gérez les balances de vos fournisseurs pour l'automatisation des commandes</p>
    </div>

    <!-- Statistiques globales -->
    <div class="wallets-stats-grid" data-aos="fade-up">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Balance Totale</div>
                <div class="stat-value">${{ number_format($stats['total_balance'], 2) }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--primary), var(--secondary));">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Disponible</div>
                <div class="stat-value">${{ number_format($stats['available_balance'], 2) }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, var(--accent), #ea580c);">
                <i class="fas fa-lock"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Réservé</div>
                <div class="stat-value">${{ number_format($stats['total_reserved'], 2) }}</div>
            </div>
        </div>

        @if($stats['low_balance_count'] > 0)
        <div class="stat-card stat-card-warning">
            <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Wallets Faibles</div>
                <div class="stat-value">{{ $stats['low_balance_count'] }}</div>
            </div>
        </div>
        @endif
    </div>

    <!-- Liste des Wallets -->
    <div class="wallets-list-section" data-aos="fade-up" data-aos-delay="100">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-list icon-inline"></i>
                Mes Wallets
            </h2>
            <a href="{{ route('merchant.suppliers') }}" class="btn-secondary">
                <i class="fas fa-plus icon-inline"></i>
                Créer un wallet
            </a>
        </div>

        @if($wallets->count() > 0)
            <div class="wallets-grid">
                @foreach($wallets as $wallet)
                <div class="wallet-card {{ $wallet->isLowBalance() ? 'wallet-low-balance' : '' }}" data-aos="fade-up">
                    <div class="wallet-header">
                        <div class="wallet-supplier">
                            <i class="fas fa-truck wallet-icon"></i>
                            <div>
                                <h3 class="wallet-name">{{ $wallet->supplier->name }}</h3>
                                <p class="wallet-type">{{ ucfirst(str_replace('_', ' ', $wallet->supplier->type)) }}</p>
                            </div>
                        </div>
                        @if($wallet->isLowBalance())
                            <span class="wallet-badge badge-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                Solde faible
                            </span>
                        @endif
                    </div>

                    <div class="wallet-balance">
                        <div class="balance-item">
                            <span class="balance-label">Balance totale</span>
                            <span class="balance-value">${{ number_format($wallet->balance, 2) }}</span>
                        </div>
                        <div class="balance-item">
                            <span class="balance-label">Disponible</span>
                            <span class="balance-value balance-available">${{ number_format($wallet->availableBalance(), 2) }}</span>
                        </div>
                        <div class="balance-item">
                            <span class="balance-label">Réservé</span>
                            <span class="balance-value balance-reserved">${{ number_format($wallet->reserved_balance, 2) }}</span>
                        </div>
                    </div>

                    <div class="wallet-stats">
                        <div class="wallet-stat">
                            <i class="fas fa-arrow-down stat-icon"></i>
                            <span class="stat-text">Déposé: ${{ number_format($wallet->total_deposited, 2) }}</span>
                        </div>
                        <div class="wallet-stat">
                            <i class="fas fa-arrow-up stat-icon"></i>
                            <span class="stat-text">Dépensé: ${{ number_format($wallet->total_spent, 2) }}</span>
                        </div>
                    </div>

                    <div class="wallet-actions">
                        <button type="button" 
                                class="btn-primary btn-sm" 
                                onclick="openDepositModal({{ $wallet->id }}, '{{ $wallet->supplier->name }}')">
                            <i class="fas fa-plus icon-inline"></i>
                            Recharger
                        </button>
                        <a href="{{ route('merchant.wallets.transactions', $wallet->id) }}" class="btn-secondary btn-sm">
                            <i class="fas fa-history icon-inline"></i>
                            Historique
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="empty-state" data-aos="fade-up">
                <i class="fas fa-wallet empty-icon"></i>
                <h3>Aucun wallet créé</h3>
                <p>Créez un wallet pour un fournisseur pour activer l'automatisation des commandes.</p>
                <a href="{{ route('merchant.suppliers') }}" class="btn-primary">
                    <i class="fas fa-plus icon-inline"></i>
                    Créer mon premier wallet
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Modal Dépôt -->
<div id="depositModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Recharger le wallet</h3>
            <button type="button" class="modal-close" onclick="closeDepositModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="depositForm" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Fournisseur</label>
                    <input type="text" id="supplierName" class="form-input" readonly>
                </div>
                <div class="form-group">
                    <label class="form-label">Montant <span class="text-danger">*</span></label>
                    <input type="number" 
                           name="amount" 
                           class="form-input" 
                           step="0.01" 
                           min="1" 
                           required 
                           placeholder="0.00">
                </div>
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" 
                              class="form-input" 
                              rows="3" 
                              placeholder="Description du dépôt (optionnel)"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeDepositModal()">Annuler</button>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-check icon-inline"></i>
                    Confirmer le dépôt
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openDepositModal(walletId, supplierName) {
    document.getElementById('depositModal').style.display = 'flex';
    document.getElementById('supplierName').value = supplierName;
    document.getElementById('depositForm').action = `/merchant/wallets/${walletId}/deposit`;
}

function closeDepositModal() {
    document.getElementById('depositModal').style.display = 'none';
    document.getElementById('depositForm').reset();
}

// Fermer la modal en cliquant à l'extérieur
window.onclick = function(event) {
    const modal = document.getElementById('depositModal');
    if (event.target == modal) {
        closeDepositModal();
    }
}
</script>
@endsection

