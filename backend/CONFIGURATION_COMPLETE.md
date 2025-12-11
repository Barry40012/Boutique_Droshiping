# ✅ Configuration Complète - Étapes Finales

## 📋 Ce que vous avez déjà

Vous avez trouvé vos clés Supabase :
- ✅ **Publishable key** (anon key)
- ✅ **Secret key** (service role key)

## 🔍 Il vous manque encore

1. **Project URL** - Dans la même page Settings → API, vous devriez voir :
   ```
   Project URL: https://xxxxxxxxxxxxx.supabase.co
   ```
   → Copiez cette URL

## 📝 Créer le fichier .env.local

Dans le dossier `backend`, créez un fichier nommé `.env.local` et mettez-y :

```env
# Supabase Configuration
NEXT_PUBLIC_SUPABASE_URL=https://votre-projet-id.supabase.co
NEXT_PUBLIC_SUPABASE_ANON_KEY=sb_publishable_Z98bZc6fjNFbtY-tinx3pA_YBv3z1ny
SUPABASE_SERVICE_ROLE_KEY=sb_secret_NOms6y8V9nGnaD5WSAWAoQ_XH22oP_c

# Application
NEXT_PUBLIC_URL=http://localhost:3000

# Paiement (à remplir plus tard)
KORAPAY_PUBLIC_KEY=
KORAPAY_SECRET_KEY=

# Fournisseurs (optionnel pour commencer)
ALIEXPRESS_API_KEY=
ALIEXPRESS_API_SECRET=
```

⚠️ **Remplacez** :
- `https://votre-projet-id.supabase.co` par votre vraie Project URL
- Les clés par vos vraies clés (celles que vous avez trouvées)

## 🗄️ Exécuter les migrations SQL

1. Dans Supabase → **SQL Editor** (dans la barre latérale)
2. Cliquez sur **New query**
3. Ouvrez le fichier `supabase_migrations.sql` (à la racine du projet `E:\Boutique_Droshiping\`)
4. Copiez TOUT le contenu
5. Collez dans l'éditeur SQL de Supabase
6. Cliquez sur **Run** (ou appuyez sur F5)
7. Vérifiez qu'il n'y a pas d'erreurs
8. Allez dans **Table Editor** pour vérifier que les tables sont créées :
   - customers
   - products
   - orders
   - suppliers
   - etc.

## 🚀 Lancer le serveur

```bash
cd backend
npm install
npm run dev
```

Le serveur devrait démarrer sur `http://localhost:3000`

## ✅ Tester

Ouvrez un navigateur ou utilisez curl :
```bash
curl http://localhost:3000/api/health
```

Vous devriez voir :
```json
{"status":"ok","message":"Backend API is running","timestamp":"..."}
```

## 🔒 Sécurité - IMPORTANT

⚠️ **NE JAMAIS** :
- Commiter le fichier `.env.local` (déjà dans .gitignore ✅)
- Partager vos clés publiquement
- Mettre vos clés dans le code source

✅ **TOUJOURS** :
- Garder `.env.local` local et privé
- Utiliser des clés différentes pour production

---

**Une fois que c'est fait, votre backend est prêt ! 🎉**

