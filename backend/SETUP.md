# 🚀 Guide de Configuration du Backend

## 📋 Prérequis

- Node.js 18+ installé
- Compte Supabase créé
- Compte Korapay (ou DPO) pour les paiements
- Compte fournisseur (AliExpress ou CJ Dropshipping)

---

## 🔧 Installation

### 1. Installer les dépendances

```bash
cd backend
npm install
```

### 2. Configurer les variables d'environnement

Créer un fichier `.env.local` à la racine du dossier `backend` :

```env
# Supabase
NEXT_PUBLIC_SUPABASE_URL=https://votre-projet.supabase.co
NEXT_PUBLIC_SUPABASE_ANON_KEY=votre_anon_key
SUPABASE_SERVICE_ROLE_KEY=votre_service_role_key

# Application
NEXT_PUBLIC_URL=http://localhost:3000

# Paiement Korapay
KORAPAY_PUBLIC_KEY=votre_public_key
KORAPAY_SECRET_KEY=votre_secret_key

# Fournisseurs (optionnel pour commencer)
ALIEXPRESS_API_KEY=votre_aliexpress_key
ALIEXPRESS_API_SECRET=votre_aliexpress_secret
```

### 3. Configurer Supabase

1. Aller sur [supabase.com](https://supabase.com)
2. Créer un nouveau projet ou utiliser un existant
3. Dans le SQL Editor, exécuter le script `supabase_migrations.sql` (à la racine du projet)
4. Copier l'URL et les clés dans `.env.local`

### 4. Lancer le serveur

```bash
npm run dev
```

Le serveur sera accessible sur `http://localhost:3000`

---

## ✅ Vérification

### Tester l'API

```bash
# Health check
curl http://localhost:3000/api/health

# Devrait retourner :
# {"status":"ok","message":"Backend API is running","timestamp":"..."}
```

### Tester avec un client HTTP

Utiliser Postman, Insomnia, ou Thunder Client (VS Code) pour tester les endpoints.

---

## 📦 Structure du Projet

```
backend/
├── app/
│   ├── api/              # Routes API
│   │   ├── products/     # Gestion produits
│   │   ├── orders/       # Gestion commandes
│   │   ├── payments/     # Paiements
│   │   ├── webhooks/     # Webhooks
│   │   ├── suppliers/    # Fournisseurs
│   │   └── stats/        # Statistiques
│   └── page.tsx          # Page d'accueil
├── lib/
│   ├── supabase/         # Client Supabase
│   ├── payments/         # Intégration paiements
│   ├── automation/       # Automatisation
│   └── utils/            # Utilitaires
├── types/                # Types TypeScript
└── middleware.ts         # Middleware CORS
```

---

## 🔐 Sécurité

- ⚠️ **Ne jamais commiter** le fichier `.env.local`
- ⚠️ Utiliser `SUPABASE_SERVICE_ROLE_KEY` uniquement côté serveur
- ⚠️ Valider toutes les entrées utilisateur
- ⚠️ Implémenter l'authentification pour les routes sensibles

---

## 🐛 Dépannage

### Erreur de connexion Supabase
- Vérifier que les variables d'environnement sont correctes
- Vérifier que le projet Supabase est actif
- Vérifier que les migrations SQL ont été exécutées

### Erreur de paiement Korapay
- Vérifier les clés API Korapay
- Vérifier que le compte Korapay est actif
- Vérifier les logs pour plus de détails

### Erreur d'automatisation
- Vérifier que le wallet fournisseur a suffisamment de balance
- Vérifier les clés API fournisseur
- Vérifier les logs dans la console

---

## 📝 Prochaines Étapes

1. ✅ Backend initialisé
2. ⏭️ Créer le frontend
3. ⏭️ Connecter frontend et backend
4. ⏭️ Tester le flux complet
5. ⏭️ Déployer en production

---

**Le backend est prêt ! 🎉**

