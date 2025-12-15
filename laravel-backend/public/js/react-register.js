(() => {
  const rootEl = document.getElementById('react-register')
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
  const RegisterHero = () =>
    h('div', { className: 'register-hero', 'data-aos': 'fade-in' }, [
      h('div', { className: 'register-hero-content' }, [
        h('div', { className: 'register-icon-wrapper' }, h(Icon, { name: 'user-plus' })),
        h('h1', { className: 'register-title' }, 'Créer ton compte'),
        h('p', { className: 'register-subtitle' }, 'Rejoins notre plateforme et lance ta boutique de dropshipping en quelques minutes'),
        h('div', { className: 'register-features', style: { display: 'flex', gap: '20px', flexWrap: 'wrap', justifyContent: 'center', marginTop: '24px' } }, [
          h('div', { className: 'register-feature-item' }, [
            h(Icon, { name: 'check-circle', className: 'icon-inline' }),
            ' Gratuit pendant 14 jours'
          ]),
          h('div', { className: 'register-feature-item' }, [
            h(Icon, { name: 'check-circle', className: 'icon-inline' }),
            ' Sans carte bancaire'
          ]),
          h('div', { className: 'register-feature-item' }, [
            h(Icon, { name: 'check-circle', className: 'icon-inline' }),
            ' Support inclus'
          ]),
        ])
      ])
    ])

  // Composant Formulaire
  const RegisterForm = () => {
    const csrfToken = document.querySelector('meta[name=csrf-token]')?.content || ''
    
    return withAOS(
      h('div', { className: 'register-form-container' }, [
        h('form', { 
          method: 'POST', 
          action: '/register',
          className: 'register-form',
          onSubmit: (e) => {
            // Validation côté client optionnelle
          }
        }, [
          h('input', { type: 'hidden', name: '_token', value: csrfToken }),
          h(InputField, {
            name: 'name',
            label: 'Nom complet',
            type: 'text',
            icon: 'user',
            error: errors.name,
            oldValue: oldValues.name,
            delay: 0
          }),
          h(InputField, {
            name: 'email',
            label: 'Adresse email',
            type: 'email',
            icon: 'envelope',
            error: errors.email,
            oldValue: oldValues.email,
            delay: 50
          }),
          h(InputField, {
            name: 'password',
            label: 'Mot de passe',
            type: 'password',
            icon: 'lock',
            error: errors.password,
            delay: 100
          }),
          h(InputField, {
            name: 'password_confirmation',
            label: 'Confirmer le mot de passe',
            type: 'password',
            icon: 'lock',
            error: errors.password_confirmation,
            delay: 150
          }),
          h('div', { className: 'form-group', 'data-aos': 'fade-up', 'data-aos-delay': 200 }, [
            h('button', { type: 'submit', className: 'btn-primary accent btn-large btn-submit' }, [
              h(Icon, { name: 'user-plus', className: 'icon-inline' }),
              ' Créer mon compte'
            ])
          ])
        ]),
        h('div', { className: 'register-footer', 'data-aos': 'fade-up', 'data-aos-delay': 250 }, [
          h('p', { className: 'register-footer-text' }, [
            'Déjà un compte ? ',
            h('a', { href: '/login', className: 'register-footer-link' }, 'Se connecter')
          ])
        ])
      ]),
      'fade-up',
      0
    )
  }

  // Composant Principal
  const RegisterPage = () =>
    h(React.Fragment, null, [
      h(RegisterHero),
      h(RegisterForm)
    ])

  const root = createRoot(rootEl)
  root.render(h(RegisterPage))
  
  // Réinitialiser AOS après le rendu React
  setTimeout(() => {
    if (window.AOS) {
      AOS.refresh()
    }
  }, 100)
})()

