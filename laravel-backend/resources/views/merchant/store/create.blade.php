@extends('layouts.app')

@section('title', 'Créer ma boutique - Dropshipping Platform')

@section('content')
<div class="store-wizard-page">
    {{-- Affichage des erreurs Laravel --}}
    @if($errors->any())
        <div class="alert alert-danger" style="margin: 20px; padding: 15px 20px; background-color: #fee; border: 2px solid #fcc; border-radius: 8px; color: #c33; font-size: 16px; font-weight: bold;">
            <i class="fas fa-exclamation-triangle" style="margin-right: 10px;"></i>
            <strong>Erreurs détectées :</strong>
            <ul style="margin: 10px 0 0 20px; padding: 0;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger" style="margin: 20px; padding: 15px 20px; background-color: #fee; border: 2px solid #fcc; border-radius: 8px; color: #c33; font-size: 16px; font-weight: bold;">
            <i class="fas fa-exclamation-triangle" style="margin-right: 10px;"></i>
            {{ session('error') }}
        </div>
    @endif
    
    <div id="react-store-wizard" 
         data-templates='@json($templates)'
         data-questionnaire='@json($questionnaireData ?? [])'
         data-submit-url="{{ route('merchant.store.store') }}"
         data-errors='@json($errors->getMessages())'></div>
    <script src="https://unpkg.com/react@18/umd/react.production.min.js" crossorigin></script>
    <script src="https://unpkg.com/react-dom@18/umd/react-dom.production.min.js" crossorigin></script>
    <script src="{{ asset('js/react-store-wizard.js') }}" defer></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="{{ asset('js/aos-init.js') }}" defer></script>
</div>

<!-- Pop-up de félicitations -->
<div id="success-toast" class="success-toast" style="display: none;">
    <div class="toast-content">
        <i class="fas fa-check-circle toast-icon"></i>
        <div class="toast-message">
            <h3>Félicitations !</h3>
            <p>Votre boutique a été créée avec succès !</p>
        </div>
    </div>
</div>

<script src="{{ asset('js/store-create-toast.js') }}" defer></script>
@endsection

