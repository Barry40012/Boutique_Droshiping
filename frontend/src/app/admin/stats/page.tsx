'use client'

import { useEffect, useState } from 'react'
import { statsApi } from '@/lib/api/client'
import { BarChart3, DollarSign, ShoppingCart, Package } from 'lucide-react'

export default function AdminStatsPage() {
  const [stats, setStats] = useState<any>(null)
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    loadStats()
  }, [])

  const loadStats = async () => {
    try {
      const response = await statsApi.get()
      setStats(response.data)
    } catch (error) {
      console.error('Error loading stats:', error)
    } finally {
      setLoading(false)
    }
  }

  if (loading) {
    return <div>Chargement...</div>
  }

  if (!stats) {
    return <div>Erreur lors du chargement des statistiques</div>
  }

  return (
    <div>
      <h1 className="text-3xl font-bold mb-8">Statistiques</h1>

      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div className="bg-white p-6 rounded-lg border">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-gray-600 text-sm mb-1">Revenus Total</p>
              <p className="text-3xl font-bold">${stats.revenue?.total?.toFixed(2) || '0.00'}</p>
            </div>
            <DollarSign className="w-10 h-10 text-green-600" />
          </div>
        </div>

        <div className="bg-white p-6 rounded-lg border">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-gray-600 text-sm mb-1">Marge Totale</p>
              <p className="text-3xl font-bold">${stats.revenue?.margin?.toFixed(2) || '0.00'}</p>
            </div>
            <BarChart3 className="w-10 h-10 text-blue-600" />
          </div>
        </div>

        <div className="bg-white p-6 rounded-lg border">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-gray-600 text-sm mb-1">Commandes</p>
              <p className="text-3xl font-bold">{stats.orders?.total || 0}</p>
              <p className="text-sm text-gray-500 mt-1">{stats.orders?.paid || 0} payées</p>
            </div>
            <ShoppingCart className="w-10 h-10 text-purple-600" />
          </div>
        </div>

        <div className="bg-white p-6 rounded-lg border">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-gray-600 text-sm mb-1">Produits</p>
              <p className="text-3xl font-bold">{stats.products?.total || 0}</p>
              <p className="text-sm text-gray-500 mt-1">{stats.products?.active || 0} actifs</p>
            </div>
            <Package className="w-10 h-10 text-orange-600" />
          </div>
        </div>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div className="bg-white p-6 rounded-lg border">
          <h2 className="text-xl font-semibold mb-4">Fournisseurs</h2>
          <div className="space-y-2">
            <div className="flex justify-between">
              <span>Total Fournisseurs</span>
              <span className="font-semibold">{stats.suppliers?.total || 0}</span>
            </div>
            <div className="flex justify-between">
              <span>Balance Wallet Totale</span>
              <span className="font-semibold text-green-600">
                ${stats.suppliers?.total_wallet_balance?.toFixed(2) || '0.00'}
              </span>
            </div>
          </div>
        </div>

        <div className="bg-white p-6 rounded-lg border">
          <h2 className="text-xl font-semibold mb-4">Commandes</h2>
          <div className="space-y-2">
            <div className="flex justify-between">
              <span>Total</span>
              <span className="font-semibold">{stats.orders?.total || 0}</span>
            </div>
            <div className="flex justify-between">
              <span>Payées</span>
              <span className="font-semibold text-green-600">{stats.orders?.paid || 0}</span>
            </div>
            <div className="flex justify-between">
              <span>En attente</span>
              <span className="font-semibold text-yellow-600">{stats.orders?.pending || 0}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  )
}

