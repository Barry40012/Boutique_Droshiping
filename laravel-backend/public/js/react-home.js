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
      'data-aos-duration': 800
    })
  }

  const PricingCard = ({ title, price, desc, features, featured, icon }) =>
    withAOS(
      h('div', { className: `pricing-card ${featured ? 'featured' : ''}` }, [
        icon && h('div', { className: 'pricing-icon' }, h(Icon, { name: icon })),
        h('div', { className: 'pricing-title' }, title),
        h('div', { className: 'pricing-price' }, price),
        h('div', { className: 'pricing-desc' }, desc),
        h('ul', { className: 'pricing-list' },
          features.map((f, idx) => h('li', { key: idx }, h(Icon, { name: 'check', className: 'icon-check' }), ` ${f}`))
        ),
        h('a', { href: '/register', className: featured ? 'btn-primary accent' : 'btn-primary secondary' }, 'Commencer')
      ]),
      'fade-up'
    )

  const Hero = () =>
    h('div', { className: 'hero', 'data-aos': 'fade-in' }, 
      h('div', { className: 'hero-content' }, [
        h('p', { className: 'hero-kicker' }, [
          h(Icon, { name: 'rocket', className: 'icon-inline' }),
          ' Plateforme Dropshipping Automatisée'
        ]),
        h('h1', { className: 'hero-title' }, 'Automatise ta boutique et encaisse en toute confiance'),
        h('p', { className: 'hero-subtitle' }, 'Paiement carte (Visa/Korapay), commandes fournisseur automatiques, dashboard admin complet, multi-boutiques. Tout ce dont tu as besoin pour réussir en dropshipping.'),
        h('div', { className: 'hero-actions', style: { display: 'flex', gap: '12px', flexWrap: 'wrap', marginTop: '24px' } }, [
          h('a', { href: '/register', className: 'btn-primary accent btn-large' }, [
            h(Icon, { name: 'play-circle', className: 'icon-inline' }),
            ' Essayer gratuitement'
          ]),
          h('a', { href: '#pricing', className: 'btn-ghost btn-large' }, [
            h(Icon, { name: 'arrow-down', className: 'icon-inline' }),
            ' Voir les plans'
          ]),
        ]),
        h('div', { className: 'hero-features', style: { display: 'flex', gap: '24px', marginTop: '32px', flexWrap: 'wrap' } }, [
          h('div', { className: 'hero-feature-item' }, [
            h(Icon, { name: 'check-circle', className: 'icon-inline' }),
            ' Paiement sécurisé'
          ]),
          h('div', { className: 'hero-feature-item' }, [
            h(Icon, { name: 'check-circle', className: 'icon-inline' }),
            ' Automatisation complète'
          ]),
          h('div', { className: 'hero-feature-item' }, [
            h(Icon, { name: 'check-circle', className: 'icon-inline' }),
            ' Support 24/7'
          ]),
        ])
      ])
    )

  const FeatureCard = ({ title, text, icon, delay = 0, color = 'primary' }) =>
    withAOS(
      h('div', { className: `card feature-card feature-card-${color}` }, [
        h('div', { className: `feature-icon feature-icon-${color}` }, h(Icon, { name: icon || 'star' })),
        h('h3', { className: 'card-title' }, title),
        h('p', { className: 'card-text' }, text),
      ]),
      'fade-up',
      delay
    )

  const HowStep = ({ badge, title, text, icon, delay = 0, number, isLast = false }) =>
    h('div', { className: 'step-item', 'data-aos': 'fade-up', 'data-aos-delay': delay }, [
      h('div', { className: 'step-connector', style: { display: isLast ? 'none' : 'block' } }),
      h('div', { className: 'step-content' }, [
        h('div', { className: 'step-circle' }, [
          h('div', { className: 'step-number' }, number),
          h('div', { className: 'step-icon-wrapper' }, h(Icon, { name: icon || 'check-circle' }))
        ]),
        h('div', { className: 'step-info' }, [
          h('div', { className: 'step-badge' }, badge),
          h('h3', { className: 'step-title' }, title),
          h('p', { className: 'step-text' }, text),
        ])
      ])
    ])

  const CTA = () =>
    h('div', { className: 'cta-section', 'data-aos': 'fade-up' },
      h('div', { className: 'cta-content' }, [
        h('div', { className: 'cta-icon' }, h(Icon, { name: 'store' })),
        h('p', { className: 'cta-kicker' }, [
          h(Icon, { name: 'gift', className: 'icon-inline' }),
          ' SaaS / Abonnement'
        ]),
        h('h2', { className: 'cta-title' },
          'Ouvre ta boutique en quelques clics'),
        h('p', { className: 'cta-subtitle' },
          'Abonnement mensuel, produits illimités, commandes automatisées, paiement Visa, dashboard admin complet. Tout ce dont tu as besoin pour lancer ton business.'),
        h('div', { className: 'cta-actions', style: { display: 'flex', gap: '12px', flexWrap: 'wrap', justifyContent: 'center' } }, [
          h('a', { href: '/register', className: 'btn-primary accent btn-large' }, [
            h(Icon, { name: 'user-plus', className: 'icon-inline' }),
            ' Créer mon compte'
          ]),
          h('a', { href: '/products', className: 'btn-ghost btn-large' }, [
            h(Icon, { name: 'shopping-bag', className: 'icon-inline' }),
            ' Voir les produits'
          ]),
        ]),
      ])
    )

  const ProductCard = ({ product, delay = 0 }) =>
    withAOS(
      h('div', { className: 'card product-card' }, [
        h('div', { className: 'product-image-placeholder' }, h(Icon, { name: 'image', className: 'icon-large' })),
        h('h3', { className: 'card-title' }, product.name),
        h('p', { className: 'card-text' }, product.description),
        h('div', { className: 'card-price' }, [
          h('div', { className: 'price-main' }, `$${Number(product.selling_price || 0).toFixed(2)}`),
          h('div', { className: 'price-old' }, `$${Number(product.supplier_price || 0).toFixed(2)}`),
        ]),
        h('div', { className: 'card-actions' }, [
          h('a', { href: `/products/${product.id}`, className: 'btn-ghost' }, [
            h(Icon, { name: 'eye', className: 'icon-inline' }),
            ' Détails'
          ]),
          h('form', { action: `/cart/add/${product.id}`, method: 'POST' }, [
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

  const Home = () =>
    h(React.Fragment, null, [
      h(Hero),
      h('div', { className: 'section section-features' }, [
        h('div', { className: 'section-header', 'data-aos': 'fade-up' }, [
          h('div', { className: 'pill pill-primary' }, [
            h(Icon, { name: 'sparkles', className: 'icon-inline' }),
            ' Pourquoi choisir notre plateforme'
          ]),
          h('h2', { className: 'section-title', style: { marginTop: '12px' } }, 'Un outil complet pour le dropshipping'),
          h('p', { className: 'section-description' }, 'Tout ce dont tu as besoin pour automatiser et scaler ton business de dropshipping, sans complexité.')
        ]),
        h('div', { className: 'grid grid-features' }, [
          h(FeatureCard, { 
            title: 'Automatisation complète', 
            text: 'Paiement, commandes fournisseur, notifications — tout fonctionne en automatique. Plus besoin de gérer manuellement chaque commande.',
            icon: 'cog',
            delay: 0,
            color: 'primary'
          }),
          h(FeatureCard, { 
            title: 'Adapté pour l\'Afrique', 
            text: 'Passerelles Visa compatibles (Korapay/DPO), multi-devises, mobile-friendly. Conçu spécialement pour le marché africain.',
            icon: 'globe-africa',
            delay: 100,
            color: 'accent'
          }),
          h(FeatureCard, { 
            title: 'SaaS multi-boutiques', 
            text: 'Chaque marchand a son espace dédié, ses produits, ses commandes, son lien de boutique unique. Gestion simplifiée.',
            icon: 'store',
            delay: 200,
            color: 'secondary'
          }),
        ])
      ]),
      h('div', { className: 'section section-steps' }, [
        h('div', { className: 'section-header', 'data-aos': 'fade-up' }, [
          h('div', { className: 'pill pill-secondary' }, [
            h(Icon, { name: 'route', className: 'icon-inline' }),
            ' Workflow simple'
          ]),
          h('h2', { className: 'section-title', style: { marginTop: '12px' } }, 'Comment ça marche ?'),
          h('p', { className: 'section-description' }, 'Un processus en 3 étapes simples pour automatiser complètement ton business.')
        ]),
        h('div', { className: 'steps-timeline' }, [
          h(HowStep, { 
            badge: 'Étape 1', 
            title: 'Client paie sur ta boutique', 
            text: 'Paiement Visa sécurisé via Korapay/DPO, panier simple et intuitif. Le client effectue son achat en toute sécurité.',
            icon: 'credit-card',
            delay: 0,
            number: '1'
          }),
          h(HowStep, { 
            badge: 'Étape 2', 
            title: 'Commande transmise automatiquement', 
            text: 'Envoi automatique au fournisseur (AliExpress/CJ ou local). Plus besoin d\'intervenir manuellement.',
            icon: 'paper-plane',
            delay: 100,
            number: '2'
          }),
          h(HowStep, { 
            badge: 'Étape 3', 
            title: 'Fournisseur expédie', 
            text: 'Tu gardes ta marge, le fournisseur reçoit son prix. Le client reçoit sa commande directement du fournisseur.',
            icon: 'truck',
            delay: 200,
            number: '3',
            isLast: true
          }),
        ])
      ]),
      products.length > 0 && h('div', { className: 'section section-products' }, [
        h('div', { className: 'section-header', 'data-aos': 'fade-up' }, [
          h('h2', { className: 'section-title' }, [
            h(Icon, { name: 'fire', className: 'icon-inline' }),
            ' Produits populaires'
          ]),
          h('p', { className: 'section-description' }, 'Découvre nos produits les plus vendus')
        ]),
        h('div', { className: 'grid grid-products' },
          products.map((p, idx) => h(ProductCard, { key: p.id, product: p, delay: idx * 100 }))
        ),
      ]),
      h('div', { className: 'section section-pricing', id: 'pricing' }, [
        h('div', { className: 'section-header', 'data-aos': 'fade-up' }, [
          h('div', { className: 'pill pill-accent' }, [
            h(Icon, { name: 'tags', className: 'icon-inline' }),
            ' Tarifs'
          ]),
          h('h2', { className: 'section-title', style: { marginTop: '12px' } }, 'Plans simples et transparents'),
          h('p', { className: 'section-description' }, 'Choisis le plan qui correspond à tes besoins. Pas de frais cachés, annulation à tout moment.')
        ]),
        h('div', { className: 'pricing-grid' }, [
          h(PricingCard, {
            title: 'Free',
            price: '$0',
            desc: 'Pour tester rapidement.',
            features: ['1 boutique', '5 produits max', 'Support email'],
            featured: false,
            icon: 'gift'
          }),
          h(PricingCard, {
            title: 'Starter',
            price: '$29/mo',
            desc: 'Idéal pour lancer ta boutique.',
            features: ['Boutiques illimitées', 'Produits illimités', 'Paiement Visa/Korapay', 'Automatisation fournisseur'],
            featured: true,
            icon: 'star'
          }),
          h(PricingCard, {
            title: 'Pro',
            price: '$79/mo',
            desc: 'Pour scaler avec support prioritaire.',
            features: ['Tout Starter', 'Webhook/Automations avancées', 'Support prioritaire', 'Rapports avancés'],
            featured: false,
            icon: 'crown'
          }),
        ])
      ]),
      h(CTA),
    ])

  const root = createRoot(rootEl)
  root.render(h(Home))
  
  // Réinitialiser AOS après le rendu React pour capturer les nouveaux éléments
  setTimeout(() => {
    if (window.AOS) {
      AOS.refresh()
    }
  }, 100)
})()
