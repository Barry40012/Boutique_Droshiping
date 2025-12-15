@extends('layouts.app')

@section('title', 'Créer ma boutique - Questionnaire')

@section('content')
<div class="questionnaire-page">
    <div class="questionnaire-container" data-aos="fade-up">
        <div class="questionnaire-header">
            <div class="questionnaire-illustration" data-aos="zoom-in">
                <i class="fas fa-store"></i>
            </div>
            <h1 class="questionnaire-title" data-aos="fade-up">
                Créez votre boutique en quelques étapes
            </h1>
            <p class="questionnaire-subtitle" data-aos="fade-up" data-aos-delay="100">Nous avons besoin de quelques informations pour personnaliser votre expérience</p>
        </div>

        <!-- Message plan Free -->
        <div class="free-plan-banner" data-aos="fade-down">
            <div class="banner-content">
                <i class="fas fa-gift banner-icon"></i>
                <div>
                    <h3>Vous êtes actuellement sur la version Free</h3>
                    <p>Découvrez toutes les fonctionnalités de la plateforme. Vous pouvez migrer vers la version Pro pour bénéficier de plus de fonctionnalités.</p>
                </div>
            </div>
        </div>

        <form action="{{ route('merchant.store.create') }}" method="GET" class="questionnaire-form" data-aos="fade-up">
            <!-- Expérience -->
            <div class="question-section">
                <h2 class="question-title">
                    <i class="fas fa-user-graduate icon-inline"></i>
                    Quelle est votre expérience en dropshipping ?
                </h2>
                <div class="options-grid">
                    <label class="option-card">
                        <input type="radio" name="experience_level" value="beginner" required>
                        <div class="option-content">
                            <i class="fas fa-seedling option-icon"></i>
                            <h3>Débutant</h3>
                            <p>Je découvre le dropshipping</p>
                        </div>
                    </label>
                    <label class="option-card">
                        <input type="radio" name="experience_level" value="intermediate" required>
                        <div class="option-content">
                            <i class="fas fa-chart-line option-icon"></i>
                            <h3>Intermédiaire</h3>
                            <p>J'ai déjà vendu quelques produits</p>
                        </div>
                    </label>
                    <label class="option-card">
                        <input type="radio" name="experience_level" value="professional" required>
                        <div class="option-content">
                            <i class="fas fa-trophy option-icon"></i>
                            <h3>Professionnel</h3>
                            <p>J'ai une expérience solide</p>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Pays -->
            <div class="question-section">
                <h2 class="question-title">
                    <i class="fas fa-globe icon-inline"></i>
                    Dans quel pays êtes-vous ?
                </h2>
                <div class="form-group">
                    <select name="country" id="country-select" class="form-input country-select" required>
                        <option value="">Sélectionnez votre pays</option>
                    </select>
                    <div id="country-flag-display" class="country-flag-display"></div>
                </div>
            </div>

            <!-- Devise -->
            <div class="question-section">
                <h2 class="question-title">
                    <i class="fas fa-coins icon-inline"></i>
                    Quelle devise souhaitez-vous utiliser ?
                </h2>
                <div class="form-group">
                    <select name="currency" id="currency-select" class="form-input currency-select" required>
                        <option value="">Sélectionnez une devise</option>
                    </select>
                    <div id="currency-display" class="currency-display"></div>
                    <input type="hidden" id="currency-rate" name="currency_rate" value="1">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary btn-large">
                    <i class="fas fa-arrow-right icon-inline"></i>
                    Continuer vers la création de boutique
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src="{{ asset('js/questionnaire.js') }}" defer></script>
@endsection

