import { NextRequest, NextResponse } from 'next/server'
import { createKorapayPayment } from '@/lib/payments/korapay'
import { createAdminClient } from '@/lib/supabase/client'

// POST /api/payments/create - Créer une session de paiement
export async function POST(request: NextRequest) {
  try {
    const body = await request.json()
    const { orderId, customerEmail, customerName } = body

    if (!orderId || !customerEmail) {
      return NextResponse.json(
        { error: 'orderId et customerEmail sont requis' },
        { status: 400 }
      )
    }

    // Récupérer la commande
    const supabase = createAdminClient()
    const { data: order, error: orderError } = await supabase
      .from('orders')
      .select('*')
      .eq('id', orderId)
      .single()

    if (orderError || !order) {
      return NextResponse.json(
        { error: 'Commande non trouvée' },
        { status: 404 }
      )
    }

    // Créer le paiement Korapay
    const paymentResponse = await createKorapayPayment({
      amount: order.total_amount,
      currency: 'USD',
      reference: `ORDER-${orderId}`,
      customer: {
        email: customerEmail,
        name: customerName
      },
      redirect_url: `${process.env.NEXT_PUBLIC_URL}/checkout/success?orderId=${orderId}`,
      metadata: {
        order_id: orderId
      }
    })

    // Enregistrer le paiement dans la base de données
    const { data: payment, error: paymentError } = await supabase
      .from('payments')
      .insert({
        order_id: orderId,
        amount: order.total_amount,
        payment_method: 'card',
        payment_gateway: 'korapay',
        transaction_id: paymentResponse.data.reference,
        status: 'pending'
      })
      .select()
      .single()

    if (paymentError) {
      console.error('Erreur enregistrement paiement:', paymentError)
    }

    return NextResponse.json({
      payment_url: paymentResponse.data.authorization_url,
      reference: paymentResponse.data.reference
    })
  } catch (error: any) {
    return NextResponse.json(
      { error: error.message || 'Erreur création paiement' },
      { status: 500 }
    )
  }
}

