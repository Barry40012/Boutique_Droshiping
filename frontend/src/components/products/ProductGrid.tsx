import { productsApi } from '@/lib/api/client'
import { ProductCard } from './ProductCard'

interface ProductGridProps {
  limit?: number
}

export async function ProductGrid({ limit }: ProductGridProps) {
  try {
    const response = await productsApi.getAll()
    const products = response.data.products || []
    const displayedProducts = limit ? products.slice(0, limit) : products

    if (displayedProducts.length === 0) {
      return (
        <div className="text-center py-12">
          <p className="text-gray-600">Aucun produit disponible pour le moment.</p>
        </div>
      )
    }

    return (
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        {displayedProducts.map((product: any) => (
          <ProductCard key={product.id} product={product} />
        ))}
      </div>
    )
  } catch (error) {
    console.error('Error fetching products:', error)
    return (
      <div className="text-center py-12">
        <p className="text-red-600">Erreur lors du chargement des produits.</p>
      </div>
    )
  }
}

