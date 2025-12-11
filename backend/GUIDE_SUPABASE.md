# 🔐 Guide Configuration Supabase

## 📍 Où trouver vos clés Supabase

### 1. Aller sur votre projet Supabase
- Connectez-vous sur [supabase.com](https://supabase.com)
- Sélectionnez votre projet

### 2. Récupérer les clés API (PAS la connection string PostgreSQL !)

⚠️ **IMPORTANT** : Vous êtes peut-être dans la section "Database" → "Connection string"
👉 **Ce n'est PAS ce qu'il nous faut !**

**Ce qu'il nous faut :**
- Allez dans **Settings** (⚙️) dans la barre latérale gauche
- Cliquez sur **API** dans le menu Settings
- Vous verrez une section **Project API keys** avec :

  - **Project URL** 
    ```
    https://xxxxxxxxxxxxx.supabase.co
    ```
    → C'est votre `NEXT_PUBLIC_SUPABASE_URL`

  - **anon public** key (clé publique)
    ```
    eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6...
    ```
    → C'est votre `NEXT_PUBLIC_SUPABASE_ANON_KEY`

  - **service_role** key (clé secrète - à révéler)
    ```
    eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6...
    ```
    → C'est votre `SUPABASE_SERVICE_ROLE_KEY`
    ⚠️ Cliquez sur "Reveal" pour voir cette clé (elle est secrète !)

### 3. Créer le fichier .env.local

Dans le dossier `backend`, créez un fichier `.env.local` et copiez-y :

```env
NEXT_PUBLIC_SUPABASE_URL=https://xxxxxxxxxxxxx.supabase.co
NEXT_PUBLIC_SUPABASE_ANON_KEY=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...
SUPABASE_SERVICE_ROLE_KEY=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...
```

⚠️ **IMPORTANT** : Remplacez par VOS vraies valeurs !

### 4. Exécuter les migrations SQL

1. Dans Supabase, allez dans **SQL Editor**
2. Créez une nouvelle requête
3. Copiez tout le contenu du fichier `supabase_migrations.sql` (à la racine du projet)
4. Exécutez le script
5. Vérifiez que toutes les tables sont créées dans **Table Editor**

### 5. Vérifier la connexion

Lancez le serveur :
```bash
cd backend
npm install
npm run dev
```

Testez :
```bash
curl http://localhost:3000/api/health
```

Si ça fonctionne, c'est bon ! ✅

---

## 🔒 Sécurité

- ❌ **NE JAMAIS** commiter le fichier `.env.local` (il est déjà dans `.gitignore`)
- ❌ **NE JAMAIS** partager vos clés publiquement
- ✅ Gardez vos clés privées et locales
- ✅ Utilisez des variables d'environnement différentes pour production

---

## 🆘 Problèmes courants

### "Missing Supabase environment variables"
→ Vérifiez que `.env.local` existe et contient les bonnes valeurs

### "Invalid API key"
→ Vérifiez que vous avez copié les clés complètes (elles sont longues !)

### "Table does not exist"
→ Exécutez les migrations SQL dans Supabase

---

**C'est tout ! Vous n'avez pas besoin de me donner vos clés. Gardez-les privées ! 🔐**

