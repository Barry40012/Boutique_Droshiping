import { NextRequest, NextResponse } from 'next/server'
import { createAdminClient } from '@/lib/supabase/client'

// GET /api/supplier-orders - Récupérer toutes les commandes fournisseurs
export async function GET(request: NextRequest) {
  try {
    const supabase = createAdminClient()
    const { searchParams } = new URL(request.url)
    const orderId = searchParams.get('order_id')
    const supplierId = searchParams.get('supplier_id')

    let query = supabase
      .from('supplier_orders')
      .select('*, suppliers(name), orders(*)')
      .order('created_at', { ascending: false })

    if (orderId) {
      query = query.eq('order_id', orderId)
    }

    if (supplierId) {
      query = query.eq('supplier_id', supplierId)
    }

    const { data, error } = await query

    if (error) {
      return NextResponse.json(
        { error: error.message },
        { status: 500 }
      )
    }

    return NextResponse.json({ supplier_orders: data || [] })
  } catch (error: any) {
    return NextResponse.json(
      { error: error.message || 'Erreur serveur' },
      { status: 500 }
    )
  }
}

