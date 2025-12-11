import axios from 'axios'
import { createAdminClient } from '@/lib/supabase/client'

/**
 * Créer une commande chez le fournisseur via API
 */
export async function createSupplierOrder(
  supplier: any,
  items: any[],
  shippingAddress: any
): Promise<string> {
  const supabase = createAdminClient()

  try {
    if (supplier.api_type === 'aliexpress') {
      return await createAliExpressOrder(supplier, items, shippingAddress)
    } else if (supplier.api_type === 'cj_dropshipping') {
      return await createCJDropshippingOrder(supplier, items, shippingAddress)
    } else {
      throw new Error(`Type de fournisseur non supporté: ${supplier.api_type}`)
    }
  } catch (error: any) {
    console.error('Erreur création commande fournisseur:', error)
    throw error
  }
}

/**
 * Créer une commande AliExpress
 */
async function createAliExpressOrder(
  supplier: any,
  items: any[],
  shippingAddress: any
): Promise<string> {
  const apiKey = supplier.api_key
  const apiSecret = supplier.api_secret

  if (!apiKey || !apiSecret) {
    throw new Error('Clés API AliExpress manquantes')
  }

  // Préparer les produits pour AliExpress
  const products = items.map(item => ({
    product_id: item.product.supplier_product_id,
    quantity: item.quantity
  }))

  // Appel API AliExpress (exemple - adapter selon la vraie API)
  const response = await axios.post(
    'https://api.aliexpress.com/order/create',
    {
      products,
      shipping_address: {
        name: shippingAddress.name,
        address_line_1: shippingAddress.street,
        city: shippingAddress.city,
        country: shippingAddress.country,
        postal_code: shippingAddress.postalCode,
        phone: shippingAddress.phone
      }
    },
    {
      headers: {
        'Authorization': `Bearer ${apiKey}`,
        'Content-Type': 'application/json'
      }
    }
  )

  return response.data.order_id
}

/**
 * Créer une commande CJ Dropshipping
 */
async function createCJDropshippingOrder(
  supplier: any,
  items: any[],
  shippingAddress: any
): Promise<string> {
  const apiKey = supplier.api_key
  const apiSecret = supplier.api_secret

  if (!apiKey || !apiSecret) {
    throw new Error('Clés API CJ Dropshipping manquantes')
  }

  // Appel API CJ Dropshipping (exemple - adapter selon la vraie API)
  const response = await axios.post(
    'https://api.cjdropshipping.com/order/create',
    {
      products: items.map(item => ({
        productId: item.product.supplier_product_id,
        quantity: item.quantity
      })),
      shipping: shippingAddress
    },
    {
      headers: {
        'Authorization': `Bearer ${apiKey}`,
        'Content-Type': 'application/json'
      }
    }
  )

  return response.data.orderId
}

/**
 * Payer le fournisseur via API
 */
export async function paySupplier(
  supplier: any,
  amount: number,
  supplierOrderId: string
): Promise<void> {
  try {
    if (supplier.api_type === 'aliexpress') {
      // AliExpress gère le paiement automatiquement lors de la création de commande
      // Si besoin, ajouter un appel API spécifique ici
      return
    } else if (supplier.api_type === 'cj_dropshipping') {
      // CJ Dropshipping peut nécessiter un paiement séparé
      // Adapter selon l'API réelle
      return
    }
  } catch (error: any) {
    console.error('Erreur paiement fournisseur:', error)
    // Ne pas faire échouer toute la commande si le paiement API échoue
    // Le wallet a déjà été débité
  }
}

