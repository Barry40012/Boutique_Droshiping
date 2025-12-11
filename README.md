# 🛍️ BOUTIQUE DROPSHIPPING - Projet Complet

## 📚 Documentation du Projet

Ce projet contient tous les documents nécessaires pour comprendre et développer votre site de dropshipping automatisé.

### **Fichiers de Documentation**

1. **`objectif_du_projet.md`** - Objectifs et besoins initiaux
2. **`RESUME_PROJET.md`** - Résumé complet du projet avec fonctionnalités et étapes
3. **`ARCHITECTURE_TECHNIQUE.md`** - Architecture détaillée, schémas et code examples
4. **`supabase_migrations.sql`** - Scripts SQL pour créer la base de données
5. **`README.md`** - Ce fichier (guide de démarrage)

---

## 🎯 Vue d'Ensemble

### **Objectif**
Créer un site de dropshipping **100% automatisé** sans Shopify, avec :
- ✅ Gestion produits et commandes
- ✅ Paiement par carte Visa (Korapay/DPO - compatible Afrique)
- ✅ Automatisation complète : paiement → fournisseur → expédition
- ✅ Intégration Facebook Ads
- ✅ Système de marge automatique

### **Stack Technologique**
- **Frontend** : Next.js + React + TypeScript + Tailwind CSS
- **Backend** : Next.js API Routes
- **Base de données** : Supabase (PostgreSQL)
- **Paiement** : Korapay ou DPO Group
- **Fournisseurs** : AliExpress API / CJ Dropshipping

---

## 🚀 Démarrage Rapide

### **Prérequis**
- Node.js 18+ installé
- Compte Supabase créé
- Compte Korapay ou DPO (pour paiements)
- Compte fournisseur (AliExpress/CJ Dropshipping)

### **Étapes d'Installation**

#### **1. Initialiser le Projet Next.js**

```bash
# Créer le projet
npx create-next-app@latest boutique-dropshipping --typescript --tailwind --app

# Aller dans le dossier
cd boutique-dropshipping

# Installer les dépendances Supabase
npm install @supabase/supabase-js @supabase/auth-helpers-nextjs

# Installer autres dépendances utiles
npm install zustand react-hook-form zod
npm install @radix-ui/react-dialog @radix-ui/react-dropdown-menu
npm install lucide-react
```

#### **2. Configurer Supabase**

1. Aller sur [supabase.com](https://supabase.com)
2. Créer un nouveau projet
3. Copier l'URL et la clé anonyme
4. Exécuter les migrations SQL :
   - Aller dans SQL Editor dans Supabase Dashboard
   - Copier le contenu de `supabase_migrations.sql`
   - Exécuter le script

#### **3. Configurer les Variables d'Environnement**

Créer un fichier `.env.local` à la racine du projet :

```env
# Supabase
NEXT_PUBLIC_SUPABASE_URL=your_supabase_url
NEXT_PUBLIC_SUPABASE_ANON_KEY=your_anon_key
SUPABASE_SERVICE_ROLE_KEY=your_service_role_key

# Application
NEXT_PUBLIC_URL=http://localhost:3000

# Paiement (Korapay ou DPO)
KORAPAY_PUBLIC_KEY=your_korapay_public_key
KORAPAY_SECRET_KEY=your_korapay_secret_key

# Fournisseur (AliExpress ou CJ Dropshipping)
ALIEXPRESS_API_KEY=your_aliexpress_key
ALIEXPRESS_API_SECRET=your_aliexpress_secret

# Facebook Pixel
NEXT_PUBLIC_FACEBOOK_PIXEL_ID=your_pixel_id
```

#### **4. Structure du Projet**

Créer la structure de dossiers suivante :

```
boutique-dropshipping/
├── src/
│   ├── app/
│   │   ├── api/
│   │   ├── products/
│   │   ├── cart/
│   │   ├── checkout/
│   │   └── admin/
│   ├── components/
│   │   ├── ui/
│   │   ├── products/
│   │   ├── cart/
│   │   └── layout/
│   ├── lib/
│   │   ├── supabase/
│   │   ├── payments/
│   │   └── suppliers/
│   ├── hooks/
│   └── types/
└── .env.local
```

#### **5. Lancer le Projet**

```bash
# Mode développement
npm run dev

# Le site sera accessible sur http://localhost:3000
```

---

## 📋 Checklist de Développement

Suivez les étapes dans `RESUME_PROJET.md` :

### **Phase 1 : Setup & Infrastructure** ✅
- [ ] Configuration Supabase
- [ ] Setup projet Next.js
- [ ] Configuration authentification

### **Phase 2 : Frontend** 
- [ ] Pages publiques
- [ ] Composants réutilisables
- [ ] Dashboard client

### **Phase 3 : Paiement**
- [ ] Intégration Korapay/DPO
- [ ] Webhooks paiement
- [ ] Système wallet fournisseur

### **Phase 4 : Automatisation Fournisseur**
- [ ] Intégration API fournisseur
- [ ] Automatisation commandes
- [ ] Import produits

### **Phase 5 : Dashboard Admin**
- [ ] Gestion produits
- [ ] Gestion commandes
- [ ] Analytics

### **Phase 6 : Marketing**
- [ ] Facebook Pixel
- [ ] SEO

### **Phase 7 : Tests & Déploiement**
- [ ] Tests complets
- [ ] Déploiement production

--- ---

## 🔑 Points Clés du Système

### **1. Automatisation Paiement → Fournisseur**

Quand un client paie :
1. Webhook reçoit confirmation paiement
2. Système crée commande dans Supabase
3. Système débite wallet fournisseur (5$)
4. Système envoie commande au fournisseur via API
5. Système paie fournisseur automatiquement
6. Marge (20$) reste dans votre compte
7. Client reçoit notification

**Tout automatique, même pendant que vous dormez !**

### **2. Système Wallet Fournisseur**

- Vous déposez de l'argent dans le wallet (ex: 1000$)
- À chaque commande, le système débite automatiquement
- Alertes si balance faible
- Historique complet des transactions

### **3. Calcul Marge Automatique**

```
Prix fournisseur : 5$
Prix vente : 25$
Marge = 20$ (automatiquement calculée)
```

---

## 🛠️ Commandes Utiles

```bash
# Développement
npm run dev

# Build production
npm run build

# Lancer production
npm start

# Linter
npm run lint

# Type checking
npm run type-check
```

---

## 📖 Ressources

### **Documentation**
- [Next.js Documentation](https://nextjs.org/docs)
- [Supabase Documentation](https://supabase.com/docs)
- [React Documentation](https://react.dev)

### **APIs Externes**
- [Korapay API](https://docs.korapay.com)
- [DPO Group API](https://doc.directpay.online)
- [AliExpress API](https://developers.aliexpress.com)
- [Facebook Pixel](https://developers.facebook.com/docs/meta-pixel)

---

## 🐛 Dépannage

### **Problème de connexion Supabase**
- Vérifier les variables d'environnement
- Vérifier que RLS est bien configuré
- Vérifier les clés API dans Supabase Dashboard

### **Problème de paiement**
- Vérifier les clés Korapay/DPO
- Vérifier que les webhooks sont bien configurés
- Vérifier les logs dans Supabase

### **Problème d'automatisation fournisseur**
- Vérifier les clés API fournisseur
- Vérifier que le wallet a suffisamment de balance
- Vérifier les logs d'erreur

---

## 📞 Support

Pour toute question ou problème :
1. Consulter la documentation dans les fichiers `.md`
2. Vérifier les logs d'erreur
3. Consulter la documentation des APIs externes

---

## 📝 Notes Importantes

- ⚠️ **Sécurité** : Ne jamais commiter les fichiers `.env.local`
- ⚠️ **Balance Wallet** : Toujours maintenir une balance suffisante
- ⚠️ **Tests** : Tester en mode développement avant production
- ⚠️ **Backup** : Faire des backups réguliers de la base de données

---

## 🎉 Prochaines Étapes

1. ✅ Lire `RESUME_PROJET.md` pour comprendre le projet
2. ✅ Lire `ARCHITECTURE_TECHNIQUE.md` pour les détails techniques
3. ✅ Configurer Supabase avec `supabase_migrations.sql`
4. ⏭️ Commencer le développement Phase 1

**Bon développement ! 🚀**

