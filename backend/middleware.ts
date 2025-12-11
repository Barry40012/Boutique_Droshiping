import { NextResponse } from 'next/server'
import type { NextRequest } from 'next/server'

/**
 * Middleware pour la gestion des CORS et sécurité
 */
export function middleware(request: NextRequest) {
  // Headers CORS pour les requêtes API
  if (request.nextUrl.pathname.startsWith('/api')) {
    const response = NextResponse.next()

    // Autoriser les requêtes depuis votre frontend
    const origin = request.headers.get('origin')
    const allowedOrigins = [
      process.env.NEXT_PUBLIC_URL,
      'http://localhost:3000',
      'http://localhost:3001'
    ].filter(Boolean)

    if (origin && allowedOrigins.includes(origin)) {
      response.headers.set('Access-Control-Allow-Origin', origin)
    }

    response.headers.set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
    response.headers.set('Access-Control-Allow-Headers', 'Content-Type, Authorization')
    response.headers.set('Access-Control-Max-Age', '86400')

    // Gérer les requêtes OPTIONS (preflight)
    if (request.method === 'OPTIONS') {
      return new NextResponse(null, { status: 200, headers: response.headers })
    }

    return response
  }

  return NextResponse.next()
}

export const config = {
  matcher: '/api/:path*'
}

