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
</head>
<body class="min-h-screen flex flex-col bg-gray-50" style="font-family: 'Inter', sans-serif;">
    <header class="bg-white border-b">
        <div class="container mx-auto px-4 h-16 flex items-center justify-between">
            <a href="{{ url('/') }}" class="text-2xl font-bold">🛍️ Boutique</a>
            <nav class="flex items-center gap-6 text-sm">
                <a href="{{ url('/products') }}" class="hover:text-blue-600">Produits</a>
                <a href="{{ url('/cart') }}" class="hover:text-blue-600">Panier</a>
                @auth
                    <a href="{{ url('/admin') }}" class="hover:text-blue-600">Admin</a>
                    <form action="{{ url('/logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-red-600 hover:text-red-700">Déconnexion</button>
                    </form>
                @else
                    <a href="{{ url('/login') }}" class="hover:text-blue-600">Connexion</a>
                    <a href="{{ url('/register') }}" class="hover:text-blue-600">Créer un compte</a>
                @endauth
            </nav>
        </div>
    </header>

    <main class="flex-1 container mx-auto px-4 py-8">
        @yield('content')
    </main>

    <footer class="bg-white border-t py-8">
        <div class="container mx-auto px-4 text-center text-gray-500 text-sm">
            © {{ date('Y') }} Boutique Dropshipping — Tous droits réservés.
        </div>
    </footer>
</body>
</html>

