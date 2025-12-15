<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Symfony\Component\HttpFoundation\Response;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): string|null
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user(),
            ],
        ]);
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return Response
     */
    public function handle(Request $request, \Closure $next): Response
    {
        // Si c'est une requête AJAX qui attend du JSON, ne pas utiliser Inertia
        if ($request->wantsJson() || 
            $request->ajax() || 
            $request->header('X-Requested-With') === 'XMLHttpRequest' ||
            $request->header('Accept') === 'application/json') {
            // Laisser passer la requête sans transformation Inertia
            return $next($request);
        }

        // Pour les requêtes POST de formulaire (création de boutique), 
        // laisser passer sans Inertia pour permettre la redirection normale
        if ($request->isMethod('POST') && $request->routeIs('merchant.store.store')) {
            \Log::info('Bypassing Inertia for store creation form submission');
            return $next($request);
        }

        return parent::handle($request, $next);
    }
}

