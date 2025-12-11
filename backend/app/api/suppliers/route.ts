import { NextRequest, NextResponse } from 'next/server'
import { createAdminClient } from '@/lib/supabase/client'

// GET /api/suppliers - Récupérer tous les fournisseurs
export async function GET() {
  try {
    const supabase = createAdminClient()
    const { data, error } = await supabase
      .from('suppliers')
      .select('id, name, api_type, wallet_balance, status, created_at')
      .order('created_at', { ascending: false })

    if (error) {
      return NextResponse.json(
        { error: error.message },
        { status: 500 }
      )
    }

    return NextResponse.json({ suppliers: data })
  } catch (error: any) {
    return NextResponse.json(
      { error: error.message || 'Erreur serveur' },
      { status: 500 }
    )
  }
}

// POST /api/suppliers - Créer un nouveau fournisseur
export async function POST(request: NextRequest) {
  try {
    const supabase = createAdminClient()
    const body = await request.json()

    const { data, error } = await supabase
      .from('suppliers')
      .insert({
        name: body.name,
        api_type: body.api_type,
        api_key: body.api_key,
        api_secret: body.api_secret,
        wallet_balance: body.wallet_balance || 0,
        status: body.status || 'active'
      })
      .select()
      .single()

    if (error) {
      return NextResponse.json(
        { error: error.message },
        { status: 500 }
      )
    }

    return NextResponse.json({ supplier: data }, { status: 201 })
  } catch (error: any) {
    return NextResponse.json(
      { error: error.message || 'Erreur serveur' },
      { status: 500 }
    )
  }
}

