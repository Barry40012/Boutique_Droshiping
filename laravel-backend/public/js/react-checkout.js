(() => {
  const rootEl = document.getElementById('react-checkout')
  if (!rootEl || !window.React || !window.ReactDOM) return

  const cartData = (() => {
    try {
      return JSON.parse(rootEl.dataset.cart || '{}')
    } catch (e) {
      return {}
    }
  })()

  const total = parseFloat(rootEl.dataset.total || 0)
  const errors = (() => {
    try {
      return JSON.parse(rootEl.dataset.errors || '{}')
    } catch (e) {
      return {}
    }
  })()

  const oldInput = (() => {
    try {
      return JSON.parse(rootEl.dataset.old || '{}')
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

  // Composant Checkout Form
  const CheckoutForm = ({ errors, oldInput }) => {
    const [formData, setFormData] = React.useState({
      name: oldInput.name || '',
      phone: oldInput.phone || '',
      street: oldInput.street || '',
      city: oldInput.city || '',
      country: oldInput.country || '',
      postalCode: oldInput.postalCode || ''
    })

    const handleChange = (field, value) => {
      setFormData(prev => ({ ...prev, [field]: value }))
    }

    return withAOS(
      h('form', {
        action: '/checkout',
        method: 'POST',
        className: 'checkout-form'
      }, [
        h('input', {
          type: 'hidden',
          name: '_token',
          value: document.querySelector('meta[name=csrf-token]')?.content
        }),
        h('div', { className: 'form-section' }, [
          h('h2', { className: 'section-title' }, [
            h(Icon, { name: 'user', className: 'icon-inline' }),
            ' Informations personnelles'
          ]),
          h('div', { className: 'form-grid' }, [
            h('div', { className: 'form-group' }, [
              h('label', { className: 'form-label' }, 'Nom complet *'),
              h('input', {
                type: 'text',
                name: 'name',
                value: formData.name,
                onChange: (e) => handleChange('name', e.target.value),
                className: `form-input ${errors.name ? 'input-error' : ''}`,
                required: true,
                placeholder: 'Jean Dupont'
              }),
              errors.name && h('span', { className: 'error-message' }, errors.name)
            ]),
            h('div', { className: 'form-group' }, [
              h('label', { className: 'form-label' }, [
                h(Icon, { name: 'phone', className: 'icon-inline' }),
                ' Téléphone *'
              ]),
              h('input', {
                type: 'tel',
                name: 'phone',
                value: formData.phone,
                onChange: (e) => handleChange('phone', e.target.value),
                className: `form-input ${errors.phone ? 'input-error' : ''}`,
                required: true,
                placeholder: '+33 6 12 34 56 78'
              }),
              errors.phone && h('span', { className: 'error-message' }, errors.phone)
            ])
          ])
        ]),
        h('div', { className: 'form-section' }, [
          h('h2', { className: 'section-title' }, [
            h(Icon, { name: 'map-marker-alt', className: 'icon-inline' }),
            ' Adresse de livraison'
          ]),
          h('div', { className: 'form-group' }, [
            h('label', { className: 'form-label' }, 'Adresse *'),
            h('input', {
              type: 'text',
              name: 'street',
              value: formData.street,
              onChange: (e) => handleChange('street', e.target.value),
              className: `form-input ${errors.street ? 'input-error' : ''}`,
              required: true,
              placeholder: '123 Rue de la République'
            }),
            errors.street && h('span', { className: 'error-message' }, errors.street)
          ]),
          h('div', { className: 'form-grid' }, [
            h('div', { className: 'form-group' }, [
              h('label', { className: 'form-label' }, 'Ville *'),
              h('input', {
                type: 'text',
                name: 'city',
                value: formData.city,
                onChange: (e) => handleChange('city', e.target.value),
                className: `form-input ${errors.city ? 'input-error' : ''}`,
                required: true,
                placeholder: 'Paris'
              }),
              errors.city && h('span', { className: 'error-message' }, errors.city)
            ]),
            h('div', { className: 'form-group' }, [
              h('label', { className: 'form-label' }, 'Pays *'),
              h('input', {
                type: 'text',
                name: 'country',
                value: formData.country,
                onChange: (e) => handleChange('country', e.target.value),
                className: `form-input ${errors.country ? 'input-error' : ''}`,
                required: true,
                placeholder: 'France'
              }),
              errors.country && h('span', { className: 'error-message' }, errors.country)
            ]),
            h('div', { className: 'form-group' }, [
              h('label', { className: 'form-label' }, 'Code postal *'),
              h('input', {
                type: 'text',
                name: 'postalCode',
                value: formData.postalCode,
                onChange: (e) => handleChange('postalCode', e.target.value),
                className: `form-input ${errors.postalCode ? 'input-error' : ''}`,
                required: true,
                placeholder: '75001'
              }),
              errors.postalCode && h('span', { className: 'error-message' }, errors.postalCode)
            ])
          ])
        ]),
        h('div', { className: 'form-actions' }, [
          h('a', {
            href: '/cart',
            className: 'btn-secondary'
          }, [
            h(Icon, { name: 'arrow-left', className: 'icon-inline' }),
            ' Retour au panier'
          ]),
          h('button', {
            type: 'submit',
            className: 'btn-primary btn-large'
          }, [
            'Confirmer la commande',
            h(Icon, { name: 'arrow-right', className: 'icon-inline' })
          ])
        ])
      ]),
      'fade-right',
      0
    )
  }

  // Composant Order Summary
  const OrderSummary = ({ cart, total }) => {
    const cartItems = Object.entries(cart || {})
    const itemCount = cartItems.reduce((sum, [, item]) => sum + item.quantity, 0)

    return withAOS(
      h('div', { className: 'order-summary' }, [
        h('h2', { className: 'summary-title' }, [
          h(Icon, { name: 'receipt', className: 'icon-inline' }),
          ' Résumé de la commande'
        ]),
        h('div', { className: 'summary-items' }, 
          cartItems.map(([productId, item]) =>
            h('div', { key: productId, className: 'summary-item' }, [
              h('div', { className: 'summary-item-info' }, [
                h('span', { className: 'summary-item-name' }, item.product.name),
                h('span', { className: 'summary-item-qty' }, `x${item.quantity}`)
              ]),
              h('span', { className: 'summary-item-price' }, 
                `$${(item.product.selling_price * item.quantity).toFixed(2)}`
              )
            ])
          )
        ),
        h('div', { className: 'summary-divider' }),
        h('div', { className: 'summary-totals' }, [
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
  const CheckoutPage = () => {
    const cartItems = Object.entries(cartData || {})

    if (cartItems.length === 0) {
      return withAOS(
        h('div', { className: 'checkout-empty' }, [
          h(Icon, { name: 'shopping-cart', className: 'empty-icon' }),
          h('h2', { className: 'empty-title' }, 'Votre panier est vide'),
          h('p', { className: 'empty-text' }, 'Ajoutez des produits à votre panier avant de passer commande.'),
          h('a', { href: '/products', className: 'btn-primary' }, [
            h(Icon, { name: 'arrow-left', className: 'icon-inline' }),
            ' Continuer les achats'
          ])
        ]),
        'fade-up',
        0
      )
    }

    return h('div', { className: 'checkout-container' }, [
      h('div', { className: 'checkout-header', 'data-aos': 'fade-down' }, [
        h('h1', { className: 'checkout-title' }, [
          h(Icon, { name: 'credit-card', className: 'icon-inline' }),
          ' Finaliser votre commande'
        ]),
        h('div', { className: 'checkout-steps' }, [
          h('div', { className: 'step active' }, [
            h(Icon, { name: 'check-circle', className: 'icon-inline' }),
            ' Panier'
          ]),
          h('div', { className: 'step active' }, [
            h(Icon, { name: 'check-circle', className: 'icon-inline' }),
            ' Commande'
          ]),
          h('div', { className: 'step' }, [
            h(Icon, { name: 'circle', className: 'icon-inline' }),
            ' Paiement'
          ])
        ])
      ]),
      h('div', { className: 'checkout-content' }, [
        h(CheckoutForm, { errors: errors, oldInput: oldInput }),
        h(OrderSummary, { cart: cartData, total: total })
      ])
    ])
  }

  const root = createRoot(rootEl)
  root.render(h(CheckoutPage))
  
  // Réinitialiser AOS après le rendu React
  setTimeout(() => {
    if (window.AOS) {
      AOS.refresh()
    }
  }, 100)
})()

