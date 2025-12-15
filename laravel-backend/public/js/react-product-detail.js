(() => {
  const rootEl = document.getElementById('react-product-detail')
  if (!rootEl || !window.React || !window.ReactDOM) return

  const product = (() => {
    try {
      return JSON.parse(rootEl.dataset.product || '{}')
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

  // Composant Image Gallery
  const ImageGallery = ({ images, productName }) => {
    let selectedImageState = images && images.length > 0 ? images[0] : null
    const setSelectedImage = (img) => {
      selectedImageState = img
      // Force re-render (simple approach for UMD)
      const event = new Event('imageChanged')
      document.dispatchEvent(event)
    }

    if (!images || images.length === 0) {
      return h('div', { className: 'product-detail-image-main', 'data-aos': 'fade-right' }, [
        h('div', { className: 'product-image-placeholder-large' }, h(Icon, { name: 'image', className: 'icon-large' }))
      ])
    }

    return h('div', { className: 'product-detail-images', 'data-aos': 'fade-right' }, [
      h('div', { className: 'product-detail-image-main' }, [
        h('img', {
          src: selectedImageState || images[0],
          alt: productName,
          className: 'product-main-image',
          key: selectedImageState || images[0]
        })
      ]),
      images.length > 1 && h('div', { className: 'product-detail-image-thumbs' },
        images.map((img, idx) =>
          h('div', {
            key: idx,
            className: `product-thumb ${selectedImageState === img ? 'active' : ''}`,
            onClick: (e) => {
              setSelectedImage(img)
              // Update image immediately
              setTimeout(() => {
                const mainImg = document.querySelector('.product-main-image')
                if (mainImg) mainImg.src = img
                // Update active thumb
                document.querySelectorAll('.product-thumb').forEach(thumb => {
                  thumb.classList.remove('active')
                })
                e.currentTarget.classList.add('active')
              }, 0)
            }
          }, [
            h('img', { src: img, alt: `${productName} - Image ${idx + 1}` })
          ])
        )
      )
    ])
  }

  // Composant Product Info
  const ProductInfo = ({ product }) => {
    let quantity = 1
    const setQuantity = (qty) => {
      quantity = Math.max(1, qty)
      const input = document.querySelector('.quantity-input')
      if (input) input.value = quantity
    }

    const handleAddToCart = (e) => {
      e.preventDefault()
      const form = e.target.closest('form')
      if (form) {
        const quantityInput = form.querySelector('input[name="quantity"]')
        if (quantityInput) {
          quantityInput.value = quantity
        }
        form.submit()
      }
    }

    const margin = (product.selling_price || 0) - (product.supplier_price || 0)
    const marginPercent = product.supplier_price > 0 
      ? ((margin / product.supplier_price) * 100).toFixed(0)
      : 0

    return withAOS(
      h('div', { className: 'product-detail-info' }, [
        h('div', { className: 'product-breadcrumb', 'data-aos': 'fade-up' }, [
          h('a', { href: '/', className: 'breadcrumb-link' }, [
            h(Icon, { name: 'home', className: 'icon-inline' }),
            ' Accueil'
          ]),
          h('span', { className: 'breadcrumb-separator' }, '/'),
          h('a', { href: '/products', className: 'breadcrumb-link' }, 'Produits'),
          h('span', { className: 'breadcrumb-separator' }, '/'),
          h('span', { className: 'breadcrumb-current' }, product.name)
        ]),
        h('h1', { className: 'product-detail-title', 'data-aos': 'fade-up', 'data-aos-delay': 50 }, product.name),
        h('div', { className: 'product-detail-price-section', 'data-aos': 'fade-up', 'data-aos-delay': 100 }, [
          h('div', { className: 'product-price-main' }, `$${Number(product.selling_price || 0).toFixed(2)}`),
          h('div', { className: 'product-price-old' }, `$${Number(product.supplier_price || 0).toFixed(2)}`),
          h('div', { className: 'product-margin-badge' }, [
            h(Icon, { name: 'chart-line', className: 'icon-inline' }),
            ` Marge: ${marginPercent}%`
          ])
        ]),
        product.description && h('div', { className: 'product-detail-description', 'data-aos': 'fade-up', 'data-aos-delay': 150 }, [
          h('h3', { className: 'product-section-title' }, 'Description'),
          h('p', { className: 'product-description-text' }, product.description)
        ]),
        h('div', { className: 'product-detail-features', 'data-aos': 'fade-up', 'data-aos-delay': 200 }, [
          h('div', { className: 'product-feature-item' }, [
            h(Icon, { name: 'shipping-fast', className: 'feature-icon' }),
            h('div', [
              h('div', { className: 'feature-title' }, 'Livraison rapide'),
              h('div', { className: 'feature-text' }, 'Expédition sous 24-48h')
            ])
          ]),
          h('div', { className: 'product-feature-item' }, [
            h(Icon, { name: 'shield-alt', className: 'feature-icon' }),
            h('div', [
              h('div', { className: 'feature-title' }, 'Paiement sécurisé'),
              h('div', { className: 'feature-text' }, 'Visa/MasterCard acceptés')
            ])
          ]),
          h('div', { className: 'product-feature-item' }, [
            h(Icon, { name: 'undo', className: 'feature-icon' }),
            h('div', [
              h('div', { className: 'feature-title' }, 'Retour facile'),
              h('div', { className: 'feature-text' }, '30 jours pour retourner')
            ])
          ])
        ]),
        h('form', { 
          action: `/cart/add/${product.id}`, 
          method: 'POST',
          className: 'product-detail-form',
          onSubmit: handleAddToCart,
          'data-aos': 'fade-up',
          'data-aos-delay': 250
        }, [
          h('input', { type: 'hidden', name: '_token', value: document.querySelector('meta[name=csrf-token]')?.content }),
          h('div', { className: 'product-quantity-selector' }, [
            h('label', { className: 'form-label' }, 'Quantité'),
            h('div', { className: 'quantity-controls' }, [
              h('button', {
                type: 'button',
                className: 'quantity-btn',
                onClick: (e) => {
                  quantity = Math.max(1, quantity - 1)
                  e.target.closest('.quantity-controls').querySelector('.quantity-input').value = quantity
                }
              }, h(Icon, { name: 'minus' })),
              h('input', {
                type: 'number',
                name: 'quantity',
                defaultValue: 1,
                min: 1,
                className: 'quantity-input',
                onChange: (e) => {
                  quantity = Math.max(1, parseInt(e.target.value) || 1)
                  e.target.value = quantity
                }
              }),
              h('button', {
                type: 'button',
                className: 'quantity-btn',
                onClick: (e) => {
                  quantity = quantity + 1
                  e.target.closest('.quantity-controls').querySelector('.quantity-input').value = quantity
                }
              }, h(Icon, { name: 'plus' }))
            ])
          ]),
          h('button', { type: 'submit', className: 'btn-primary accent btn-large btn-add-cart' }, [
            h(Icon, { name: 'cart-plus', className: 'icon-inline' }),
            ' Ajouter au panier'
          ])
        ])
      ]),
      'fade-up',
      0
    )
  }

  // Composant Principal
  const ProductDetailPage = () => {
    if (!product || !product.id) {
      return h('div', { className: 'product-not-found', 'data-aos': 'fade-up' }, [
        h(Icon, { name: 'exclamation-triangle', className: 'empty-icon' }),
        h('h2', { className: 'empty-title' }, 'Produit non trouvé'),
        h('p', { className: 'empty-text' }, 'Le produit que vous recherchez n\'existe pas.'),
        h('a', { href: '/products', className: 'btn-primary' }, [
          h(Icon, { name: 'arrow-left', className: 'icon-inline' }),
          ' Retour aux produits'
        ])
      ])
    }

    return h('div', { className: 'product-detail-container' }, [
      h('div', { className: 'product-detail-grid' }, [
        h(ImageGallery, { images: product.images, productName: product.name }),
        h(ProductInfo, { product: product })
      ])
    ])
  }

  const root = createRoot(rootEl)
  root.render(h(ProductDetailPage))
  
  // Réinitialiser AOS après le rendu React
  setTimeout(() => {
    if (window.AOS) {
      AOS.refresh()
    }
  }, 100)
})()

