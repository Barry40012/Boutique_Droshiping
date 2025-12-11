import { createClientComponentClient } from '@supabase/auth-helpers-nextjs'

export const createSupabaseClient = () => {
  return createClientComponentClient()
}

// Wrapper pour récupérer la session côté client
export async function getClientSession() {
  const supabase = createSupabaseClient()
  const {
    data: { session },
  } = await supabase.auth.getSession()
  return session
}

