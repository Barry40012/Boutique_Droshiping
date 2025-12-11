import React from 'react'
import { Layout } from './Layout'

export default function Home({ products = [] }) {
  return (
    <Layout>
      <div className="hero">
        <div className="hero-content">
          <p className="hero-kicker">Dropshipping automatisé</p>
          <h1 className="hero-title">Lance ta boutique en quelques minutes</h1>
          <p className="hero-subtitle">Paiement carte, automatisation fournisseur, dashboard admin. Tout est prêt.</p>
          <a href="/products" className="btn-primary accent">
            Voir tous les produits
          </a>
        </div>
      </div>

      <h2 className="section-title">Produits populaires</h2>
      <div className="grid">
        {products.length === 0 && (
          <p className="text-gray-600 col-span-full text-center">Aucun produit pour le moment.</p>
        )}
        {products.map((product) => (
          <div className="card" key={product.id}>
            <h3 className="card-title">{product.name}</h3>
            <p className="card-text">{product.description}</p>
            <div className="card-price">
              <div className="price-main">${product.selling_price?.toFixed(2)}</div>
              <div className="price-old">${product.supplier_price?.toFixed(2)}</div>
            </div>
            <div className="card-actions">
              <a href={`/products/${product.id}`} className="btn-ghost">Détails</a>
              <form action={`/cart/add/${product.id}`} method="POST">
                <input type="hidden" name="_token" value={document.querySelector('meta[name=csrf-token]')?.content} />
                <button type="submit" className="btn-primary">Ajouter</button>
              </form>
            </div>
          </div>
        ))}
      </div>
    </Layout>
  )
}

