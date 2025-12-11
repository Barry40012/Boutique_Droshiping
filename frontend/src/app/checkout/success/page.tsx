'use client'

import { useEffect } from 'react'
import { useSearchParams } from 'next/navigation'
import Link from 'next/link'
import { Button } from '@/components/ui/Button'
import { useCartStore } from '@/store/cartStore'
import { trackPurchase } from '@/components/FacebookPixel'

export default function CheckoutSuccessPage() {
  const searchParams = useSearchParams()
  const orderId = searchParams.get('orderId')
  const clearCart = useCartStore((state) => state.clearCart)

  useEffect(() => {
    // Vider le panier après paiement réussi
    clearCart()
    
    // Tracker l'achat Facebook Pixel (si on a le montant)
    // trackPurchase(total, 'USD')
  }, [clearCart])

  return (
    <div className="container mx-auto px-4 py-16 text-center">
      <div className="max-w-2xl mx-auto">
        <div className="mb-8">
          <div className="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg className="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
            </svg>
          </div>
          <h1 className="text-4xl font-bold mb-4">Commande confirmée !</h1>
          <p className="text-gray-600 text-lg">
            Merci pour votre achat. Votre commande a été reçue et sera traitée sous peu.
          </p>
        </div>

        {orderId && (
          <div className="bg-gray-50 rounded-lg p-6 mb-8">
            <p className="text-sm text-gray-600 mb-2">Numéro de commande</p>
            <p className="font-mono text-lg font-semibold">{orderId}</p>
          </div>
        )}

        <div className="space-y-4">
          <p className="text-gray-600">
            Vous recevrez un email de confirmation avec les détails de votre commande.
          </p>
          
          <div className="flex gap-4 justify-center">
            <Link href="/products">
              <Button variant="outline">Continuer les achats</Button>
            </Link>
            <Link href="/">
              <Button>Retour à l'accueil</Button>
            </Link>
          </div>
        </div>
      </div>
    </div>
  )
}

