import React from 'react'

export function Layout({ children }) {
  return (
    <div className="page">
      <header className="topbar">
        <div className="container topbar-inner">
          <a href="/" className="brand">
            <span className="brand-icon">🛍️</span>
            <span className="brand-text">Boutique</span>
          </a>
          <nav className="nav">
            <a href="/products" className="nav-link">Produits</a>
            <a href="/cart" className="nav-link">Panier</a>
            <a href="/login" className="nav-link">Connexion</a>
            <a href="/register" className="nav-link">Créer un compte</a>
          </nav>
        </div>
      </header>
      <main className="container main">
        {children}
      </main>
      <footer className="footer">
        <div className="container text-center">
          © {new Date().getFullYear()} Boutique Dropshipping — Tous droits réservés.
        </div>
      </footer>
    </div>
  )
}

