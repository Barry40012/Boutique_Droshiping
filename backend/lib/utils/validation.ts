import { z } from 'zod'

/**
 * Schémas de validation Zod
 */

export const productSchema = z.object({
  name: z.string().min(1, 'Le nom est requis'),
  description: z.string().optional().nullable(),
  supplier_price: z.number().positive('Le prix fournisseur doit être positif'),
  selling_price: z.number().positive('Le prix de vente doit être positif'),
  images: z.array(z.string()).default([]),
  supplier_id: z.string().uuid().optional().nullable(),
  supplier_product_id: z.string().optional().nullable(),
  status: z.enum(['active', 'inactive', 'out_of_stock']).default('active')
})

export const orderSchema = z.object({
  customer_id: z.string().uuid().optional().nullable(),
  total_amount: z.number().positive('Le montant total doit être positif'),
  shipping_address: z.object({
    name: z.string().min(1, 'Le nom est requis'),
    street: z.string().min(1, 'La rue est requise'),
    city: z.string().min(1, 'La ville est requise'),
    country: z.string().min(1, 'Le pays est requis'),
    postalCode: z.string().min(1, 'Le code postal est requis'),
    phone: z.string().min(1, 'Le téléphone est requis')
  }),
  items: z.array(z.object({
    product_id: z.string().uuid(),
    quantity: z.number().int().positive('La quantité doit être positive'),
    unit_price: z.number().positive('Le prix unitaire doit être positif'),
    supplier_price: z.number().positive('Le prix fournisseur doit être positif')
  })).min(1, 'Au moins un produit est requis')
})

export const supplierSchema = z.object({
  name: z.string().min(1, 'Le nom est requis'),
  api_type: z.enum(['aliexpress', 'cj_dropshipping', 'other']),
  api_key: z.string().optional().nullable(),
  api_secret: z.string().optional().nullable(),
  wallet_balance: z.number().min(0).default(0),
  status: z.enum(['active', 'inactive']).default('active')
})

export const paymentSchema = z.object({
  orderId: z.string().uuid('ID de commande invalide'),
  customerEmail: z.string().email('Email invalide'),
  customerName: z.string().optional()
})

export const walletDepositSchema = z.object({
  supplier_id: z.string().uuid('ID fournisseur invalide'),
  amount: z.number().positive('Le montant doit être positif'),
  description: z.string().optional()
})

