(() => {
  const rootEl = document.getElementById('react-products')
  if (!rootEl || !window.React || !window.ReactDOM) return

  const products = (() => {
    try {
      return JSON.parse(rootEl.dataset.products || '[]')
    } catch (e) {
      return []
    }
  })()

  const pagination = (() => {
    try {
      return JSON.parse(rootEl.dataset.pagination || '{}')
    } catch (e) {
      return {}
    }
  })()

  const { createElement: h } = React
  const { createRoot } = ReactDOM

  // Helper pour créer des icônes Font Awesome
  const Icon = ({ name, className = '' }) => 
    h('i', { className: `fas fa-${name} ${className}`, 'aria-hidden': 'true' })

  // Helper pour ajouter les attributs AOS
  const withAOS = (element, animation = 'fade-up', delay = 0) => {
    const props = element.props || {}
    return React.cloneElement(element, {
      ...props,
      'data-aos': animation,
      'data-aos-delay': delay,
      'data-aos-duration': 600
    })
  }

  // Composant ProductCard
  const ProductCard = ({ product, delay = 0 }) => {
    const handleAddToCart = (e) => {
      e.preventDefault()
      const form = e.target.closest('form')
      if (form) {
        form.submit()
      }
    }

    return withAOS(
      h('div', { className: 'card product-card' }, [
        h('div', { className: 'product-image-placeholder' }, [
          product.images && product.images.length > 0
            ? h('img', { 
                src: product.images[0], 
                alt: product.name,
                className: 'product-image',
                style: { width: '100%', height: '100%', objectFit: 'cover', borderRadius: '12px' }
              })
            : h(Icon, { name: 'image', className: 'icon-large' })
        ]),
        h('div', { className: 'product-badge', style: { 
          position: 'absolute', 
          top: '12px', 
          right: '12px',
          background: 'linear-gradient(135deg, var(--accent), #ea580c)',
          color: '#fff',
          padding: '4px 12px',
          borderRadius: '20px',
          fontSize: '12px',
          fontWeight: '600',
          zIndex: 1
        } }, 'Nouveau'),
        h('h3', { className: 'card-title' }, product.name),
        h('p', { className: 'card-text' }, product.description || 'Produit de qualité supérieure'),
        h('div', { className: 'card-price' }, [
          h('div', { className: 'price-main' }, `$${Number(product.selling_price || 0).toFixed(2)}`),
          h('div', { className: 'price-old' }, `$${Number(product.supplier_price || 0).toFixed(2)}`),
        ]),
        h('div', { className: 'card-actions' }, [
          h('a', { href: `/products/${product.id}`, className: 'btn-ghost' }, [
            h(Icon, { name: 'eye', className: 'icon-inline' }),
            ' Détails'
          ]),
          h('form', { action: `/cart/add/${product.id}`, method: 'POST', onSubmit: handleAddToCart }, [
            h('input', { type: 'hidden', name: '_token', value: document.querySelector('meta[name=csrf-token]')?.content }),
            h('button', { type: 'submit', className: 'btn-primary' }, [
              h(Icon, { name: 'cart-plus', className: 'icon-inline' }),
              ' Ajouter'
            ])
          ])
        ])
      ]),
      'fade-up',
      delay
    )
  }

  // Composant Hero Section
  const ProductsHero = () =>
    h('div', { className: 'products-hero', 'data-aos': 'fade-in' }, [
      h('div', { className: 'products-hero-content' }, [
        h('div', { className: 'products-icon-wrapper' }, h(Icon, { name: 'box' })),
        h('h1', { className: 'products-title' }, 'Tous nos produits'),
        h('p', { className: 'products-subtitle' }, 'Découvre notre sélection de produits de qualité pour ton dropshipping'),
      ])
    ])

  // Composant Pagination
  const Pagination = ({ pagination }) => {
    if (!pagination || !pagination.links || pagination.links.length <= 3) return null

    const links = pagination.links.filter(link => {
      // Filtrer les liens null et ceux qui sont juste des points (...)
      return link.url !== null && link.label !== '...'
    })
    
    if (links.length <= 1) return null

    return h('div', { className: 'pagination', 'data-aos': 'fade-up' }, [
      h('div', { className: 'pagination-links' },
        links.map((link, idx) => {
          const isActive = link.active || false
          const isDisabled = !link.url || link.url === '#'
          const label = link.label || ''
          
          return h('a', {
            key: idx,
            href: link.url || '#',
            className: `pagination-link ${isActive ? 'active' : ''} ${isDisabled ? 'disabled' : ''}`,
            onClick: (e) => {
              if (isDisabled) {
                e.preventDefault()
              }
            },
            dangerouslySetInnerHTML: { __html: label }
          })
        })
      )
    ])
  }

  // Composant Principal
  const ProductsPage = () =>
    h(React.Fragment, null, [
      h(ProductsHero),
      h('div', { className: 'section section-products-list' }, [
        products.length === 0
          ? h('div', { className: 'empty-state', 'data-aos': 'fade-up' }, [
              h(Icon, { name: 'box-open', className: 'empty-icon' }),
              h('h2', { className: 'empty-title' }, 'Aucun produit disponible'),
              h('p', { className: 'empty-text' }, 'Reviens bientôt pour découvrir nos nouveaux produits !')
            ])
          : h('div', { className: 'grid grid-products' },
              products.map((p, idx) => h(ProductCard, { key: p.id, product: p, delay: idx * 50 }))
            ),
        pagination && Object.keys(pagination).length > 0 && h(Pagination, { pagination })
      ])
    ])

  const root = createRoot(rootEl)
  root.render(h(ProductsPage))
  
  // Réinitialiser AOS après le rendu React
  setTimeout(() => {
    if (window.AOS) {
      AOS.refresh()
    }
  }, 100)
})()

