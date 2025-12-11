# Backend API - Boutique Dropshipping

## 🚀 Installation

```bash
# Installer les dépendances
npm install

# Créer le fichier .env.local
cp .env.example .env.local

# Remplir les variables d'environnement dans .env.local
```

## 📝 Variables d'Environnement

Créer un fichier `.env.local` avec :

```env
NEXT_PUBLIC_SUPABASE_URL=your_supabase_url
NEXT_PUBLIC_SUPABASE_ANON_KEY=your_anon_key
SUPABASE_SERVICE_ROLE_KEY=your_service_role_key

# Paiement
KORAPAY_PUBLIC_KEY=your_korapay_public_key
KORAPAY_SECRET_KEY=your_korapay_secret_key

# Fournisseurs
ALIEXPRESS_API_KEY=your_aliexpress_key
ALIEXPRESS_API_SECRET=your_aliexpress_secret
```

## 🏃 Lancer le serveur

```bash
# Mode développement
npm run dev

# Le serveur sera sur http://localhost:3000
```

## 📁 Structure

```
backend/
├── app/
│   ├── api/          # Routes API
│   └── page.tsx      # Page d'accueil
├── lib/
│   └── supabase/     # Configuration Supabase
└── types/            # Types TypeScript
```

## 🔗 Endpoints API

Voir le fichier `API_ROUTES.md` pour la documentation complète.

### Endpoints principaux :

- `GET /api/health` - Vérifier le statut de l'API
- `GET /api/products` - Liste des produits
- `POST /api/products` - Créer un produit
- `GET /api/orders` - Liste des commandes
- `POST /api/orders` - Créer une commande
- `POST /api/payments/create` - Créer un paiement
- `POST /api/webhooks/payment` - Webhook paiement
- `GET /api/suppliers` - Liste des fournisseurs
- `GET /api/stats` - Statistiques

## 🚀 Fonctionnalités Implémentées

✅ **Gestion Produits** - CRUD complet
✅ **Gestion Commandes** - Création et suivi
✅ **Paiements Korapay** - Intégration complète
✅ **Webhooks** - Automatisation paiements
✅ **Automatisation Fournisseurs** - Paiement automatique
✅ **Wallet Fournisseur** - Gestion balance
✅ **Statistiques** - Dashboard analytics

## 🔄 Flux Automatique

1. Client crée une commande → `POST /api/orders`
2. Client initie le paiement → `POST /api/payments/create`
3. Korapay notifie le paiement → `POST /api/webhooks/payment`
4. Système automatise :
   - Débite wallet fournisseur
   - Crée commande chez fournisseur
   - Paiement automatique fournisseur
   - Marge enregistrée

