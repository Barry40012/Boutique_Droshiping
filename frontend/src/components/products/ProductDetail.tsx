'use client'

import Image from 'next/image'
import { Product } from '@/types'
import { Button } from '@/components/ui/Button'
import { useCartStore } from '@/store/cartStore'
import { useState } from 'react'
import { trackAddToCart } from '@/components/FacebookPixel'

interface ProductDetailProps {
  product: Product
}

export function ProductDetail({ product }: ProductDetailProps) {
  const [quantity, setQuantity] = useState(1)
  const addItem = useCartStore((state) => state.addItem)

  const handleAddToCart = () => {
    addItem(product, quantity)
    trackAddToCart(product.selling_price * quantity)
  }

  return (
    <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
      {/* Images */}
      <div>
        {product.images && product.images.length > 0 ? (
          <div className="relative w-full h-96 bg-gray-100 rounded-lg overflow-hidden">
            <Image
              src={product.images[0]}
              alt={product.name}
              fill
              className="object-cover"
            />
          </div>
        ) : (
          <div className="w-full h-96 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400">
            Pas d'image disponible
          </div>
        )}
      </div>

      {/* Details */}
      <div className="space-y-6">
        <div>
          <h1 className="text-4xl font-bold mb-4">{product.name}</h1>
          {product.description && (
            <p className="text-gray-600 text-lg mb-4">{product.description}</p>
          )}
        </div>

        <div className="space-y-4">
          <div>
            <p className="text-4xl font-bold text-primary">
              ${product.selling_price.toFixed(2)}
            </p>
            {product.supplier_price && (
              <p className="text-lg text-gray-500 line-through">
                ${product.supplier_price.toFixed(2)}
              </p>
            )}
          </div>

          <div className="flex items-center gap-4">
            <label htmlFor="quantity" className="font-medium">
              Quantité:
            </label>
            <input
              id="quantity"
              type="number"
              min="1"
              value={quantity}
              onChange={(e) => setQuantity(parseInt(e.target.value) || 1)}
              className="w-20 px-3 py-2 border rounded-md"
            />
          </div>

          <Button
            onClick={handleAddToCart}
            size="lg"
            className="w-full"
            disabled={product.status !== 'active'}
          >
            {product.status === 'active' 
              ? `Ajouter au panier - $${(product.selling_price * quantity).toFixed(2)}`
              : 'Indisponible'
            }
          </Button>

          {product.status === 'active' && (
            <div className="text-sm text-gray-600">
              ✓ Livraison gratuite
              <br />
              ✓ Paiement sécurisé
            </div>
          )}
        </div>
      </div>
    </div>
  )
}

