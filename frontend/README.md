# 🎨 Frontend - Boutique Dropshipping

## 🚀 Installation

```bash
cd frontend
npm install
```

## ⚙️ Configuration

1. Créer un fichier `.env.local` :
```env
NEXT_PUBLIC_API_URL=http://localhost:3000/api
NEXT_PUBLIC_FACEBOOK_PIXEL_ID=your_pixel_id
```

2. Lancer le serveur :
```bash
npm run dev
```

Le frontend sera accessible sur `http://localhost:3001`

## 📁 Structure

```
frontend/
├── src/
│   ├── app/              # Pages Next.js
│   │   ├── products/     # Pages produits
│   │   ├── cart/         # Page panier
│   │   ├── checkout/      # Page checkout
│   │   └── admin/        # Dashboard admin
│   ├── components/       # Composants React
│   ├── lib/              # Utilitaires et API
│   ├── store/            # State management (Zustand)
│   └── types/            # Types TypeScript
```

## 🎯 Fonctionnalités

✅ **Pages Publiques**
- Home avec produits populaires
- Liste produits
- Détail produit
- Panier
- Checkout

✅ **Dashboard Admin**
- Gestion produits
- Gestion commandes
- Gestion fournisseurs
- Statistiques

✅ **Intégrations**
- Facebook Pixel
- API Backend
- Panier persistant

## 🔗 Connexion Backend

Le frontend se connecte au backend via `NEXT_PUBLIC_API_URL`.
Assurez-vous que le backend tourne sur le port 3000.

