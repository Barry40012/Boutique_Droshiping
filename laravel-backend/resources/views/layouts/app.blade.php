<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Boutique Dropshipping')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
</head>
<body class="page">
    <header class="topbar">
        <div class="container topbar-inner">
            <a href="{{ url('/') }}" class="brand">
                <span class="brand-icon">🛍️</span>
                <span class="brand-text">Boutique</span>
            </a>
            <nav class="nav">
                <a href="{{ url('/products') }}" class="nav-link">Produits</a>
                <a href="{{ url('/cart') }}" class="nav-link">Panier</a>
                @auth
                    <a href="{{ url('/admin') }}" class="nav-link">Admin</a>
                    <form action="{{ url('/logout') }}" method="POST" class="nav-inline-form">
                        @csrf
                        <button type="submit" class="nav-link danger">Déconnexion</button>
                    </form>
                @else
                    <a href="{{ url('/login') }}" class="nav-link">Connexion</a>
                    <a href="{{ url('/register') }}" class="nav-link">Créer un compte</a>
                @endauth
            </nav>
        </div>
    </header>

    <main class="container main">
        @yield('content')
    </main>

    <footer class="footer">
        <div class="container text-center">
            © {{ date('Y') }} Boutique Dropshipping — Tous droits réservés.
        </div>
    </footer>
</body>
</html>

