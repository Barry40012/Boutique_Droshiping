'use client'

import { useEffect, useState } from 'react'
import Link from 'next/link'
import { createSupabaseClient } from '@/lib/supabase/client'
import { Button } from '@/components/ui/Button'

export function UserMenu() {
  const supabase = createSupabaseClient()
  const [loading, setLoading] = useState(true)
  const [email, setEmail] = useState<string | null>(null)

  useEffect(() => {
    const fetchSession = async () => {
      const { data } = await supabase.auth.getUser()
      setEmail(data.user?.email ?? null)
      setLoading(false)
    }
    fetchSession()

    const { data: subscription } = supabase.auth.onAuthStateChange(
      async (_event, session) => {
        setEmail(session?.user?.email ?? null)
      }
    )

    return () => {
      subscription?.subscription.unsubscribe()
    }
  }, [supabase])

  const handleLogout = async () => {
    await supabase.auth.signOut()
    window.location.reload()
  }

  if (loading) return null

  if (!email) {
    return (
      <div className="flex items-center gap-3">
        <Link href="/auth/login" className="text-sm hover:text-primary">Se connecter</Link>
        <Link href="/auth/register" className="text-sm hover:text-primary">Créer un compte</Link>
      </div>
    )
  }

  return (
    <div className="flex items-center gap-3 text-sm">
      <span className="text-gray-700">{email}</span>
      <Button variant="outline" size="sm" onClick={handleLogout}>
        Déconnexion
      </Button>
    </div>
  )
}

