export function Footer() {
  return (
    <footer className="border-t bg-gray-50 mt-auto">
      <div className="container mx-auto px-4 py-8">
        <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div>
            <h3 className="font-bold text-lg mb-4">Boutique Dropshipping</h3>
            <p className="text-gray-600">
              Votre boutique en ligne de qualité
            </p>
          </div>
          <div>
            <h3 className="font-bold text-lg mb-4">Liens</h3>
            <ul className="space-y-2 text-gray-600">
              <li><a href="/products" className="hover:text-primary">Produits</a></li>
              <li><a href="/cart" className="hover:text-primary">Panier</a></li>
            </ul>
          </div>
          <div>
            <h3 className="font-bold text-lg mb-4">Contact</h3>
            <p className="text-gray-600">
              Support disponible 24/7
            </p>
          </div>
        </div>
        <div className="mt-8 pt-8 border-t text-center text-gray-600">
          <p>&copy; {new Date().getFullYear()} Boutique Dropshipping. Tous droits réservés.</p>
        </div>
      </div>
    </footer>
  )
}

