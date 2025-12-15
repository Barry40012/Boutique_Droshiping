# 🔧 Dépannage - Ajout de Produit

## ❌ Erreur : "Erreur de connexion à la base de données"

### ✅ Solutions rapides :

#### 1. **Vérifier la connexion Internet**
- Assure-toi d'avoir une connexion Internet active
- Teste avec `ping aws-1-eu-west-1.pooler.supabase.com` dans le terminal

#### 2. **Vider les caches Laravel**
```bash
cd laravel-backend
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

#### 3. **Tester la connexion à la base de données**
```bash
cd laravel-backend
php test-db-connection.php
```

Si le test échoue :
- Vérifie ton fichier `.env` dans `laravel-backend/.env`
- Vérifie que ton projet Supabase est actif (pas en pause)
- Essaie l'URL directe au lieu du pooler dans Supabase

#### 4. **Vérifier les paramètres Supabase dans `.env`**
```env
DB_CONNECTION=pgsql
DB_HOST=aws-1-eu-west-1.pooler.supabase.com
DB_PORT=6543
DB_DATABASE=postgres
DB_USERNAME=ton_username
DB_PASSWORD=ton_password
DB_SSLMODE=require
```

#### 5. **Vérifier les logs Laravel**
Les erreurs détaillées sont dans :
```
laravel-backend/storage/logs/laravel.log
```

#### 6. **Si le problème persiste**
- Redémarre le serveur Laravel (`php artisan serve`)
- Vérifie que Supabase n'est pas en pause
- Contacte le support Supabase si nécessaire

### 📝 Note
Le système a été amélioré pour :
- ✅ Afficher des messages d'erreur plus détaillés
- ✅ Vérifier la connexion avant de créer le produit
- ✅ Gérer les erreurs de format UUID
- ✅ Logger toutes les erreurs pour diagnostic

