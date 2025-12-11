# 📍 Où Trouver les Valeurs pour .env.local

## 🎯 Dans Supabase Dashboard

### Étape 1 : Aller dans Settings → API

1. Connectez-vous sur [supabase.com](https://supabase.com)
2. Sélectionnez votre projet
3. Dans la barre latérale gauche, cliquez sur **Settings** (⚙️)
4. Cliquez sur **API** dans le menu

### Étape 2 : Copier les 3 valeurs

Sur la page API, vous verrez :

---

## 1️⃣ NEXT_PUBLIC_SUPABASE_URL

**Où :** En haut de la page, section **Project URL**

Vous verrez quelque chose comme :
```
Project URL
https://pgrnzjpkepppikdsuheg.supabase.co
```

👉 **Copiez cette URL complète** (commence par `https://` et finit par `.supabase.co`)

---

## 2️⃣ NEXT_PUBLIC_SUPABASE_ANON_KEY

**Où :** Section **Publishable key**

Vous avez déjà trouvé cette valeur :
```
sb_publishable_Z98bZc6fjNFbtY-tinx3pA_YBv3z1ny
```

👉 **Copiez cette clé complète** (commence par `sb_publishable_`)

---

## 3️⃣ SUPABASE_SERVICE_ROLE_KEY

**Où :** Section **Secret keys** → Cliquez sur "Reveal" pour voir la clé

Vous avez déjà trouvé cette valeur :
```
sb_secret_NOms6y8V9nGnaD5WSAWAoQ_XH22oP_c
```

👉 **Copiez cette clé complète** (commence par `sb_secret_`)

⚠️ **Attention** : Cette clé est secrète ! Ne la partagez jamais publiquement.

---

## 📝 Exemple de .env.local complet

Une fois que vous avez les 3 valeurs, votre fichier `.env.local` devrait ressembler à :

```env
NEXT_PUBLIC_SUPABASE_URL=https://pgrnzjpkepppikdsuheg.supabase.co
NEXT_PUBLIC_SUPABASE_ANON_KEY=sb_publishable_Z98bZc6fjNFbtY-tinx3pA_YBv3z1ny
SUPABASE_SERVICE_ROLE_KEY=sb_secret_NOms6y8V9nGnaD5WSAWAoQ_XH22oP_c

NEXT_PUBLIC_URL=http://localhost:3000
```

---

## 🔍 Si vous ne trouvez pas la Project URL

Parfois elle est affichée différemment. Cherchez :
- "Project URL"
- "API URL"
- "Supabase URL"
- Une URL qui commence par `https://` et finit par `.supabase.co`

---

## ✅ Vérification

Une fois rempli, vérifiez que :
- ✅ Les 3 lignes commencent bien par `NEXT_PUBLIC_SUPABASE_URL=`, `NEXT_PUBLIC_SUPABASE_ANON_KEY=`, `SUPABASE_SERVICE_ROLE_KEY=`
- ✅ Il n'y a pas d'espaces avant ou après le `=`
- ✅ Les valeurs sont complètes (pas de `...` à la fin)
- ✅ Pas de guillemets autour des valeurs

---

**C'est tout ! 🎉**

