import { createAdminClient } from '@/lib/supabase/client'
import { createSupplierOrder, paySupplier } from './supplier-handler'

/**
 * Traiter automatiquement une commande après paiement
 * - Débiter le wallet fournisseur
 * - Créer la commande chez le fournisseur
 * - Payer le fournisseur
 */
export async function processOrderAutomation(orderId: string) {
  const supabase = createAdminClient()

  try {
    // 1. Récupérer la commande avec ses items
    const { data: order, error: orderError } = await supabase
      .from('orders')
      .select('*')
      .eq('id', orderId)
      .single()

    if (orderError || !order) {
      throw new Error(`Commande ${orderId} non trouvée`)
    }

    // 2. Récupérer les items de la commande
    const { data: orderItems, error: itemsError } = await supabase
      .from('order_items')
      .select('*, products(*)')
      .eq('order_id', orderId)

    if (itemsError || !orderItems) {
      throw new Error(`Impossible de récupérer les items de la commande`)
    }

    // 3. Grouper les items par fournisseur
    const itemsBySupplier = new Map<string, any[]>()

    for (const item of orderItems) {
      const product = item.products
      if (!product || !product.supplier_id) {
        continue
      }

      const supplierId = product.supplier_id
      if (!itemsBySupplier.has(supplierId)) {
        itemsBySupplier.set(supplierId, [])
      }
      itemsBySupplier.get(supplierId)!.push({
        ...item,
        product
      })
    }

    // 4. Traiter chaque fournisseur
    for (const [supplierId, items] of itemsBySupplier.entries()) {
      // Récupérer le fournisseur
      const { data: supplier, error: supplierError } = await supabase
        .from('suppliers')
        .select('*')
        .eq('id', supplierId)
        .single()

      if (supplierError || !supplier) {
        console.error(`Fournisseur ${supplierId} non trouvé`)
        continue
      }

      // Calculer le montant total pour ce fournisseur
      const totalAmount = items.reduce((sum, item) => {
        return sum + (item.supplier_price * item.quantity)
      }, 0)

      // Vérifier la balance du wallet
      if (supplier.wallet_balance < totalAmount) {
        throw new Error(
          `Balance insuffisante pour le fournisseur ${supplier.name}. ` +
          `Balance: ${supplier.wallet_balance}, Requis: ${totalAmount}`
        )
      }

      // Débiter le wallet
      const newBalance = supplier.wallet_balance - totalAmount
      const { error: walletError } = await supabase
        .from('suppliers')
        .update({ wallet_balance: newBalance })
        .eq('id', supplierId)

      if (walletError) {
        throw new Error(`Erreur débit wallet: ${walletError.message}`)
      }

      // Enregistrer la transaction wallet
      await supabase
        .from('wallet_transactions')
        .insert({
          supplier_id: supplierId,
          order_id: orderId,
          type: 'withdrawal',
          amount: totalAmount,
          balance_before: supplier.wallet_balance,
          balance_after: newBalance,
          description: `Paiement automatique commande ${orderId}`
        })

      // Créer la commande chez le fournisseur
      const supplierOrderId = await createSupplierOrder(
        supplier,
        items,
        order.shipping_address
      )

      // Payer le fournisseur via API
      await paySupplier(supplier, totalAmount, supplierOrderId)

      // Enregistrer la commande fournisseur
      await supabase
        .from('supplier_orders')
        .insert({
          order_id: orderId,
          supplier_id: supplierId,
          supplier_order_id: supplierOrderId,
          amount_paid: totalAmount,
          status: 'paid'
        })

      console.log(`Commande ${orderId} traitée pour fournisseur ${supplier.name}`)
    }

    // 5. Mettre à jour le statut de la commande
    await supabase
      .from('orders')
      .update({ status: 'processing' })
      .eq('id', orderId)

    return { success: true }
  } catch (error: any) {
    console.error('Erreur automatisation commande:', error)
    throw error
  }
}

