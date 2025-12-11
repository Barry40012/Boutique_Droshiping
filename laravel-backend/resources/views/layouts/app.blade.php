<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Boutique Dropshipping')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.4.1/dist/tailwind.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #1d4ed8; /* bleu */
            --secondary: #f97316; /* orange */
            --dark: #0f172a; /* noir bleuté */
        }
    </style>
</head>
<body class="min-h-screen flex flex-col bg-slate-50" style="font-family: 'Inter', sans-serif;">
    <header class="bg-white border-b shadow-sm">
        <div class="container mx-auto px-4 h-16 flex items-center justify-between">
            <a href="{{ url('/') }}" class="text-2xl font-bold flex items-center gap-2">
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-[var(--primary)] text-white">🛍️</span>
                <span class="text-slate-900">Boutique</span>
            </a>
            <nav class="flex items-center gap-6 text-sm text-slate-600">
                <a href="{{ url('/products') }}" class="hover:text-[var(--primary)]">Produits</a>
                <a href="{{ url('/cart') }}" class="hover:text-[var(--primary)]">Panier</a>
                @auth
                    <a href="{{ url('/admin') }}" class="hover:text-[var(--primary)]">Admin</a>
                    <form action="{{ url('/logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-red-600 hover:text-red-700">Déconnexion</button>
                    </form>
                @else
                    <a href="{{ url('/login') }}" class="hover:text-[var(--primary)]">Connexion</a>
                    <a href="{{ url('/register') }}" class="hover:text-[var(--primary)]">Créer un compte</a>
                @endauth
            </nav>
        </div>
    </header>

    <main class="flex-1 container mx-auto px-4 py-10">
        @yield('content')
    </main>

    <footer class="bg-slate-900 text-white py-8">
        <div class="container mx-auto px-4 text-center text-sm">
            © {{ date('Y') }} Boutique Dropshipping — Tous droits réservés.
        </div>
    </footer>
</body>
</html>

