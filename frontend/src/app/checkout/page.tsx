'use client'

import { useState } from 'react'
import { useCartStore } from '@/store/cartStore'
import { Button } from '@/components/ui/Button'
import { ordersApi, paymentsApi } from '@/lib/api/client'
import { useRouter } from 'next/navigation'
import { trackPurchase } from '@/components/FacebookPixel'

export default function CheckoutPage() {
  const router = useRouter()
  const { items, getTotal, clearCart } = useCartStore()
  const total = getTotal()
  const [loading, setLoading] = useState(false)
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    phone: '',
    street: '',
    city: '',
    country: '',
    postalCode: '',
  })

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    setLoading(true)

    try {
      // Créer la commande
      const orderData = {
        total_amount: total,
        shipping_address: {
          name: formData.name,
          street: formData.street,
          city: formData.city,
          country: formData.country,
          postalCode: formData.postalCode,
          phone: formData.phone,
        },
        items: items.map(item => ({
          product_id: item.product.id,
          quantity: item.quantity,
          unit_price: item.product.selling_price,
          supplier_price: item.product.supplier_price,
        })),
      }

      const orderResponse = await ordersApi.create(orderData)
      const order = orderResponse.data.order

      // Créer le paiement
      const paymentResponse = await paymentsApi.create({
        orderId: order.id,
        customerEmail: formData.email,
        customerName: formData.name,
      })

      // Rediriger vers le paiement
      if (paymentResponse.data.payment_url) {
        window.location.href = paymentResponse.data.payment_url
      } else {
        throw new Error('URL de paiement non reçue')
      }
    } catch (error: any) {
      console.error('Erreur checkout:', error)
      alert('Erreur lors de la création de la commande. Veuillez réessayer.')
      setLoading(false)
    }
  }

  if (items.length === 0) {
    router.push('/cart')
    return null
  }

  return (
    <div className="container mx-auto px-4 py-8">
      <h1 className="text-4xl font-bold mb-8">Checkout</h1>
      
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <form onSubmit={handleSubmit} className="space-y-6">
          <div>
            <h2 className="text-2xl font-semibold mb-4">Informations de livraison</h2>
            
            <div className="space-y-4">
              <div>
                <label htmlFor="name" className="block mb-2 font-medium">
                  Nom complet *
                </label>
                <input
                  id="name"
                  type="text"
                  required
                  value={formData.name}
                  onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                  className="w-full px-4 py-2 border rounded-md"
                />
              </div>
              
              <div>
                <label htmlFor="email" className="block mb-2 font-medium">
                  Email *
                </label>
                <input
                  id="email"
                  type="email"
                  required
                  value={formData.email}
                  onChange={(e) => setFormData({ ...formData, email: e.target.value })}
                  className="w-full px-4 py-2 border rounded-md"
                />
              </div>
              
              <div>
                <label htmlFor="phone" className="block mb-2 font-medium">
                  Téléphone *
                </label>
                <input
                  id="phone"
                  type="tel"
                  required
                  value={formData.phone}
                  onChange={(e) => setFormData({ ...formData, phone: e.target.value })}
                  className="w-full px-4 py-2 border rounded-md"
                />
              </div>
              
              <div>
                <label htmlFor="street" className="block mb-2 font-medium">
                  Adresse *
                </label>
                <input
                  id="street"
                  type="text"
                  required
                  value={formData.street}
                  onChange={(e) => setFormData({ ...formData, street: e.target.value })}
                  className="w-full px-4 py-2 border rounded-md"
                />
              </div>
              
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label htmlFor="city" className="block mb-2 font-medium">
                    Ville *
                  </label>
                  <input
                    id="city"
                    type="text"
                    required
                    value={formData.city}
                    onChange={(e) => setFormData({ ...formData, city: e.target.value })}
                    className="w-full px-4 py-2 border rounded-md"
                  />
                </div>
                
                <div>
                  <label htmlFor="postalCode" className="block mb-2 font-medium">
                    Code postal *
                  </label>
                  <input
                    id="postalCode"
                    type="text"
                    required
                    value={formData.postalCode}
                    onChange={(e) => setFormData({ ...formData, postalCode: e.target.value })}
                    className="w-full px-4 py-2 border rounded-md"
                  />
                </div>
              </div>
              
              <div>
                <label htmlFor="country" className="block mb-2 font-medium">
                  Pays *
                </label>
                <input
                  id="country"
                  type="text"
                  required
                  value={formData.country}
                  onChange={(e) => setFormData({ ...formData, country: e.target.value })}
                  className="w-full px-4 py-2 border rounded-md"
                />
              </div>
            </div>
          </div>
          
          <Button type="submit" size="lg" className="w-full" disabled={loading}>
            {loading ? 'Traitement...' : `Payer $${total.toFixed(2)}`}
          </Button>
        </form>
        
        <div className="border rounded-lg p-6 h-fit">
          <h2 className="text-2xl font-semibold mb-4">Résumé de la commande</h2>
          <div className="space-y-2 mb-4">
            {items.map((item) => (
              <div key={item.product.id} className="flex justify-between text-sm">
                <span>{item.product.name} x{item.quantity}</span>
                <span>${(item.product.selling_price * item.quantity).toFixed(2)}</span>
              </div>
            ))}
          </div>
          <div className="border-t pt-4">
            <div className="flex justify-between text-xl font-bold">
              <span>Total</span>
              <span>${total.toFixed(2)}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  )
}

