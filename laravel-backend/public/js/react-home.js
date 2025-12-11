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

  const FeatureCard = ({ title, text }) =>
    h('div', { className: 'card' }, [
      h('h3', { className: 'card-title' }, title),
      h('p', { className: 'card-text' }, text),
    ])

  const HowStep = ({ badge, title, text }) =>
    h('div', { className: 'card' }, [
      h('div', { className: 'btn-ghost', style: { width: 'fit-content', marginBottom: '8px' } }, badge),
      h('h3', { className: 'card-title' }, title),
      h('p', { className: 'card-text' }, text),
    ])

  const CTA = () =>
    h('div', { className: 'hero', style: { marginTop: '2rem' } },
      h('div', { className: 'hero-content' }, [
        h('p', { className: 'hero-kicker' }, 'SaaS / Abonnement'),
        h('h2', { className: 'hero-title', style: { fontSize: '30px', marginBottom: '10px' } },
          'Ouvre ta boutique en quelques clics'),
        h('p', { className: 'hero-subtitle' },
          'Abonnement mensuel, produits, commandes, paiement Visa, dashboard admin.'),
        h('div', { style: { display: 'flex', gap: '12px', flexWrap: 'wrap' } }, [
          h('a', { href: '/register', className: 'btn-primary accent' }, 'Créer mon compte'),
          h('a', { href: '/products', className: 'btn-ghost' }, 'Voir les produits'),
        ]),
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
      h('h2', { className: 'section-title' }, 'Pourquoi notre plateforme ?'),
      h('div', { className: 'grid' }, [
        h(FeatureCard, { title: 'Automatisation', text: 'Paiement, commandes fournisseur, notifications — tout roule en automatique.' }),
        h(FeatureCard, { title: 'Adapté Afrique', text: 'Passerelles Visa compatibles (Korapay/DPO), multi-devises, mobile-friendly.' }),
        h(FeatureCard, { title: 'SaaS multi-boutiques', text: 'Chaque marchand a son espace, ses produits, ses commandes, son lien de boutique.' }),
      ]),
      h('h2', { className: 'section-title', style: { marginTop: '2rem' } }, 'Comment ça marche ?'),
      h('div', { className: 'grid' }, [
        h(HowStep, { badge: 'Étape 1', title: 'Client paie sur ta boutique', text: 'Paiement Visa sécurisé, panier simple.' }),
        h(HowStep, { badge: 'Étape 2', title: 'Commande transmise', text: 'Envoi auto au fournisseur (AliExpress/CJ ou local).' }),
        h(HowStep, { badge: 'Étape 3', title: 'Fournisseur expédie', text: 'Tu gardes ta marge, le fournisseur reçoit son prix.' }),
      ]),
      h('h2', { className: 'section-title' }, 'Produits populaires'),
      products.length === 0
        ? h('p', { className: 'text-gray-600 col-span-full text-center' }, 'Aucun produit pour le moment.')
        : h('div', { className: 'grid' },
            products.map((p) => h(ProductCard, { key: p.id, product: p }))
          ),
      h(CTA),
    ])

  const root = createRoot(rootEl)
  root.render(h(Home))
})()

