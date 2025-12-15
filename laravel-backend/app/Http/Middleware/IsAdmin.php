<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Vérifier si l'utilisateur est authentifié
        if (!Auth::check()) {
            return redirect('/login');
        }

        // Vérifier si l'utilisateur est admin
        // Pour l'instant, on vérifie par email (vous pouvez ajouter un champ is_admin dans la table users)
        // Ajoutez ADMIN_EMAILS=votre@email.com dans votre fichier .env
        $adminEmails = env('ADMIN_EMAILS', '');
        $adminEmailsArray = !empty($adminEmails) ? explode(',', $adminEmails) : [];
        $userEmail = Auth::user()->email;

        if (!in_array($userEmail, $adminEmailsArray)) {
            // Si l'utilisateur n'est pas admin, rediriger vers le dashboard marchand
            if (Auth::user()->store) {
                return redirect('/merchant/dashboard');
            }
            return redirect('/merchant/store/questionnaire');
        }

        return $next($request);
    }
}

