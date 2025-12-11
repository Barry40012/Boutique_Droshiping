import Link from 'next/link'
import { LayoutDashboard, Package, ShoppingCart, Users, BarChart3 } from 'lucide-react'

export default function AdminLayout({
  children,
}: {
  children: React.ReactNode
}) {
  return (
    <div className="min-h-screen bg-gray-50">
      <div className="flex">
        {/* Sidebar */}
        <aside className="w-64 bg-white border-r min-h-screen p-6">
          <h1 className="text-2xl font-bold mb-8">Admin</h1>
          <nav className="space-y-2">
            <Link href="/admin" className="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-100">
              <LayoutDashboard className="w-5 h-5" />
              Dashboard
            </Link>
            <Link href="/admin/products" className="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-100">
              <Package className="w-5 h-5" />
              Produits
            </Link>
            <Link href="/admin/orders" className="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-100">
              <ShoppingCart className="w-5 h-5" />
              Commandes
            </Link>
            <Link href="/admin/suppliers" className="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-100">
              <Users className="w-5 h-5" />
              Fournisseurs
            </Link>
            <Link href="/admin/stats" className="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-100">
              <BarChart3 className="w-5 h-5" />
              Statistiques
            </Link>
          </nav>
        </aside>

        {/* Main Content */}
        <main className="flex-1 p-8">
          {children}
        </main>
      </div>
    </div>
  )
}

