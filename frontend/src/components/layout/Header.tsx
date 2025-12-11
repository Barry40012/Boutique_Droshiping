'use client'

import Link from 'next/link'
import { useCartStore } from '@/store/cartStore'
import { ShoppingCart } from 'lucide-react'

export function Header() {
  const itemCount = useCartStore((state) => state.getItemCount())

  return (
    <header className="border-b bg-white sticky top-0 z-50">
      <div className="container mx-auto px-4">
        <div className="flex items-center justify-between h-16">
          <Link href="/" className="text-2xl font-bold">
            🛍️ Boutique
          </Link>
          
          <nav className="flex items-center gap-6">
            <Link href="/products" className="hover:text-primary transition-colors">
              Produits
            </Link>
            <Link href="/cart" className="relative hover:text-primary transition-colors">
              <ShoppingCart className="w-6 h-6" />
              {itemCount > 0 && (
                <span className="absolute -top-2 -right-2 bg-primary text-primary-foreground text-xs rounded-full w-5 h-5 flex items-center justify-center">
                  {itemCount}
                </span>
              )}
            </Link>
            <Link href="/admin" className="hover:text-primary transition-colors">
              Admin
            </Link>
          </nav>
        </div>
      </div>
    </header>
  )
}

