# 🔧 TROUBLESHOOTING - Connexion Base de Données

## ❌ Erreur : `could not translate host name "aws-1-eu-west-1.pooler.supabase.com" to address`

Cette erreur indique que Laravel ne peut pas résoudre le nom d'hôte Supabase.

### ✅ Solutions possibles :

#### 1. **Vérifier la connexion Internet**
- Assure-toi d'avoir une connexion Internet active
- Teste avec `ping aws-1-eu-west-1.pooler.supabase.com` dans le terminal

#### 2. **Vérifier le fichier `.env`**
Assure-toi que les valeurs suivantes sont correctes dans `laravel-backend/.env` :

```env
DB_CONNECTION=pgsql
DB_HOST=aws-1-eu-west-1.pooler.supabase.com
DB_PORT=6543
DB_DATABASE=postgres
DB_USERNAME=postgres.pgrnzjpkepppikdsuheg
DB_PASSWORD=Barrynoir400@
DB_SSLMODE=require
```

#### 3. **Vérifier le projet Supabase**
- Va sur https://supabase.com
- Vérifie que ton projet est actif
- Vérifie les paramètres de connexion dans Settings > Database

#### 4. **Essayer l'URL directe au lieu du pooler**
Si le pooler ne fonctionne pas, essaie l'URL directe :
- Va dans Supabase > Settings > Database
- Utilise l'URL "Direct connection" au lieu de "Connection pooling"

#### 5. **Vider le cache Laravel**
```bash
cd laravel-backend
php artisan config:clear
php artisan cache:clear
```

#### 6. **Vérifier les logs**
Les erreurs sont maintenant loggées dans `storage/logs/laravel.log`

### 📝 Note
J'ai ajouté une gestion d'erreurs dans les contrôleurs pour éviter les crashes. 
Si la connexion échoue, l'application affichera des données vides au lieu de planter.

