import { NextRequest, NextResponse } from 'next/server'
import { verifyKorapayPayment } from '@/lib/payments/korapay'
import { createAdminClient } from '@/lib/supabase/client'
import { processOrderAutomation } from '@/lib/automation/order-processor'

// POST /api/webhooks/payment - Webhook pour les notifications de paiement
export async function POST(request: NextRequest) {
  try {
    const body = await request.json()
    const { reference, status, amount } = body

    if (!reference) {
      return NextResponse.json(
        { error: 'Reference manquante' },
        { status: 400 }
      )
    }

    // Extraire l'orderId de la référence (format: ORDER-{orderId})
    const orderId = reference.replace('ORDER-', '')

    const supabase = createAdminClient()

    // Vérifier le paiement avec Korapay
    const verification = await verifyKorapayPayment(reference)

    if (verification.status !== 'success') {
      return NextResponse.json(
        { error: 'Paiement non confirmé' },
        { status: 400 }
      )
    }

    // Mettre à jour le statut du paiement
    const { error: paymentError } = await supabase
      .from('payments')
      .update({
        status: 'success',
        gateway_response: verification
      })
      .eq('transaction_id', reference)

    if (paymentError) {
      console.error('Erreur mise à jour paiement:', paymentError)
    }

    // Mettre à jour le statut de la commande
    const { error: orderError } = await supabase
      .from('orders')
      .update({
        payment_status: 'paid',
        status: 'processing',
        payment_id: reference
      })
      .eq('id', orderId)

    if (orderError) {
      console.error('Erreur mise à jour commande:', orderError)
    }

    // Déclencher l'automatisation (paiement fournisseur, création commande)
    try {
      await processOrderAutomation(orderId)
    } catch (autoError: any) {
      console.error('Erreur automatisation:', autoError)
      // On continue même si l'automatisation échoue
    }

    return NextResponse.json({ success: true })
  } catch (error: any) {
    console.error('Erreur webhook paiement:', error)
    return NextResponse.json(
      { error: error.message || 'Erreur traitement webhook' },
      { status: 500 }
    )
  }
}

