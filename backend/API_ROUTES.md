# 📡 Documentation des Routes API

## Base URL
```
http://localhost:3000/api
```

---

## 🔍 Produits

### `GET /api/products`
Récupérer tous les produits

**Query Parameters:**
- `status` (optional): Filtrer par statut (`active`, `inactive`, `out_of_stock`)

**Response:**
```json
{
  "products": [
    {
      "id": "uuid",
      "name": "Produit",
      "description": "...",
      "supplier_price": 5.00,
      "selling_price": 25.00,
      "margin": 20.00,
      "images": ["url1", "url2"],
      "status": "active"
    }
  ]
}
```

### `GET /api/products/[id]`
Récupérer un produit par ID

### `POST /api/products`
Créer un nouveau produit

**Body:**
```json
{
  "name": "Nom du produit",
  "description": "Description",
  "supplier_price": 5.00,
  "selling_price": 25.00,
  "images": ["url1"],
  "supplier_id": "uuid",
  "supplier_product_id": "aliexpress-123",
  "status": "active"
}
```

### `PUT /api/products/[id]`
Mettre à jour un produit

### `DELETE /api/products/[id]`
Supprimer un produit

---

## 🛒 Commandes

### `GET /api/orders`
Récupérer toutes les commandes

**Query Parameters:**
- `customer_id` (optional): Filtrer par client
- `status` (optional): Filtrer par statut

### `GET /api/orders/[id]`
Récupérer une commande avec ses items

### `POST /api/orders`
Créer une nouvelle commande

**Body:**
```json
{
  "customer_id": "uuid",
  "total_amount": 25.00,
  "shipping_address": {
    "name": "John Doe",
    "street": "123 Rue Example",
    "city": "Paris",
    "country": "France",
    "postalCode": "75001",
    "phone": "+33123456789"
  },
  "items": [
    {
      "product_id": "uuid",
      "quantity": 1,
      "unit_price": 25.00,
      "supplier_price": 5.00
    }
  ]
}
```

### `PUT /api/orders/[id]`
Mettre à jour une commande (statut, tracking, etc.)

---

## 💳 Paiements

### `POST /api/payments/create`
Créer une session de paiement

**Body:**
```json
{
  "orderId": "uuid",
  "customerEmail": "client@example.com",
  "customerName": "John Doe"
}
```

**Response:**
```json
{
  "payment_url": "https://korapay.com/...",
  "reference": "ORDER-uuid"
}
```

### `POST /api/webhooks/payment`
Webhook pour les notifications de paiement (appelé par Korapay)

**Body:**
```json
{
  "reference": "ORDER-uuid",
  "status": "success",
  "amount": 25.00
}
```

---

## 🏭 Fournisseurs

### `GET /api/suppliers`
Récupérer tous les fournisseurs

### `GET /api/suppliers/[id]`
Récupérer un fournisseur par ID

### `POST /api/suppliers`
Créer un nouveau fournisseur

**Body:**
```json
{
  "name": "AliExpress",
  "api_type": "aliexpress",
  "api_key": "key",
  "api_secret": "secret",
  "wallet_balance": 1000.00,
  "status": "active"
}
```

### `PUT /api/suppliers/[id]`
Mettre à jour un fournisseur

### `DELETE /api/suppliers/[id]`
Supprimer un fournisseur

---

## 💰 Wallet Fournisseur

### `GET /api/suppliers/wallet?supplier_id=uuid`
Récupérer la balance d'un fournisseur

### `POST /api/suppliers/wallet`
Ajouter de l'argent au wallet

**Body:**
```json
{
  "supplier_id": "uuid",
  "amount": 500.00,
  "description": "Dépôt initial"
}
```

### `GET /api/suppliers/wallet/transactions?supplier_id=uuid&limit=50`
Historique des transactions wallet

---

## 📦 Commandes Fournisseurs

### `GET /api/supplier-orders`
Récupérer toutes les commandes fournisseurs

**Query Parameters:**
- `order_id` (optional): Filtrer par commande client
- `supplier_id` (optional): Filtrer par fournisseur

---

## 📊 Statistiques

### `GET /api/stats`
Statistiques générales de la boutique

**Response:**
```json
{
  "revenue": {
    "total": 10000.00,
    "margin": 8000.00
  },
  "orders": {
    "total": 100,
    "paid": 95,
    "pending": 5
  },
  "products": {
    "total": 50,
    "active": 45
  },
  "suppliers": {
    "total": 3,
    "total_wallet_balance": 5000.00
  }
}
```

---

## ❤️ Health Check

### `GET /api/health`
Vérifier le statut de l'API

**Response:**
```json
{
  "status": "ok",
  "message": "Backend API is running",
  "timestamp": "2024-01-01T00:00:00.000Z"
}
```

---

## 🔐 Authentification

Les routes API utilisent actuellement le `SUPABASE_SERVICE_ROLE_KEY` pour l'authentification admin.

Pour une authentification utilisateur, il faudra ajouter :
- Middleware d'authentification
- Vérification des tokens JWT
- Gestion des rôles (admin, client)

---

## 🚨 Codes d'Erreur

- `400` - Bad Request (données invalides)
- `401` - Unauthorized (non authentifié)
- `404` - Not Found (ressource non trouvée)
- `500` - Internal Server Error (erreur serveur)

---

## 📝 Notes

- Toutes les routes retournent du JSON
- Les dates sont au format ISO 8601
- Les montants sont en décimales (USD par défaut)
- Les UUIDs suivent le format standard

