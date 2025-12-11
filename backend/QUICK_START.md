# ⚡ Démarrage Rapide - Backend

## 🎯 Configuration en 3 étapes

### Étape 1 : Récupérer les clés Supabase

1. Allez sur [supabase.com](https://supabase.com) → Votre projet
2. **Settings** (⚙️) → **API** (PAS Database !)
3. Copiez ces 3 valeurs :
   - **Project URL** 
   - **anon public** key
   - **service_role** key (cliquez "Reveal" pour la voir)

### Étape 2 : Créer le fichier .env.local

Dans le dossier `backend`, créez un fichier nommé `.env.local` :

```env
# Collez VOS valeurs ici (remplacez les exemples)
NEXT_PUBLIC_SUPABASE_URL=https://votre-projet.supabase.co
NEXT_PUBLIC_SUPABASE_ANON_KEY=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...
SUPABASE_SERVICE_ROLE_KEY=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...

NEXT_PUBLIC_URL=http://localhost:3000
```

### Étape 3 : Installer et lancer

```bash
cd backend
npm install
npm run dev
```

✅ Le serveur démarre sur `http://localhost:3000`

---

## 🗄️ Exécuter les migrations SQL

1. Dans Supabase → **SQL Editor**
2. **New query**
3. Copiez tout le contenu de `supabase_migrations.sql` (à la racine du projet)
4. **Run** (ou F5)
5. Vérifiez dans **Table Editor** que les tables sont créées

---

## ✅ Tester

```bash
# Dans un autre terminal
curl http://localhost:3000/api/health
```

Devrait retourner :
```json
{"status":"ok","message":"Backend API is running","timestamp":"..."}
```

---

## 🆘 Problème ?

- **"Missing Supabase environment variables"** 
  → Vérifiez que `.env.local` existe et contient les bonnes valeurs

- **"Invalid API key"**
  → Vérifiez que vous avez copié les clés complètes (elles sont très longues !)

- **"Table does not exist"**
  → Exécutez les migrations SQL dans Supabase

---

**C'est tout ! 🚀**

