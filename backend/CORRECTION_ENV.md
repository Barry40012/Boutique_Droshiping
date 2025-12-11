# ✅ Correction du fichier .env.local

## 🎯 Ce que vous devez mettre

Basé sur ce que vous m'avez montré, votre fichier `.env.local` devrait être :

```env
NEXT_PUBLIC_SUPABASE_URL=https://pgrnzjpkepppikdsuheg.supabase.co
NEXT_PUBLIC_SUPABASE_ANON_KEY=sb_publishable_Z98bZc6fjNFbtY-tinx3pA_YBv3z1ny
SUPABASE_SERVICE_ROLE_KEY=sb_secret_NOms6y8V9nGnaD5WSAWAoQ_XH22oP_c

NEXT_PUBLIC_URL=http://localhost:3000
```

## ⚠️ Important

1. **Remplacez** `eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...` par votre vraie clé secrète :
   ```
   sb_secret_NOms6y8V9nGnaD5WSAWAoQ_XH22oP_c
   ```

2. **Vérifiez** que la Project URL est correcte :
   - Allez dans **Settings** → **API** (PAS Database !)
   - Cherchez "Project URL" en haut de la page
   - Elle devrait être : `https://pgrnzjpkepppikdsuheg.supabase.co`

## 📝 Format correct

Chaque ligne doit être :
- Sans espaces avant ou après le `=`
- Sans guillemets autour des valeurs
- Valeurs complètes (pas de `...`)

Exemple **CORRECT** :
```env
NEXT_PUBLIC_SUPABASE_URL=https://pgrnzjpkepppikdsuheg.supabase.co
```

Exemple **INCORRECT** :
```env
NEXT_PUBLIC_SUPABASE_URL = "https://pgrnzjpkepppikdsuheg.supabase.co"
NEXT_PUBLIC_SUPABASE_URL=https://...
```

## ✅ Vérification finale

Votre fichier doit avoir exactement ces lignes (sans les commentaires) :

```env
NEXT_PUBLIC_SUPABASE_URL=https://pgrnzjpkepppikdsuheg.supabase.co
NEXT_PUBLIC_SUPABASE_ANON_KEY=sb_publishable_Z98bZc6fjNFbtY-tinx3pA_YBv3z1ny
SUPABASE_SERVICE_ROLE_KEY=sb_secret_NOms6y8V9nGnaD5WSAWAoQ_XH22oP_c
NEXT_PUBLIC_URL=http://localhost:3000
```

---

**Une fois corrigé, vous pouvez tester avec `npm run dev` ! 🚀**

