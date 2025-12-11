(() => {
  const rootEl = document.getElementById('react-home')
  if (!rootEl || !window.React || !window.ReactDOM) return

  const products = (() => {
    try {
      return JSON.parse(rootEl.dataset.products || '[]')
    } catch (e) {
      return []
    }
  })()

  const { createElement: h } = React
  const { createRoot } = ReactDOM

  const Hero = () =>
    h('div', { className: 'hero' },
      h('div', { className: 'hero-content' }, [
        h('p', { className: 'hero-kicker' }, 'Dropshipping automatisé'),
        h('h1', { className: 'hero-title' }, 'Lance ta boutique en quelques minutes'),
        h('p', { className: 'hero-subtitle' }, 'Paiement carte, automatisation fournisseur, dashboard admin. Tout est prêt.'),
        h('a', { href: '/products', className: 'btn-primary accent' }, 'Voir tous les produits')
      ])
    )

  const ProductCard = ({ product }) =>
    h('div', { className: 'card' }, [
      h('h3', { className: 'card-title' }, product.name),
      h('p', { className: 'card-text' }, product.description),
      h('div', { className: 'card-price' }, [
        h('div', { className: 'price-main' }, `$${Number(product.selling_price || 0).toFixed(2)}`),
        h('div', { className: 'price-old' }, `$${Number(product.supplier_price || 0).toFixed(2)}`),
      ]),
      h('div', { className: 'card-actions' }, [
        h('a', { href: `/products/${product.id}`, className: 'btn-ghost' }, 'Détails'),
        h('form', { action: `/cart/add/${product.id}`, method: 'POST' }, [
          h('input', { type: 'hidden', name: '_token', value: document.querySelector('meta[name=csrf-token]')?.content }),
          h('button', { type: 'submit', className: 'btn-primary' }, 'Ajouter')
        ])
      ])
    ])

  const Home = () =>
    h(React.Fragment, null, [
      h(Hero),
      h('h2', { className: 'section-title' }, 'Produits populaires'),
      products.length === 0
        ? h('p', { className: 'text-gray-600 col-span-full text-center' }, 'Aucun produit pour le moment.')
        : h('div', { className: 'grid' },
            products.map((p) => h(ProductCard, { key: p.id, product: p }))
          )
    ])

  const root = createRoot(rootEl)
  root.render(h(Home))
})()

