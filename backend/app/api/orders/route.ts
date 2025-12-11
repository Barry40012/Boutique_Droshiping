import { NextRequest, NextResponse } from 'next/server'
import { createAdminClient } from '@/lib/supabase/client'

// GET /api/orders - Récupérer toutes les commandes
export async function GET(request: NextRequest) {
  try {
    const supabase = createAdminClient()
    const { searchParams } = new URL(request.url)
    const customerId = searchParams.get('customer_id')
    const status = searchParams.get('status')

    let query = supabase
      .from('orders')
      .select('*')
      .order('created_at', { ascending: false })

    if (customerId) {
      query = query.eq('customer_id', customerId)
    }

    if (status) {
      query = query.eq('status', status)
    }

    const { data, error } = await query

    if (error) {
      return NextResponse.json(
        { error: error.message },
        { status: 500 }
      )
    }

    return NextResponse.json({ orders: data })
  } catch (error: any) {
    return NextResponse.json(
      { error: error.message || 'Erreur serveur' },
      { status: 500 }
    )
  }
}

// POST /api/orders - Créer une nouvelle commande
export async function POST(request: NextRequest) {
  try {
    const supabase = createAdminClient()
    const body = await request.json()

    // Calculer le coût fournisseur et la marge
    let supplierCost = 0
    let totalMargin = 0

    if (body.items && Array.isArray(body.items)) {
      for (const item of body.items) {
        supplierCost += item.supplier_price * item.quantity
        totalMargin += (item.unit_price - item.supplier_price) * item.quantity
      }
    }

    const { data: order, error: orderError } = await supabase
      .from('orders')
      .insert({
        customer_id: body.customer_id,
        total_amount: body.total_amount,
        supplier_cost: supplierCost,
        margin: totalMargin,
        status: 'pending',
        payment_status: 'pending',
        shipping_address: body.shipping_address
      })
      .select()
      .single()

    if (orderError) {
      return NextResponse.json(
        { error: orderError.message },
        { status: 500 }
      )
    }

    // Créer les order_items
    if (body.items && Array.isArray(body.items)) {
      const orderItems = body.items.map((item: any) => ({
        order_id: order.id,
        product_id: item.product_id,
        quantity: item.quantity,
        unit_price: item.unit_price,
        supplier_price: item.supplier_price
      }))

      const { error: itemsError } = await supabase
        .from('order_items')
        .insert(orderItems)

      if (itemsError) {
        return NextResponse.json(
          { error: itemsError.message },
          { status: 500 }
        )
      }
    }

    return NextResponse.json({ order }, { status: 201 })
  } catch (error: any) {
    return NextResponse.json(
      { error: error.message || 'Erreur serveur' },
      { status: 500 }
    )
  }
}

