'use client'

import { useCartStore } from '@/store/cartStore'
import { Button } from '@/components/ui/Button'
import Link from 'next/link'
import Image from 'next/image'
import { Trash2, Plus, Minus } from 'lucide-react'

export default function CartPage() {
  const { items, removeItem, updateQuantity, getTotal, clearCart } = useCartStore()
  const total = getTotal()

  if (items.length === 0) {
    return (
      <div className="container mx-auto px-4 py-16 text-center">
        <h1 className="text-4xl font-bold mb-4">Votre Panier</h1>
        <p className="text-gray-600 mb-8">Votre panier est vide</p>
        <Link href="/products">
          <Button>Continuer les achats</Button>
        </Link>
      </div>
    )
  }

  return (
    <div className="container mx-auto px-4 py-8">
      <h1 className="text-4xl font-bold mb-8">Votre Panier</h1>
      
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div className="lg:col-span-2 space-y-4">
          {items.map((item) => (
            <div key={item.product.id} className="border rounded-lg p-4 flex gap-4">
              {item.product.images && item.product.images.length > 0 && (
                <div className="relative w-24 h-24 bg-gray-100 rounded">
                  <Image
                    src={item.product.images[0]}
                    alt={item.product.name}
                    fill
                    className="object-cover rounded"
                  />
                </div>
              )}
              
              <div className="flex-1">
                <h3 className="font-semibold text-lg">{item.product.name}</h3>
                <p className="text-gray-600">${item.product.selling_price.toFixed(2)}</p>
                
                <div className="flex items-center gap-4 mt-4">
                  <div className="flex items-center gap-2 border rounded">
                    <button
                      onClick={() => updateQuantity(item.product.id, item.quantity - 1)}
                      className="p-2 hover:bg-gray-100"
                    >
                      <Minus className="w-4 h-4" />
                    </button>
                    <span className="px-4">{item.quantity}</span>
                    <button
                      onClick={() => updateQuantity(item.product.id, item.quantity + 1)}
                      className="p-2 hover:bg-gray-100"
                    >
                      <Plus className="w-4 h-4" />
                    </button>
                  </div>
                  
                  <button
                    onClick={() => removeItem(item.product.id)}
                    className="text-red-600 hover:text-red-700"
                  >
                    <Trash2 className="w-5 h-5" />
                  </button>
                </div>
              </div>
              
              <div className="text-right">
                <p className="font-bold text-lg">
                  ${(item.product.selling_price * item.quantity).toFixed(2)}
                </p>
              </div>
            </div>
          ))}
        </div>
        
        <div className="lg:col-span-1">
          <div className="border rounded-lg p-6 sticky top-20">
            <h2 className="text-2xl font-bold mb-4">Résumé</h2>
            <div className="space-y-2 mb-4">
              <div className="flex justify-between">
                <span>Sous-total</span>
                <span>${total.toFixed(2)}</span>
              </div>
              <div className="flex justify-between">
                <span>Livraison</span>
                <span className="text-green-600">Gratuite</span>
              </div>
              <div className="border-t pt-2 mt-2">
                <div className="flex justify-between text-xl font-bold">
                  <span>Total</span>
                  <span>${total.toFixed(2)}</span>
                </div>
              </div>
            </div>
            
            <Link href="/checkout" className="block">
              <Button size="lg" className="w-full mb-4">
                Passer la commande
              </Button>
            </Link>
            
            <button
              onClick={clearCart}
              className="text-sm text-gray-600 hover:text-gray-800"
            >
              Vider le panier
            </button>
          </div>
        </div>
      </div>
    </div>
  )
}

