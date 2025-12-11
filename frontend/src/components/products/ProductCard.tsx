'use client'

import Image from 'next/image'
import Link from 'next/link'
import { Product } from '@/types'
import { Button } from '@/components/ui/Button'
import { useCartStore } from '@/store/cartStore'
import { trackAddToCart } from '@/components/FacebookPixel'

interface ProductCardProps {
  product: Product
}

export function ProductCard({ product }: ProductCardProps) {
  const addItem = useCartStore((state) => state.addItem)

  const handleAddToCart = () => {
    addItem(product, 1)
    trackAddToCart(product.selling_price)
  }

  return (
    <div className="border rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
      <Link href={`/products/${product.id}`}>
        <div className="relative w-full h-64 bg-gray-100">
          {product.images && product.images.length > 0 ? (
            <Image
              src={product.images[0]}
              alt={product.name}
              fill
              className="object-cover"
            />
          ) : (
            <div className="w-full h-full flex items-center justify-center text-gray-400">
              Pas d'image
            </div>
          )}
        </div>
      </Link>
      
      <div className="p-4">
        <Link href={`/products/${product.id}`}>
          <h3 className="font-semibold text-lg mb-2 hover:text-primary transition-colors">
            {product.name}
          </h3>
        </Link>
        
        {product.description && (
          <p className="text-gray-600 text-sm mb-4 line-clamp-2">
            {product.description}
          </p>
        )}
        
        <div className="flex items-center justify-between mb-4">
          <div>
            <p className="text-2xl font-bold text-primary">
              ${product.selling_price.toFixed(2)}
            </p>
            {product.supplier_price && (
              <p className="text-sm text-gray-500 line-through">
                ${product.supplier_price.toFixed(2)}
              </p>
            )}
          </div>
        </div>
        
        <Button 
          onClick={handleAddToCart}
          className="w-full"
          disabled={product.status !== 'active'}
        >
          {product.status === 'active' ? 'Ajouter au panier' : 'Indisponible'}
        </Button>
      </div>
    </div>
  )
}

