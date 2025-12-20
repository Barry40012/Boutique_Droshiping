@extends('layouts.app')

@section('title', 'Gestion des abonnements')

@section('content')
<div class="container" style="max-width: 1200px; margin: 2rem auto; padding: 0 1rem;">
    <div class="page-header" style="margin-bottom: 2rem;">
        <h1 style="font-size: 2rem; font-weight: 700; color: #1f2937; margin: 0;">
            <i class="fas fa-credit-card icon-inline"></i>
            Gestion des abonnements
        </h1>
        <p style="color: #6b7280; margin-top: 0.5rem;">Consultez votre plan actuel et gérez votre abonnement</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="padding: 1rem; background: #d1fae5; color: #065f46; border-radius: 8px; margin-bottom: 2rem;">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error" style="padding: 1rem; background: #fee2e2; color: #991b1b; border-radius: 8px; margin-bottom: 2rem;">
            <i class="fas fa-exclamation-circle"></i>
            {{ session('error') }}
        </div>
    @endif

    <!-- Plan actuel -->
    <div class="current-plan-section" style="background: white; border: 2px solid #e5e7eb; border-radius: 12px; padding: 2rem; margin-bottom: 2rem;">
        <h2 style="font-size: 1.5rem; font-weight: 600; color: #1f2937; margin: 0 0 1.5rem 0;">
            Votre plan actuel
        </h2>
        
        @if($currentPlan)
            <div class="plan-badge" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: linear-gradient(135deg, #0ea5e9, #8b5cf6); color: white; border-radius: 8px; font-weight: 600; font-size: 0.95rem; margin-bottom: 1.5rem;">
                <i class="fas fa-crown"></i>
                {{ $currentPlan->name ?? 'Plan Pro' }}
            </div>
            
            @if($activeSubscription)
                <div style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: #d1fae5; color: #065f46; border-radius: 8px; font-size: 0.875rem; font-weight: 600; margin-left: 1rem;">
                    <i class="fas fa-check-circle"></i>
                    Actif
                </div>
            @endif

            <div class="plan-details" style="margin-top: 1.5rem;">
                @if($activeSubscription)
                    <div style="display: flex; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px solid #f3f4f6;">
                        <span style="color: #6b7280; font-size: 0.9rem;">Date de début :</span>
                        <span style="color: #1f2937; font-weight: 600;">{{ $activeSubscription->created_at->format('d/m/Y') }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px solid #f3f4f6;">
                        <span style="color: #6b7280; font-size: 0.9rem;">Date d'expiration :</span>
                        <span style="color: #1f2937; font-weight: 600;">{{ $activeSubscription->ends_at ? $activeSubscription->ends_at->format('d/m/Y') : 'Illimité' }}</span>
                    </div>
                @endif
                <div style="display: flex; justify-content: space-between; padding: 0.75rem 0;">
                    <span style="color: #6b7280; font-size: 0.9rem;">Prix :</span>
                    <span style="color: #1f2937; font-weight: 600;">{{ number_format($currentPlan->price ?? 0, 2) }} {{ $currentPlan->currency ?? 'EUR' }}/mois</span>
                </div>
            </div>

            @php
                $features = $currentPlan->features ?? [];
                if (empty($features) && $currentPlan->slug === 'pro') {
                    $features = [
                        'Boutiques illimitées',
                        'Produits illimités',
                        'Support prioritaire',
                        'Analytiques avancées',
                        'Domaines personnalisés',
                        'Thèmes premium'
                    ];
                }
            @endphp

            @if(!empty($features))
                <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #e5e7eb;">
                    <h3 style="font-size: 1rem; font-weight: 600; color: #374151; margin: 0 0 1rem 0;">Fonctionnalités incluses</h3>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 0.75rem;">
                        @foreach($features as $feature)
                            <div style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem; background: #f9fafb; border-radius: 6px;">
                                <i class="fas fa-check-circle" style="color: #10b981; font-size: 1rem;"></i>
                                <span style="color: #374151; font-size: 0.9rem;">{{ $feature }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @else
            <div class="plan-badge free" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: linear-gradient(135deg, #6b7280, #9ca3af); color: white; border-radius: 8px; font-weight: 600; font-size: 0.95rem; margin-bottom: 1.5rem;">
                <i class="fas fa-gift"></i>
                Plan Gratuit
            </div>
            
            <div style="margin-top: 1.5rem; padding: 1.5rem; background: #f9fafb; border-radius: 8px;">
                <h3 style="font-size: 1rem; font-weight: 600; color: #374151; margin: 0 0 1rem 0;">Fonctionnalités du plan gratuit</h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 0.75rem;">
                    <div style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem; background: white; border-radius: 6px;">
                        <i class="fas fa-check-circle" style="color: #10b981; font-size: 1rem;"></i>
                        <span style="color: #374151; font-size: 0.9rem;">1 boutique</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem; background: white; border-radius: 6px;">
                        <i class="fas fa-check-circle" style="color: #10b981; font-size: 1rem;"></i>
                        <span style="color: #374151; font-size: 0.9rem;">Jusqu'à 10 produits</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem; background: white; border-radius: 6px;">
                        <i class="fas fa-check-circle" style="color: #10b981; font-size: 1rem;"></i>
                        <span style="color: #374151; font-size: 0.9rem;">Support par email</span>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Plans disponibles -->
    @if($plans && $plans->count() > 0)
        <div class="available-plans-section" style="background: white; border: 2px solid #e5e7eb; border-radius: 12px; padding: 2rem;">
            <h2 style="font-size: 1.5rem; font-weight: 600; color: #1f2937; margin: 0 0 1.5rem 0;">
                Plans disponibles
            </h2>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
                @foreach($plans as $plan)
                    <div class="plan-card" style="border: 2px solid #e5e7eb; border-radius: 12px; padding: 1.5rem; transition: all 0.3s;">
                        <div style="margin-bottom: 1rem;">
                            <h3 style="font-size: 1.25rem; font-weight: 700; color: #1f2937; margin: 0 0 0.5rem 0;">{{ $plan->name }}</h3>
                            <div style="font-size: 2rem; font-weight: 700; color: #0ea5e9;">
                                {{ number_format($plan->price ?? 0, 2) }} {{ $plan->currency ?? 'EUR' }}
                                <span style="font-size: 1rem; color: #6b7280; font-weight: 400;">/mois</span>
                            </div>
                        </div>
                        
                        @php
                            $planFeatures = $plan->features ?? [];
                        @endphp
                        
                        @if(!empty($planFeatures))
                            <ul style="list-style: none; padding: 0; margin: 1.5rem 0;">
                                @foreach($planFeatures as $feature)
                                    <li style="display: flex; align-items: center; gap: 0.75rem; padding: 0.5rem 0; color: #374151;">
                                        <i class="fas fa-check" style="color: #10b981;"></i>
                                        <span>{{ $feature }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                        
                        <button type="button" 
                                class="btn-primary" 
                                style="width: 100%; padding: 0.875rem; background: #0ea5e9; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; margin-top: 1rem;"
                                onclick="alert('Fonctionnalité de souscription à implémenter')">
                            Choisir ce plan
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div style="background: white; border: 2px solid #e5e7eb; border-radius: 12px; padding: 2rem; text-align: center;">
            <p style="color: #6b7280;">Aucun plan disponible pour le moment.</p>
        </div>
    @endif
</div>
@endsection

