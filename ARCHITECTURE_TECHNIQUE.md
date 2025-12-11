# 🏗️ ARCHITECTURE TECHNIQUE DÉTAILLÉE

## 📐 ARCHITECTURE GLOBALE

```
┌─────────────────────────────────────────────────────────────┐
│                    FRONTEND (Next.js/React)                 │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐   │
│  │  Pages   │  │Components│  │  State   │  │  Styles  │   │
│  │  Public  │  │  Reusable│  │Management│  │ Tailwind │   │
│  └──────────┘  └──────────┘  └──────────┘  └──────────┘   │
└─────────────────────────────────────────────────────────────┘
                            ↕
┌─────────────────────────────────────────────────────────────┐
│              API ROUTES (Next.js API Routes)                │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐     │
│  │ Payments │  │  Orders  │  │Products  │  │Suppliers │     │
│  │ Webhooks │  │  CRUD    │  │  CRUD    │  │   API    │     │
│  └──────────┘  └──────────┘  └──────────┘  └──────────┘     │
└─────────────────────────────────────────────────────────────┘
                            ↕
┌─────────────────────────────────────────────────────────────┐
│                    SUPABASE (Backend)                       │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐     │
│  │PostgreSQL│  │   Auth   │  │ Storage  │  │ Realtime │     │
│  │ Database │  │  System  │  │  Images  │  │  Updates │     │
│  └──────────┘  └──────────┘  └──────────┘  └──────────┘     │
└─────────────────────────────────────────────────────────────┘
                            ↕
┌─────────────────────────────────────────────────────────────┐
│              SERVICES EXTERNES                              │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐   │
│  │ Korapay/ │  │AliExpress│  │ Facebook │  │  Email   │   │
│  │   DPO    │  │    API   │  │  Pixel   │  │  Service │   │
│  └──────────┘  └──────────┘  └──────────┘  └──────────┘   │
└─────────────────────────────────────────────────────────────┘
```

---

## 🗄️ SCHÉMA BASE DE DONNÉES DÉTAILLÉ

### **Relations entre Tables**

```
customers (1) ──< (N) orders (1) ──< (N) order_items (N) >── (1) products
                                                                    │
                                                                    │ (N)
                                                                    │
suppliers (1) ──< (N) supplier_orders (N) >── (1) orders
         │
         │ (1)
         │
         └──< (N) wallet_transactions

orders (1) ──< (1) payments
```

---

## 🔄 FLUX DE DONNÉES DÉTAILLÉ

### **1. Flux Achat Client**

```javascript
// 1. Client ajoute au panier
POST /api/cart/add
{
  productId: "uuid",
  quantity: 2
}

// 2. Client procède au checkout
POST /api/checkout
{
  items: [...],
  shippingAddress: {...},
  paymentMethod: "card"
}

// 3. Création commande dans Supabase
INSERT INTO orders (customer_id, total_amount, status)
RETURNING order_id

// 4. Redirection vers paiement Korapay/DPO
POST /api/payments/create
{
  orderId: "uuid",
  amount: 25.00,
  currency: "USD"
}
→ Redirect to payment gateway

// 5. Webhook paiement confirmé
POST /api/webhooks/payment
{
  transactionId: "...",
  status: "success",
  orderId: "uuid"
}

// 6. Automatisation déclenchée
→ Update order status: "paid"
→ Create supplier_order
→ Debit wallet (5$)
→ Send payment to supplier API
→ Send order to supplier API
→ Notify customer
```

### **2. Flux Automatisation Fournisseur**

```javascript
// Webhook déclenche automatisation
async function handlePaymentSuccess(orderId) {
  // 1. Récupérer commande
  const order = await getOrder(orderId);
  
  // 2. Pour chaque produit
  for (const item of order.items) {
    const product = await getProduct(item.productId);
    const supplier = await getSupplier(product.supplierId);
    
    // 3. Vérifier wallet balance
    if (supplier.wallet_balance < product.supplier_price) {
      // Alerte : balance insuffisante
      await sendAlert("Low wallet balance");
      return;
    }
    
    // 4. Débiter wallet
    await debitWallet(supplier.id, product.supplier_price);
    
    // 5. Créer commande chez fournisseur
    const supplierOrder = await createSupplierOrder({
      supplier: supplier.api_type,
      productId: product.supplier_product_id,
      quantity: item.quantity,
      shippingAddress: order.shipping_address
    });
    
    // 6. Payer fournisseur
    await paySupplier({
      supplierId: supplier.id,
      amount: product.supplier_price * item.quantity,
      orderId: supplierOrder.id
    });
    
    // 7. Enregistrer dans supplier_orders
    await createSupplierOrderRecord({
      orderId: order.id,
      supplierId: supplier.id,
      supplierOrderId: supplierOrder.id,
      amountPaid: product.supplier_price * item.quantity
    });
  }
  
  // 8. Mettre à jour statut commande
  await updateOrderStatus(orderId, "processing");
  
  // 9. Notifier client
  await sendEmail(order.customer.email, "Order confirmed");
}
```

---

## 🔐 SÉCURITÉ SUPABASE (RLS Policies)

### **Table `products`**
```sql
-- Public peut lire les produits actifs
CREATE POLICY "Public can view active products"
ON products FOR SELECT
USING (status = 'active');

-- Admin peut tout faire
CREATE POLICY "Admin full access"
ON products FOR ALL
USING (auth.jwt() ->> 'role' = 'admin');
```

### **Table `orders`**
```sql
-- Client peut voir ses propres commandes
CREATE POLICY "Users can view own orders"
ON orders FOR SELECT
USING (auth.uid() = customer_id);

-- Admin peut tout voir
CREATE POLICY "Admin can view all orders"
ON orders FOR SELECT
USING (auth.jwt() ->> 'role' = 'admin');
```

### **Table `suppliers`**
```sql
-- Seuls les admins peuvent accéder
CREATE POLICY "Admin only"
ON suppliers FOR ALL
USING (auth.jwt() ->> 'role' = 'admin');
```

---

## 📡 API ENDPOINTS

### **Produits**
```
GET    /api/products              # Liste produits
GET    /api/products/:id          # Détail produit
POST   /api/products              # Créer produit (admin)
PUT    /api/products/:id          # Modifier produit (admin)
DELETE /api/products/:id          # Supprimer produit (admin)
```

### **Commandes**
```
GET    /api/orders                 # Mes commandes (user)
GET    /api/orders/:id             # Détail commande
POST   /api/orders                 # Créer commande
PUT    /api/orders/:id/status      # Mettre à jour statut (admin)
```

### **Paiement**
```
POST   /api/payments/create        # Créer session paiement
POST   /api/payments/verify        # Vérifier paiement
POST   /api/webhooks/payment       # Webhook Korapay/DPO
```

### **Fournisseurs**
```
GET    /api/suppliers              # Liste fournisseurs (admin)
POST   /api/suppliers/orders       # Créer commande fournisseur
POST   /api/suppliers/pay          # Payer fournisseur
GET    /api/suppliers/wallet       # Balance wallet (admin)
```

### **Panier**
```
GET    /api/cart                   # Mon panier
POST   /api/cart/add               # Ajouter au panier
PUT    /api/cart/update            # Modifier quantité
DELETE /api/cart/remove            # Retirer du panier
```

---

## 🎨 STRUCTURE PROJET NEXT.JS

```
boutique-dropshipping/
├── .env.local                    # Variables d'environnement
├── .gitignore
├── package.json
├── next.config.js
├── tailwind.config.js
│
├── public/
│   ├── images/
│   └── favicon.ico
│
├── src/
│   ├── app/                      # Next.js App Router
│   │   ├── layout.tsx
│   │   ├── page.tsx              # Home
│   │   ├── products/
│   │   │   ├── page.tsx          # Liste produits
│   │   │   └── [id]/
│   │   │       └── page.tsx      # Détail produit
│   │   ├── cart/
│   │   │   └── page.tsx
│   │   ├── checkout/
│   │   │   └── page.tsx
│   │   ├── orders/
│   │   │   └── page.tsx
│   │   ├── admin/
│   │   │   ├── products/
│   │   │   ├── orders/
│   │   │   └── dashboard/
│   │   └── api/                  # API Routes
│   │       ├── products/
│   │       ├── orders/
│   │       ├── payments/
│   │       ├── webhooks/
│   │       └── suppliers/
│   │
│   ├── components/
│   │   ├── ui/                   # Composants UI (Shadcn)
│   │   ├── products/
│   │   │   ├── ProductCard.tsx
│   │   │   └── ProductList.tsx
│   │   ├── cart/
│   │   │   ├── CartSidebar.tsx
│   │   │   └── CartItem.tsx
│   │   ├── checkout/
│   │   │   ├── CheckoutForm.tsx
│   │   │   └── PaymentButton.tsx
│   │   └── layout/
│   │       ├── Header.tsx
│   │       └── Footer.tsx
│   │
│   ├── lib/
│   │   ├── supabase/
│   │   │   ├── client.ts         # Client Supabase
│   │   │   └── server.ts         # Server Supabase
│   │   ├── payments/
│   │   │   ├── korapay.ts        # Intégration Korapay
│   │   │   └── dpo.ts            # Intégration DPO
│   │   ├── suppliers/
│   │   │   ├── aliexpress.ts     # API AliExpress
│   │   │   └── cj-dropshipping.ts
│   │   └── utils/
│   │       ├── format.ts
│   │       └── validation.ts
│   │
│   ├── hooks/
│   │   ├── useCart.ts
│   │   ├── useProducts.ts
│   │   └── useOrders.ts
│   │
│   ├── types/
│   │   ├── product.ts
│   │   ├── order.ts
│   │   ├── payment.ts
│   │   └── supplier.ts
│   │
│   └── styles/
│       └── globals.css
│
└── supabase/
    ├── migrations/               # Migrations SQL
    │   ├── 001_initial_schema.sql
    │   └── 002_rls_policies.sql
    └── seed.sql                  # Données de test
```

---

## 🔌 INTÉGRATIONS EXTERNES

### **1. Korapay Integration**

```typescript
// lib/payments/korapay.ts
export async function createPayment(amount: number, orderId: string) {
  const response = await fetch('https://api.korapay.com/v1/charges', {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${process.env.KORAPAY_SECRET_KEY}`,
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      amount,
      currency: 'USD',
      reference: orderId,
      redirect_url: `${process.env.NEXT_PUBLIC_URL}/checkout/success`,
      customer: {
        email: customerEmail
      }
    })
  });
  
  return response.json();
}

export async function verifyPayment(reference: string) {
  const response = await fetch(`https://api.korapay.com/v1/charges/${reference}`, {
    headers: {
      'Authorization': `Bearer ${process.env.KORAPAY_SECRET_KEY}`
    }
  });
  
  return response.json();
}
```

### **2. AliExpress API Integration**

```typescript
// lib/suppliers/aliexpress.ts
export async function createOrder(productId: string, quantity: number, address: Address) {
  const response = await fetch('https://api.aliexpress.com/order/create', {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${process.env.ALIEXPRESS_API_KEY}`,
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      product_id: productId,
      quantity,
      shipping_address: {
        name: address.name,
        address_line_1: address.street,
        city: address.city,
        country: address.country,
        postal_code: address.postalCode
      }
    })
  });
  
  return response.json();
}
```

### **3. Facebook Pixel**

```typescript
// components/FacebookPixel.tsx
'use client';

import { useEffect } from 'react';
import Script from 'next/script';

export function FacebookPixel() {
  useEffect(() => {
    if (typeof window !== 'undefined' && window.fbq) {
      window.fbq('track', 'PageView');
    }
  }, []);

  return (
    <>
      <Script
        id="facebook-pixel"
        strategy="afterInteractive"
        dangerouslySetInnerHTML={{
          __html: `
            !function(f,b,e,v,n,t,s)
            {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};
            if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
            n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t,s)}(window, document,'script',
            'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '${process.env.NEXT_PUBLIC_FACEBOOK_PIXEL_ID}');
            fbq('track', 'PageView');
          `
        }}
      />
    </>
  );
}

// Utilisation pour track Purchase
export function trackPurchase(amount: number, currency: string = 'USD') {
  if (typeof window !== 'undefined' && window.fbq) {
    window.fbq('track', 'Purchase', {
      value: amount,
      currency: currency
    });
  }
}
```

---

## 🚀 DÉPLOIEMENT

### **Frontend (Vercel)**
```bash
# Installation Vercel CLI
npm i -g vercel

# Déploiement
vercel

# Variables d'environnement à configurer dans Vercel Dashboard
```

### **Supabase**
- Base de données : Gérée par Supabase
- Storage : Configuré dans Supabase Dashboard
- Auth : Configuré dans Supabase Dashboard

### **Variables d'environnement**

```env
# .env.local
NEXT_PUBLIC_SUPABASE_URL=your_supabase_url
NEXT_PUBLIC_SUPABASE_ANON_KEY=your_anon_key
SUPABASE_SERVICE_ROLE_KEY=your_service_role_key

NEXT_PUBLIC_URL=https://yourdomain.com

KORAPAY_PUBLIC_KEY=your_korapay_public_key
KORAPAY_SECRET_KEY=your_korapay_secret_key

ALIEXPRESS_API_KEY=your_aliexpress_key
ALIEXPRESS_API_SECRET=your_aliexpress_secret

NEXT_PUBLIC_FACEBOOK_PIXEL_ID=your_pixel_id
```

---

## 📊 MONITORING & LOGS

### **Logs à implémenter**
- Paiements (succès/échec)
- Commandes fournisseur
- Erreurs API
- Balance wallet (alertes)

### **Monitoring**
- Vercel Analytics
- Supabase Dashboard
- Sentry (optionnel pour erreurs)

---

**Ce document sera mis à jour au fur et à mesure du développement.**

