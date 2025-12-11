import { NextRequest, NextResponse } from 'next/server'
import { createAdminClient } from '@/lib/supabase/client'

// GET /api/suppliers/[id] - Récupérer un fournisseur par ID
export async function GET(
  request: NextRequest,
  { params }: { params: { id: string } }
) {
  try {
    const supabase = createAdminClient()
    const { data, error } = await supabase
      .from('suppliers')
      .select('id, name, api_type, wallet_balance, status, created_at')
      .eq('id', params.id)
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

// PUT /api/suppliers/[id] - Mettre à jour un fournisseur
export async function PUT(
  request: NextRequest,
  { params }: { params: { id: string } }
) {
  try {
    const supabase = createAdminClient()
    const body = await request.json()

    const updateData: any = {
      updated_at: new Date().toISOString()
    }

    if (body.name) updateData.name = body.name
    if (body.api_type) updateData.api_type = body.api_type
    if (body.api_key !== undefined) updateData.api_key = body.api_key
    if (body.api_secret !== undefined) updateData.api_secret = body.api_secret
    if (body.status) updateData.status = body.status

    const { data, error } = await supabase
      .from('suppliers')
      .update(updateData)
      .eq('id', params.id)
      .select('id, name, api_type, wallet_balance, status')
      .single()

    if (error) {
      return NextResponse.json(
        { error: error.message },
        { status: 500 }
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

// DELETE /api/suppliers/[id] - Supprimer un fournisseur
export async function DELETE(
  request: NextRequest,
  { params }: { params: { id: string } }
) {
  try {
    const supabase = createAdminClient()
    const { error } = await supabase
      .from('suppliers')
      .delete()
      .eq('id', params.id)

    if (error) {
      return NextResponse.json(
        { error: error.message },
        { status: 500 }
      )
    }

    return NextResponse.json({ message: 'Fournisseur supprimé' })
  } catch (error: any) {
    return NextResponse.json(
      { error: error.message || 'Erreur serveur' },
      { status: 500 }
    )
  }
}

