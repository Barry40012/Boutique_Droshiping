(() => {
  const rootEl = document.getElementById('react-login')
  if (!rootEl || !window.React || !window.ReactDOM) return

  const errors = (() => {
    try {
      const errorsData = rootEl.dataset.errors
      if (!errorsData) return {}
      const parsed = JSON.parse(errorsData)
      // Laravel retourne un objet avec des tableaux de messages
      // On prend le premier message de chaque champ
      const errorObj = {}
      Object.keys(parsed).forEach(key => {
        if (Array.isArray(parsed[key]) && parsed[key].length > 0) {
          errorObj[key] = parsed[key][0]
        }
      })
      return errorObj
    } catch (e) {
      return {}
    }
  })()

  const oldValues = (() => {
    try {
      const oldData = rootEl.dataset.old
      return oldData ? JSON.parse(oldData) : {}
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

  // Composant Input
  const InputField = ({ name, label, type = 'text', icon, error, oldValue, delay = 0, required = true }) => {
    const hasError = errors[name]
    const inputId = `input-${name}`
    
    return withAOS(
      h('div', { className: 'form-group' }, [
        h('label', { htmlFor: inputId, className: 'form-label' }, [
          icon && h(Icon, { name: icon, className: 'icon-inline' }),
          label,
          required && h('span', { className: 'required' }, ' *')
        ]),
        h('div', { className: 'input-wrapper' }, [
          h('input', {
            id: inputId,
            name: name,
            type: type,
            defaultValue: oldValue || oldValues[name] || '',
            required: required,
            className: `form-input ${hasError ? 'error' : ''}`,
            placeholder: `Entrez votre ${label.toLowerCase()}`
          }),
          hasError && h('div', { className: 'input-error' }, [
            h(Icon, { name: 'exclamation-circle', className: 'icon-inline' }),
            errors[name]
          ])
        ])
      ]),
      'fade-up',
      delay
    )
  }

  // Composant Hero Section
  const LoginHero = () =>
    h('div', { className: 'login-hero', 'data-aos': 'fade-in' }, [
      h('div', { className: 'login-hero-content' }, [
        h('div', { className: 'login-icon-wrapper' }, h(Icon, { name: 'sign-in-alt' })),
        h('h1', { className: 'login-title' }, 'Connecte-toi à ta boutique'),
        h('p', { className: 'login-subtitle' }, 'Accède à ton dashboard et gère tes produits, commandes et paiements en toute simplicité'),
        h('div', { className: 'login-features', style: { display: 'flex', gap: '20px', flexWrap: 'wrap', justifyContent: 'center', marginTop: '24px' } }, [
          h('div', { className: 'login-feature-item' }, [
            h(Icon, { name: 'shield-alt', className: 'icon-inline' }),
            ' Connexion sécurisée'
          ]),
          h('div', { className: 'login-feature-item' }, [
            h(Icon, { name: 'bolt', className: 'icon-inline' }),
            ' Accès rapide'
          ]),
          h('div', { className: 'login-feature-item' }, [
            h(Icon, { name: 'lock', className: 'icon-inline' }),
            ' Données protégées'
          ]),
        ])
      ])
    ])

  // Composant Formulaire
  const LoginForm = () => {
    const csrfToken = document.querySelector('meta[name=csrf-token]')?.content || ''
    
    return withAOS(
      h('div', { className: 'login-form-container' }, [
        h('form', { 
          method: 'POST', 
          action: '/login',
          className: 'login-form',
        }, [
          h('input', { type: 'hidden', name: '_token', value: csrfToken }),
          h(InputField, {
            name: 'email',
            label: 'Adresse email',
            type: 'email',
            icon: 'envelope',
            error: errors.email,
            oldValue: oldValues.email,
            delay: 0
          }),
          h(InputField, {
            name: 'password',
            label: 'Mot de passe',
            type: 'password',
            icon: 'lock',
            error: errors.password,
            delay: 50
          }),
          h('div', { className: 'form-group form-group-checkbox', 'data-aos': 'fade-up', 'data-aos-delay': 100 }, [
            h('label', { className: 'checkbox-label' }, [
              h('input', { 
                type: 'checkbox', 
                name: 'remember',
                id: 'remember',
                className: 'checkbox-input'
              }),
              h('span', { className: 'checkbox-text' }, [
                h(Icon, { name: 'check', className: 'checkbox-icon' }),
                ' Se souvenir de moi'
              ])
            ])
          ]),
          h('div', { className: 'form-group', 'data-aos': 'fade-up', 'data-aos-delay': 150 }, [
            h('button', { type: 'submit', className: 'btn-primary accent btn-large btn-submit' }, [
              h(Icon, { name: 'sign-in-alt', className: 'icon-inline' }),
              ' Se connecter'
            ])
          ])
        ]),
        h('div', { className: 'login-footer', 'data-aos': 'fade-up', 'data-aos-delay': 200 }, [
          h('p', { className: 'login-footer-text' }, [
            'Pas de compte ? ',
            h('a', { href: '/register', className: 'login-footer-link' }, 'Créer un compte')
          ])
        ])
      ]),
      'fade-up',
      0
    )
  }

  // Composant Principal
  const LoginPage = () =>
    h(React.Fragment, null, [
      h(LoginHero),
      h(LoginForm)
    ])

  const root = createRoot(rootEl)
  root.render(h(LoginPage))
  
  // Réinitialiser AOS après le rendu React
  setTimeout(() => {
    if (window.AOS) {
      AOS.refresh()
    }
  }, 100)
})()

