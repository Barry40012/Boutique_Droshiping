(() => {
  const rootEl = document.getElementById('react-cart')
  if (!rootEl || !window.React || !window.ReactDOM) return

  const cartData = (() => {
    try {
      return JSON.parse(rootEl.dataset.cart || '{}')
    } catch (e) {
      return {}
    }
  })()

  const total = parseFloat(rootEl.dataset.total || 0)

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

  // Composant Item Cart
  const CartItem = ({ item, productId, onUpdate, onRemove }) => {
    const [quantity, setQuantity] = React.useState(item.quantity)
    const [isUpdating, setIsUpdating] = React.useState(false)

    const handleUpdate = () => {
      setIsUpdating(true)
      const form = document.createElement('form')
      form.method = 'POST'
      form.action = `/cart/update/${productId}`
      
      const csrf = document.createElement('input')
      csrf.type = 'hidden'
      csrf.name = '_token'
      csrf.value = document.querySelector('meta[name=csrf-token]')?.content
      form.appendChild(csrf)
      
      const qtyInput = document.createElement('input')
      qtyInput.type = 'hidden'
      qtyInput.name = 'quantity'
      qtyInput.value = quantity
      form.appendChild(qtyInput)
      
      document.body.appendChild(form)
      form.submit()
    }

    const handleRemove = () => {
      if (confirm('Supprimer ce produit du panier ?')) {
        const form = document.createElement('form')
        form.method = 'POST'
        form.action = `/cart/remove/${productId}`
        
        const csrf = document.createElement('input')
        csrf.type = 'hidden'
        csrf.name = '_token'
        csrf.value = document.querySelector('meta[name=csrf-token]')?.content
        form.appendChild(csrf)
        
        document.body.appendChild(form)
        form.submit()
      }
    }

    const subtotal = item.product.selling_price * quantity
    const image = item.product.images && item.product.images.length > 0 ? item.product.images[0] : null

    return withAOS(
      h('div', { className: 'cart-item' }, [
        h('div', { className: 'cart-item-image' }, [
          image 
            ? h('img', { src: image, alt: item.product.name })
            : h('div', { className: 'cart-item-placeholder' }, h(Icon, { name: 'image' }))
        ]),
        h('div', { className: 'cart-item-info' }, [
          h('h3', { className: 'cart-item-name' }, item.product.name),
          item.product.description && h('p', { className: 'cart-item-desc' }, 
            item.product.description.length > 80 
              ? item.product.description.substring(0, 80) + '...' 
              : item.product.description
          ),
          h('div', { className: 'cart-item-price' }, `$${Number(item.product.selling_price).toFixed(2)}`)
        ]),
        h('div', { className: 'cart-item-quantity' }, [
          h('label', { className: 'quantity-label' }, 'Quantité'),
          h('div', { className: 'quantity-controls' }, [
            h('button', {
              type: 'button',
              className: 'quantity-btn',
              onClick: () => setQuantity(Math.max(1, quantity - 1))
            }, h(Icon, { name: 'minus' })),
            h('input', {
              type: 'number',
              value: quantity,
              min: 1,
              className: 'quantity-input',
              onChange: (e) => setQuantity(Math.max(1, parseInt(e.target.value) || 1))
            }),
            h('button', {
              type: 'button',
              className: 'quantity-btn',
              onClick: () => setQuantity(quantity + 1)
            }, h(Icon, { name: 'plus' }))
          ]),
          h('button', {
            type: 'button',
            className: 'btn-update-quantity',
            onClick: handleUpdate,
            disabled: isUpdating || quantity === item.quantity
          }, isUpdating ? '...' : 'Mettre à jour')
        ]),
        h('div', { className: 'cart-item-subtotal' }, [
          h('div', { className: 'subtotal-label' }, 'Sous-total'),
          h('div', { className: 'subtotal-value' }, `$${subtotal.toFixed(2)}`)
        ]),
        h('button', {
          type: 'button',
          className: 'cart-item-remove',
          onClick: handleRemove,
          title: 'Supprimer'
        }, h(Icon, { name: 'trash' }))
      ]),
      'fade-up',
      0
    )
  }

  // Composant Cart Summary
  const CartSummary = ({ total, itemCount }) => {
    return withAOS(
      h('div', { className: 'cart-summary' }, [
        h('h2', { className: 'summary-title' }, [
          h(Icon, { name: 'receipt', className: 'icon-inline' }),
          ' Résumé de la commande'
        ]),
        h('div', { className: 'summary-details' }, [
          h('div', { className: 'summary-row' }, [
            h('span', { className: 'summary-label' }, 'Articles'),
            h('span', { className: 'summary-value' }, itemCount)
          ]),
          h('div', { className: 'summary-row' }, [
            h('span', { className: 'summary-label' }, 'Sous-total'),
            h('span', { className: 'summary-value' }, `$${total.toFixed(2)}`)
          ]),
          h('div', { className: 'summary-row summary-shipping' }, [
            h('span', { className: 'summary-label' }, [
              h(Icon, { name: 'shipping-fast', className: 'icon-inline' }),
              ' Livraison'
            ]),
            h('span', { className: 'summary-value summary-free' }, 'Gratuite')
          ]),
          h('div', { className: 'summary-divider' }),
          h('div', { className: 'summary-row summary-total' }, [
            h('span', { className: 'summary-label' }, 'Total'),
            h('span', { className: 'summary-value' }, `$${total.toFixed(2)}`)
          ])
        ]),
        h('a', {
          href: '/checkout',
          className: 'btn-primary btn-large btn-checkout'
        }, [
          h(Icon, { name: 'lock', className: 'icon-inline' }),
          ' Passer la commande'
        ]),
        h('div', { className: 'summary-security' }, [
          h(Icon, { name: 'shield-alt', className: 'security-icon' }),
          h('span', { className: 'security-text' }, 'Paiement 100% sécurisé')
        ])
      ]),
      'fade-left',
      100
    )
  }

  // Composant Principal
  const CartPage = () => {
    const cartItems = Object.entries(cartData || {})
    const itemCount = cartItems.reduce((sum, [, item]) => sum + item.quantity, 0)

    if (cartItems.length === 0) {
      return withAOS(
        h('div', { className: 'cart-empty' }, [
          h(Icon, { name: 'shopping-cart', className: 'empty-icon' }),
          h('h2', { className: 'empty-title' }, 'Votre panier est vide'),
          h('p', { className: 'empty-text' }, 'Ajoutez des produits à votre panier pour commencer vos achats.'),
          h('a', { href: '/products', className: 'btn-primary' }, [
            h(Icon, { name: 'arrow-left', className: 'icon-inline' }),
            ' Continuer les achats'
          ])
        ]),
        'fade-up',
        0
      )
    }

    return h('div', { className: 'cart-container' }, [
      h('div', { className: 'cart-header', 'data-aos': 'fade-down' }, [
        h('h1', { className: 'cart-title' }, [
          h(Icon, { name: 'shopping-cart', className: 'icon-inline' }),
          ' Mon panier'
        ]),
        h('div', { className: 'cart-count' }, `${itemCount} ${itemCount > 1 ? 'articles' : 'article'}`)
      ]),
      h('div', { className: 'cart-content' }, [
        h('div', { className: 'cart-items' }, 
          cartItems.map(([productId, item], index) =>
            h(CartItem, {
              key: productId,
              item: item,
              productId: productId,
              onUpdate: () => {},
              onRemove: () => {}
            })
          )
        ),
        h(CartSummary, { total: total, itemCount: itemCount })
      ])
    ])
  }

  const root = createRoot(rootEl)
  root.render(h(CartPage))
  
  // Réinitialiser AOS après le rendu React
  setTimeout(() => {
    if (window.AOS) {
      AOS.refresh()
    }
  }, 100)
})()

