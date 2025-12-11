import Image from 'next/image'
import { productsApi } from '@/lib/api/client'
import { ProductDetail } from '@/components/products/ProductDetail'
import { notFound } from 'next/navigation'

interface ProductPageProps {
  params: {
    id: string
  }
}

export default async function ProductPage({ params }: ProductPageProps) {
  try {
    const response = await productsApi.getById(params.id)
    const product = response.data.product

    if (!product) {
      notFound()
    }

    return (
      <div className="container mx-auto px-4 py-8">
        <ProductDetail product={product} />
      </div>
    )
  } catch (error) {
    console.error('Error fetching product:', error)
    notFound()
  }
}

