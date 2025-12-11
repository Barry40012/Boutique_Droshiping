import { NextResponse } from 'next/server'
import { createAdminClient } from '@/lib/supabase/client'

// GET /api/stats - Statistiques générales
export async function GET() {
  try {
    const supabase = createAdminClient()

    // Statistiques commandes
    const { data: orders, error: ordersError } = await supabase
      .from('orders')
      .select('total_amount, margin, payment_status, status')

    // Statistiques produits
    const { data: products, error: productsError } = await supabase
      .from('products')
      .select('status')

    // Statistiques fournisseurs
    const { data: suppliers, error: suppliersError } = await supabase
      .from('suppliers')
      .select('wallet_balance, status')

    if (ordersError || productsError || suppliersError) {
      return NextResponse.json(
        { error: 'Erreur récupération statistiques' },
        { status: 500 }
      )
    }

    // Calculer les stats
    const totalRevenue = orders
      ?.filter(o => o.payment_status === 'paid')
      .reduce((sum, o) => sum + o.total_amount, 0) || 0

    const totalMargin = orders
      ?.filter(o => o.payment_status === 'paid')
      .reduce((sum, o) => sum + o.margin, 0) || 0

    const totalOrders = orders?.length || 0
    const paidOrders = orders?.filter(o => o.payment_status === 'paid').length || 0
    const activeProducts = products?.filter(p => p.status === 'active').length || 0
    const totalSuppliers = suppliers?.length || 0
    const totalWalletBalance = suppliers?.reduce((sum, s) => sum + s.wallet_balance, 0) || 0

    return NextResponse.json({
      revenue: {
        total: totalRevenue,
        margin: totalMargin
      },
      orders: {
        total: totalOrders,
        paid: paidOrders,
        pending: totalOrders - paidOrders
      },
      products: {
        total: products?.length || 0,
        active: activeProducts
      },
      suppliers: {
        total: totalSuppliers,
        total_wallet_balance: totalWalletBalance
      }
    })
  } catch (error: any) {
    return NextResponse.json(
      { error: error.message || 'Erreur serveur' },
      { status: 500 }
    )
  }
}

