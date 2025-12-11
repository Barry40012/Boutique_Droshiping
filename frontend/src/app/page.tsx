import Link from 'next/link'
import { Button } from '@/components/ui/Button'
import { ProductGrid } from '@/components/products/ProductGrid'

export default async function Home() {
  return (
    <div className="container mx-auto px-4 py-8">
      {/* Hero Section */}
      <div className="text-center space-y-6 mb-16">
        <h1 className="text-4xl md:text-6xl font-bold">
          Bienvenue dans votre Boutique Dropshipping
        </h1>
        <p className="text-xl text-gray-600 max-w-2xl mx-auto">
          Découvrez notre sélection de produits de qualité, livrés directement chez vous.
        </p>
        <div className="flex gap-4 justify-center">
          <Link href="/products">
            <Button size="lg">
              Voir tous les produits
            </Button>
          </Link>
        </div>
      </div>

      {/* Products Section */}
      <div className="mb-16">
        <h2 className="text-3xl font-bold mb-8 text-center">Nos Produits Populaires</h2>
        <ProductGrid limit={8} />
      </div>
    </div>
  )
}

