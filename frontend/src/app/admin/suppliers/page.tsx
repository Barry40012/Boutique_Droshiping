'use client'

import { useEffect, useState } from 'react'
import { suppliersApi } from '@/lib/api/client'
import { Supplier } from '@/types'
import { Button } from '@/components/ui/Button'
import { Plus, Wallet } from 'lucide-react'

export default function AdminSuppliersPage() {
  const [suppliers, setSuppliers] = useState<Supplier[]>([])
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    loadSuppliers()
  }, [])

  const loadSuppliers = async () => {
    try {
      const response = await suppliersApi.getAll()
      setSuppliers(response.data.suppliers || [])
    } catch (error) {
      console.error('Error loading suppliers:', error)
    } finally {
      setLoading(false)
    }
  }

  if (loading) {
    return <div>Chargement...</div>
  }

  return (
    <div>
      <div className="flex justify-between items-center mb-8">
        <h1 className="text-3xl font-bold">Gestion des Fournisseurs</h1>
        <Button>
          <Plus className="w-4 h-4 mr-2" />
          Nouveau Fournisseur
        </Button>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {suppliers.map((supplier) => (
          <div key={supplier.id} className="bg-white rounded-lg border p-6">
            <div className="flex items-center justify-between mb-4">
              <h3 className="text-xl font-semibold">{supplier.name}</h3>
              <span className={`px-2 py-1 rounded text-xs ${
                supplier.status === 'active' 
                  ? 'bg-green-100 text-green-800' 
                  : 'bg-gray-100 text-gray-800'
              }`}>
                {supplier.status}
              </span>
            </div>
            
            <div className="space-y-2 mb-4">
              <p className="text-sm text-gray-600">
                Type: <span className="font-medium">{supplier.api_type}</span>
              </p>
              <div className="flex items-center gap-2">
                <Wallet className="w-4 h-4 text-gray-400" />
                <span className="text-lg font-bold text-green-600">
                  ${supplier.wallet_balance.toFixed(2)}
                </span>
              </div>
            </div>

            <Button variant="outline" className="w-full">
              Gérer
            </Button>
          </div>
        ))}

        {suppliers.length === 0 && (
          <div className="col-span-full text-center py-12 text-gray-500">
            Aucun fournisseur. Ajoutez votre premier fournisseur !
          </div>
        )}
      </div>
    </div>
  )
}

