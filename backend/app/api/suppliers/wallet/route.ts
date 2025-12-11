import { NextRequest, NextResponse } from 'next/server'
import { createAdminClient } from '@/lib/supabase/client'

// GET /api/suppliers/wallet - Récupérer la balance d'un fournisseur
export async function GET(request: NextRequest) {
  try {
    const { searchParams } = new URL(request.url)
    const supplierId = searchParams.get('supplier_id')

    if (!supplierId) {
      return NextResponse.json(
        { error: 'supplier_id requis' },
        { status: 400 }
      )
    }

    const supabase = createAdminClient()
    const { data, error } = await supabase
      .from('suppliers')
      .select('id, name, wallet_balance')
      .eq('id', supplierId)
      .single()

    if (error || !data) {
      return NextResponse.json(
        { error: 'Fournisseur non trouvé' },
        { status: 404 }
      )
    }

    return NextResponse.json({ supplier: data })
  } catch (error: any) {
    return NextResponse.json(
      { error: error.message || 'Erreur serveur' },
      { status: 500 }
    )
  }
}

// POST /api/suppliers/wallet - Ajouter de l'argent au wallet
export async function POST(request: NextRequest) {
  try {
    const body = await request.json()
    const { supplier_id, amount, description } = body

    if (!supplier_id || !amount) {
      return NextResponse.json(
        { error: 'supplier_id et amount sont requis' },
        { status: 400 }
      )
    }

    const supabase = createAdminClient()

    // Récupérer la balance actuelle
    const { data: supplier, error: supplierError } = await supabase
      .from('suppliers')
      .select('wallet_balance')
      .eq('id', supplier_id)
      .single()

    if (supplierError || !supplier) {
      return NextResponse.json(
        { error: 'Fournisseur non trouvé' },
        { status: 404 }
      )
    }

    const newBalance = supplier.wallet_balance + amount

    // Mettre à jour la balance
    const { data: updatedSupplier, error: updateError } = await supabase
      .from('suppliers')
      .update({ wallet_balance: newBalance })
      .eq('id', supplier_id)
      .select()
      .single()

    if (updateError) {
      return NextResponse.json(
        { error: updateError.message },
        { status: 500 }
      )
    }

    // Enregistrer la transaction
    await supabase
      .from('wallet_transactions')
      .insert({
        supplier_id,
        type: 'deposit',
        amount,
        balance_before: supplier.wallet_balance,
        balance_after: newBalance,
        description: description || 'Dépôt manuel'
      })

    return NextResponse.json({
      supplier: updatedSupplier,
      message: 'Balance mise à jour'
    })
  } catch (error: any) {
    return NextResponse.json(
      { error: error.message || 'Erreur serveur' },
      { status: 500 }
    )
  }
}

