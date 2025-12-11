import { statsApi } from '@/lib/api/client'
import { BarChart3, Package, ShoppingCart, DollarSign } from 'lucide-react'

export default async function AdminDashboard() {
  let stats = null
  try {
    const response = await statsApi.get()
    stats = response.data
  } catch (error) {
    console.error('Error fetching stats:', error)
  }

  return (
    <div>
      <h1 className="text-3xl font-bold mb-8">Dashboard</h1>
      
      {stats && (
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <div className="bg-white p-6 rounded-lg border">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-gray-600 text-sm">Revenus Total</p>
                <p className="text-2xl font-bold">${stats.revenue?.total?.toFixed(2) || '0.00'}</p>
              </div>
              <DollarSign className="w-8 h-8 text-green-600" />
            </div>
          </div>
          
          <div className="bg-white p-6 rounded-lg border">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-gray-600 text-sm">Commandes</p>
                <p className="text-2xl font-bold">{stats.orders?.total || 0}</p>
                <p className="text-sm text-gray-500">{stats.orders?.paid || 0} payées</p>
              </div>
              <ShoppingCart className="w-8 h-8 text-blue-600" />
            </div>
          </div>
          
          <div className="bg-white p-6 rounded-lg border">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-gray-600 text-sm">Produits</p>
                <p className="text-2xl font-bold">{stats.products?.total || 0}</p>
                <p className="text-sm text-gray-500">{stats.products?.active || 0} actifs</p>
              </div>
              <Package className="w-8 h-8 text-purple-600" />
            </div>
          </div>
          
          <div className="bg-white p-6 rounded-lg border">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-gray-600 text-sm">Marge</p>
                <p className="text-2xl font-bold">${stats.revenue?.margin?.toFixed(2) || '0.00'}</p>
              </div>
              <BarChart3 className="w-8 h-8 text-orange-600" />
            </div>
          </div>
        </div>
      )}
      
      <div className="bg-white p-6 rounded-lg border">
        <h2 className="text-xl font-semibold mb-4">Bienvenue dans le Dashboard Admin</h2>
        <p className="text-gray-600">
          Gérez vos produits, commandes, fournisseurs et consultez vos statistiques depuis ici.
        </p>
      </div>
    </div>
  )
}

