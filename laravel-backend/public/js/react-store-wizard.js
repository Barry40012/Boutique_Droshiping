(() => {
  const rootEl = document.getElementById('react-store-wizard')
  if (!rootEl || !window.React || !window.ReactDOM) return

  const templates = (() => {
    try {
      return JSON.parse(rootEl.dataset.templates || '[]')
    } catch (e) {
      return []
    }
  })()

  // Récupérer les erreurs Laravel depuis le serveur
  const serverErrors = (() => {
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
      console.error('Erreur parsing errors:', e)
      return {}
    }
  })()
  
  // Afficher les erreurs serveur dans la console
  if (Object.keys(serverErrors).length > 0) {
    console.error('❌ ERREURS SERVEUR DÉTECTÉES:', serverErrors)
  }

  const { createElement: h, useState, useRef, useEffect, useMemo } = React
  
  // Refs pour stocker les valeurs des inputs sans déclencher de re-renders
  // Cela évite la perte de focus
  const nameInputRef = { current: null }
  const descInputRef = { current: null }
  const { createRoot } = ReactDOM

  // Helper pour créer des icônes Font Awesome
  const Icon = ({ name, className = '' }) => 
    h('i', { className: `fas fa-${name} ${className}`, 'aria-hidden': 'true' })

  // Modèles de bannières
  const bannerTemplates = [
    {
      id: 'banner-1',
      name: 'Bannière Moderne',
      preview: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
      description: 'Dégradé violet moderne'
    },
    {
      id: 'banner-2',
      name: 'Bannière Énergique',
      preview: 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
      description: 'Dégradé rose-rouge énergique'
    },
    {
      id: 'banner-3',
      name: 'Bannière Professionnelle',
      preview: 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
      description: 'Dégradé bleu professionnel'
    },
    {
      id: 'banner-4',
      name: 'Bannière Nature',
      preview: 'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)',
      description: 'Dégradé vert nature'
    }
  ]

  // Catégories d'activité
  const categories = [
    { id: 'fashion', name: 'Mode & Vêtements', icon: 'tshirt' },
    { id: 'electronics', name: 'Électronique', icon: 'laptop' },
    { id: 'beauty', name: 'Beauté & Cosmétiques', icon: 'spa' },
    { id: 'home', name: 'Maison & Décoration', icon: 'home' },
    { id: 'sports', name: 'Sport & Fitness', icon: 'dumbbell' },
    { id: 'toys', name: 'Jouets & Enfants', icon: 'child' },
    { id: 'health', name: 'Santé & Bien-être', icon: 'heartbeat' },
    { id: 'other', name: 'Autre', icon: 'store' }
  ]

  // Composant Principal du Wizard
  const StoreWizard = () => {
    const [currentStep, setCurrentStep] = useState(1)
    
    // Refs pour les inputs (pour éviter les re-renders qui causent la perte de focus)
    const nameInputRef = useRef(null)
    const descInputRef = useRef(null)
    
    // Initialiser avec un thème par défaut si disponible
    const defaultTemplateId = templates && templates.length > 0 ? String(templates[0].id) : ''
    
    // Récupérer les données du questionnaire depuis l'URL
    const urlParams = new URLSearchParams(window.location.search)
    
    // Log pour debug
    const experienceLevel = urlParams.get('experience_level') || ''
    const country = urlParams.get('country') || ''
    const currency = urlParams.get('currency') || 'USD'
    const category = urlParams.get('category') || ''
    
    console.log('🔍 ========== INITIALISATION DU WIZARD ==========')
    console.log('🔍 URL complète:', window.location.href)
    console.log('🔍 Paramètres URL:', {
      experience_level: experienceLevel,
      country: country,
      currency: currency,
      category: category
    })
    
    const [formData, setFormData] = useState({
      name: '',
      category: category,
      description: '',
      banner: '',
      experience_level: experienceLevel,
      country: country,
      currency: currency,
      template_id: defaultTemplateId
    })
    
    // Log après initialisation
    console.log('🔍 formData initialisé:', formData)
    // Initialiser avec les erreurs serveur si présentes
    const [errors, setErrors] = useState(serverErrors || {})

    const totalSteps = 4 // Thème désactivé, on passe directement à la création

    const updateFormData = (field, value) => {
      setFormData(prev => {
        // Ne mettre à jour que si la valeur a changé
        if (prev[field] === value) return prev
        return { ...prev, [field]: value }
      })
      if (errors[field]) {
        setErrors(prev => {
          const newErrors = { ...prev }
          delete newErrors[field]
          return newErrors
        })
      }
    }

    // useEffect pour synchroniser les valeurs du DOM avec formData après chaque changement d'étape
    // Ceci est CRUCIAL pour les inputs non contrôlés
    useEffect(() => {
      // Synchroniser les valeurs des inputs non contrôlés avec formData
      const nameInput = document.querySelector('#store-name-input')
      const descTextarea = document.querySelector('#store-description-textarea')
      
      if (nameInput && nameInput.value && nameInput.value !== formData.name) {
        // Si l'input a une valeur différente de formData, mettre à jour formData
        setFormData(prev => ({ ...prev, name: nameInput.value }))
        console.log('🔄 Synchronisation: name mis à jour depuis DOM:', nameInput.value)
      }
      
      if (descTextarea && descTextarea.value && descTextarea.value !== formData.description) {
        // Si le textarea a une valeur différente de formData, mettre à jour formData
        setFormData(prev => ({ ...prev, description: descTextarea.value }))
        console.log('🔄 Synchronisation: description mise à jour depuis DOM:', descTextarea.value.substring(0, 30) + '...')
      }
    }, [currentStep]) // Exécuter à chaque changement d'étape

    const validateStep = (step) => {
      const newErrors = {}
      
      // Récupérer les valeurs des inputs non contrôlés pour la validation
      let nameValue = formData.name
      let descValue = formData.description
      
      if (step === 1 || step === 5) {
        const nameInput = document.querySelector('#store-name-input')
        if (nameInput) {
          nameValue = nameInput.value || ''
        }
      }
      
      if (step === 3 || step === 5) {
        const descTextarea = document.querySelector('#store-description-textarea')
        if (descTextarea) {
          descValue = descTextarea.value || ''
        }
      }
      
      if (step === 1 && !nameValue.trim()) {
        newErrors.name = 'Le nom de la boutique est requis'
      }
      
      if (step === 2 && !formData.category) {
        newErrors.category = 'Veuillez sélectionner un domaine d\'activité'
      }
      
      if (step === 3 && !descValue.trim()) {
        newErrors.description = 'La description est requise'
      }
      
      if (step === 4 && !formData.banner) {
        newErrors.banner = 'Veuillez choisir une bannière'
      }
      
      // L'étape 5 n'est plus obligatoire si un thème par défaut existe
      // if (step === 5 && !formData.template_id) {
      //   newErrors.template_id = 'Veuillez choisir un thème'
      // }
      
      // Ne pas écraser toutes les erreurs, seulement ajouter celles de cette étape
      if (Object.keys(newErrors).length > 0) {
        setErrors(prev => ({ ...prev, ...newErrors }))
      }
      
      return Object.keys(newErrors).length === 0
    }

    const handleNext = () => {
      if (validateStep(currentStep)) {
        if (currentStep < totalSteps) {
          setCurrentStep(currentStep + 1)
          window.scrollTo({ top: 0, behavior: 'smooth' })
        }
      }
    }

    const handleBack = () => {
      if (currentStep > 1) {
        setCurrentStep(currentStep - 1)
        window.scrollTo({ top: 0, behavior: 'smooth' })
      }
    }

    const handleSubmit = (e) => {
      e.preventDefault()
      
      console.log('🚀 ========== DÉBUT SOUMISSION ==========')
      console.log('📋 formData actuel:', formData)
      
      // RÉCUPÉRER LES VALEURS DIRECTEMENT DEPUIS LE DOM (inputs non contrôlés)
      // C'est la source de vérité car onInput ne met pas à jour formData
      const nameInput = document.querySelector('#store-name-input')
      const descTextarea = document.querySelector('#store-description-textarea')
      
      // Récupérer les valeurs depuis le DOM (source de vérité)
      const nameValue = nameInput ? nameInput.value.trim() : ''
      const descValue = descTextarea ? descTextarea.value.trim() : ''
      
      console.log('📋 Valeurs récupérées depuis DOM:')
      console.log('  name:', nameValue, '(input trouvé:', !!nameInput, ')')
      console.log('  description:', descValue.substring(0, 30) + '...', '(textarea trouvé:', !!descTextarea, ')')
      console.log('  category:', formData.category)
      console.log('  banner:', formData.banner)
      console.log('  experience_level:', formData.experience_level)
      console.log('  country:', formData.country)
      console.log('  currency:', formData.currency)
      
      // Utiliser les valeurs du DOM (source de vérité)
      const finalName = nameValue
      const finalDesc = descValue
      
      // Valider toutes les étapes (1 à 4, l'étape 5 thème est désactivée)
      // Valider chaque étape individuellement avec des messages détaillés
      // Utiliser les variables nameInput et descTextarea déjà déclarées plus haut
      let isValid = true
      let firstErrorStep = null
      let validationErrors = {}
      
      // Utiliser les valeurs récupérées depuis le DOM, ou formData comme fallback
      const actualName = nameValue || formData.name || ''
      const actualDesc = descValue || formData.description || ''
      
      console.log('🔍 Valeurs finales pour validation:')
      console.log('  actualName:', actualName, '(depuis DOM:', nameValue, ', depuis formData:', formData.name, ')')
      console.log('  actualDesc:', actualDesc.substring(0, 30) + '...', '(depuis DOM:', descValue ? 'oui' : 'non', ', depuis formData:', formData.description ? 'oui' : 'non', ')')
      
      // Étape 1: Nom
      if (!actualName) {
        isValid = false
        firstErrorStep = 1
        validationErrors.name = 'Le nom de la boutique est requis'
      }
      
      // Étape 2: Catégorie (domaine d'activité)
      if (!formData.category) {
        isValid = false
        if (!firstErrorStep) firstErrorStep = 2
        validationErrors.category = 'Veuillez sélectionner un domaine d\'activité'
      }
      
      // Étape 3: Description
      if (!actualDesc) {
        isValid = false
        if (!firstErrorStep) firstErrorStep = 3
        validationErrors.description = 'La description est requise'
      }
      
      // Étape 4: Bannière
      if (!formData.banner) {
        isValid = false
        if (!firstErrorStep) firstErrorStep = 4
        validationErrors.banner = 'Veuillez choisir une bannière'
      }
      
      // Vérifier aussi les données du questionnaire (experience_level, country, currency)
      console.log('🔍 ========== VÉRIFICATION QUESTIONNAIRE ==========')
      console.log('🔍 formData.experience_level:', formData.experience_level, '(vide:', !formData.experience_level, ')')
      console.log('🔍 formData.country:', formData.country, '(vide:', !formData.country, ')')
      console.log('🔍 formData.currency:', formData.currency, '(vide:', !formData.currency, ')')
      
      // Essayer aussi de récupérer depuis l'URL si vide dans formData
      const urlParamsCheck = new URLSearchParams(window.location.search)
      const experienceLevelFromUrl = urlParamsCheck.get('experience_level') || ''
      const countryFromUrl = urlParamsCheck.get('country') || ''
      const currencyFromUrl = urlParamsCheck.get('currency') || 'USD'
      
      console.log('🔍 Valeurs depuis URL:', {
        experience_level: experienceLevelFromUrl,
        country: countryFromUrl,
        currency: currencyFromUrl
      })
      
      // Utiliser les valeurs de l'URL si formData est vide
      const finalExperienceLevel = formData.experience_level || experienceLevelFromUrl
      const finalCountry = formData.country || countryFromUrl
      const finalCurrency = formData.currency || currencyFromUrl
      
      console.log('🔍 Valeurs finales utilisées:', {
        experience_level: finalExperienceLevel,
        country: finalCountry,
        currency: finalCurrency
      })
      
      // Mettre à jour formData si nécessaire (mais attention, setFormData est asynchrone)
      if (!formData.experience_level && experienceLevelFromUrl) {
        formData.experience_level = experienceLevelFromUrl
        console.log('✅ experience_level mis à jour depuis URL')
      }
      if (!formData.country && countryFromUrl) {
        formData.country = countryFromUrl
        console.log('✅ country mis à jour depuis URL')
      }
      if (!formData.currency && currencyFromUrl) {
        formData.currency = currencyFromUrl
        console.log('✅ currency mis à jour depuis URL')
      }
      
      if (!finalExperienceLevel) {
        isValid = false
        validationErrors.experience_level = 'Niveau d\'expérience requis. Veuillez remplir le questionnaire d\'abord.'
        console.error('❌ experience_level manquant')
      }
      if (!finalCountry) {
        isValid = false
        validationErrors.country = 'Pays requis. Veuillez remplir le questionnaire d\'abord.'
        console.error('❌ country manquant')
      }
      if (!finalCurrency) {
        isValid = false
        validationErrors.currency = 'Devise requise. Veuillez remplir le questionnaire d\'abord.'
        console.error('❌ currency manquant')
      }
      
      if (!isValid) {
        // Mettre à jour les erreurs avec les détails
        setErrors(prev => ({ ...prev, ...validationErrors }))
        
        // Aller à l'étape avec erreur
        if (firstErrorStep) {
          setCurrentStep(firstErrorStep)
          window.scrollTo({ top: 0, behavior: 'smooth' })
        }
        
        // Créer un message d'erreur détaillé avec des noms lisibles
        const fieldNames = {
          name: 'Nom de la boutique',
          category: 'Domaine d\'activité',
          description: 'Description',
          banner: 'Bannière',
          experience_level: 'Niveau d\'expérience',
          country: 'Pays',
          currency: 'Devise'
        }
        
        const missingFields = Object.keys(validationErrors).map(key => fieldNames[key] || key)
        const errorMessage = missingFields.length > 0 
          ? `❌ Champs manquants ou invalides: ${missingFields.join(', ')}. Veuillez corriger ces erreurs avant de continuer.`
          : 'Veuillez corriger les erreurs avant de continuer'
        
        setErrors(prev => ({ ...prev, submit: errorMessage }))
        console.error('❌ Erreurs de validation détaillées:', validationErrors)
        console.error('❌ Champs manquants:', missingFields)
        return
      }
      
      if (isValid) {
        // Afficher un indicateur de chargement
        const submitButton = document.getElementById('submit-store-btn')
        if (submitButton) {
          submitButton.disabled = true
          submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Création en cours...'
        }
        
        // Vérifier que toutes les données requises sont présentes
        if (!formData.name || !formData.description || !formData.category || !formData.banner || !formData.experience_level || !formData.country || !formData.currency) {
          setErrors(prev => ({ ...prev, submit: 'Veuillez remplir tous les champs obligatoires' }))
          if (submitButton) {
            submitButton.disabled = false
            submitButton.innerHTML = '<i class="fas fa-check icon-inline"></i> Créer ma boutique'
          }
          return
        }
        
        const form = document.createElement('form')
        form.method = 'POST'
        form.action = rootEl.dataset.submitUrl || '/merchant/store'
        
        const csrf = document.createElement('input')
        csrf.type = 'hidden'
        csrf.name = '_token'
        const csrfToken = document.querySelector('meta[name=csrf-token]')?.content
        if (!csrfToken) {
          console.error('CSRF token not found')
          setErrors(prev => ({ ...prev, submit: 'Erreur de sécurité. Veuillez rafraîchir la page.' }))
          if (submitButton) {
            submitButton.disabled = false
            submitButton.innerHTML = '<i class="fas fa-check icon-inline"></i> Créer ma boutique'
          }
          return
        }
        csrf.value = csrfToken
        form.appendChild(csrf)
        
        // Utiliser les valeurs récupérées depuis le DOM, ou formData comme fallback
        const finalName = nameValue || formData.name || ''
        const finalDesc = descValue || formData.description || ''
        
        console.log('🔍 Valeurs finales pour soumission:')
        console.log('  finalName:', finalName, '(depuis DOM:', nameValue, ', depuis formData:', formData.name, ')')
        console.log('  finalDesc:', finalDesc.substring(0, 30) + '...', '(depuis DOM:', descValue ? 'oui' : 'non', ', depuis formData:', formData.description ? 'oui' : 'non', ')')
        
        // Ajouter toutes les données du formulaire avec les valeurs du DOM
        const formFields = {
          name: finalName,
          category: formData.category,
          description: finalDesc,
          banner: formData.banner,
          experience_level: formData.experience_level,
          country: formData.country,
          currency: formData.currency,
          template_id: formData.template_id || (templates && templates.length > 0 ? String(templates[0].id) : '')
        }
        
        console.log('📋 ========== VÉRIFICATION FINALE DES DONNÉES ==========')
        console.log('📋 Données à envoyer:', formFields)
        console.log('📋 Détail de chaque champ:')
        Object.keys(formFields).forEach(key => {
          const value = formFields[key]
          const isEmpty = !value || value === '' || value === null || value === undefined
          console.log(`  ${key}: ${isEmpty ? '❌ VIDE' : '✅'} = "${value}"`)
        })
        
        // Vérifier que tous les champs requis sont présents avec des détails
        const requiredFields = ['name', 'category', 'description', 'banner', 'experience_level', 'country', 'currency']
        const missingFields = []
        const fieldDetails = {}
        
        requiredFields.forEach(field => {
          const value = formFields[field]
          const isEmpty = !value || value === '' || value === null || value === undefined
          fieldDetails[field] = {
            value: value,
            isEmpty: isEmpty,
            type: typeof value
          }
          if (isEmpty) {
            missingFields.push(field)
            console.error(`❌ Champ manquant: ${field} (valeur: ${value}, type: ${typeof value})`)
          } else {
            console.log(`✅ Champ OK: ${field} = "${value}"`)
          }
        })
        
        if (missingFields.length > 0) {
          console.error('❌ ========== CHAMPS MANQUANTS DÉTECTÉS ==========')
          console.error('❌ Nombre de champs manquants:', missingFields.length)
          console.error('❌ Champs manquants:', missingFields)
          console.error('❌ Détails complets:', fieldDetails)
          console.error('❌ formData complet:', formData)
          console.error('❌ nameValue:', nameValue)
          console.error('❌ descValue:', descValue)
          
          const fieldNames = {
            name: 'Nom de la boutique',
            category: 'Domaine d\'activité',
            description: 'Description',
            banner: 'Bannière',
            experience_level: 'Niveau d\'expérience',
            country: 'Pays',
            currency: 'Devise'
          }
          
          const missingFieldNames = missingFields.map(f => fieldNames[f] || f)
          setErrors(prev => ({ 
            ...prev, 
            submit: `❌ Champs manquants: ${missingFieldNames.join(', ')}. Veuillez remplir tous les champs obligatoires.` 
          }))
          if (submitButton) {
            submitButton.disabled = false
            submitButton.innerHTML = '<i class="fas fa-check icon-inline"></i> Créer ma boutique'
          }
          return
        }
        
        console.log('✅ Tous les champs sont remplis, soumission du formulaire...')
        
        // Ajouter tous les champs au formulaire
        Object.keys(formFields).forEach(key => {
          if (formFields[key] !== null && formFields[key] !== undefined && formFields[key] !== '') {
            const input = document.createElement('input')
            input.type = 'hidden'
            input.name = key
            input.value = String(formFields[key])
            form.appendChild(input)
            console.log(`✅ Champ ajouté: ${key} = ${formFields[key]}`)
          }
        })
        
        // template_id COMPLÈTEMENT RETIRÉ - pas besoin lors de la création
        // Le thème sera géré lors de la personnalisation de la boutique
        console.log('ℹ️ template_id retiré - sera géré lors de la personnalisation')
        
        // APPROCHE ULTRA-SIMPLE : Utiliser form.submit() directement
        // Laravel gérera la redirection automatiquement avec redirect()->route()
        console.log('🚀 ========== DÉBUT SOUMISSION FORMULAIRE ==========')
        console.log('📋 URL de soumission:', form.action)
        console.log('📋 Nombre total de champs:', form.elements.length)
        console.log('📋 Tous les champs du formulaire:')
        Array.from(form.elements).forEach((el, index) => {
          if (el.name) {
            console.log(`  ${index + 1}. ${el.name} = ${el.value}`)
          }
        })
        
        // Vérifier que le CSRF token est présent
        const csrfInForm = form.querySelector('input[name="_token"]')
        if (!csrfInForm || !csrfInForm.value) {
          console.error('❌ ERREUR: CSRF token manquant dans le formulaire!')
          setErrors(prev => ({ ...prev, submit: 'Erreur de sécurité. Veuillez rafraîchir la page.' }))
          if (submitButton) {
            submitButton.disabled = false
            submitButton.innerHTML = '<i class="fas fa-check icon-inline"></i> Créer ma boutique'
          }
          return
        }
        console.log('✅ CSRF token présent:', csrfInForm.value.substring(0, 10) + '...')
        
        // Ajouter le formulaire au DOM (caché)
        form.style.display = 'none'
        form.style.position = 'absolute'
        form.style.left = '-9999px'
        form.style.top = '-9999px'
        document.body.appendChild(form)
        
        console.log('✅ Formulaire ajouté au DOM')
        console.log('📤 Tentative de soumission...')
        
        // APPROCHE SIMPLIFIÉE : Soumettre directement le formulaire
        // Laravel redirigera automatiquement avec redirect()->route()
        console.log('🔄 Soumission du formulaire...')
        console.log('📋 URL:', form.action)
        console.log('📋 Nombre de champs:', form.elements.length)
        
        // Soumettre le formulaire - le navigateur suivra automatiquement la redirection HTTP
        try {
          // Ajouter le formulaire au DOM s'il n'y est pas déjà
          if (!form.parentNode) {
            document.body.appendChild(form)
          }
          
          // Soumettre directement - le navigateur gérera la redirection
          form.submit()
          console.log('✅ Formulaire soumis - redirection en cours...')
          
          // Note: On ne peut pas vérifier la redirection ici car form.submit() 
          // déclenche une navigation complète de la page
          
        } catch (error) {
          console.error('❌ ERREUR lors de form.submit():', error)
          setErrors(prev => ({ ...prev, submit: 'Erreur lors de la soumission: ' + error.message }))
          if (submitButton) {
            submitButton.disabled = false
            submitButton.innerHTML = '<i class="fas fa-check icon-inline"></i> Créer ma boutique'
          }
        }
      }
    }

    // Étape 1 : Nom de la boutique
    const Step1 = () => h('div', { className: 'wizard-step' }, [
      h('div', { className: 'step-header' }, [
        h(Icon, { name: 'tag', className: 'step-icon' }),
        h('h2', { className: 'step-title' }, 'Étape 1 : Nom de votre boutique'),
        h('p', { className: 'step-description' }, 'Donnez un nom à votre boutique. Ce nom apparaîtra sur votre site et dans les résultats de recherche.')
      ]),
      h('div', { className: 'step-content' }, [
        h('div', { className: 'form-group' }, [
          h('label', { className: 'form-label' }, [
            h(Icon, { name: 'store', className: 'icon-inline' }),
            ' Nom de la boutique *'
          ]),
          h('input', {
            type: 'text',
            id: 'store-name-input',
            ref: nameInputRef,
            className: `form-input ${errors.name ? 'input-error' : ''}`,
            placeholder: 'Ex: Ma Boutique Mode',
            defaultValue: formData.name,
            onInput: (e) => {
              // Ne pas mettre à jour formData ici pour éviter les re-renders
              // La valeur sera récupérée lors du submit ou onBlur
            },
            onBlur: (e) => {
              // Mettre à jour formData seulement quand l'utilisateur quitte le champ
              const value = e.target.value.trim()
              if (value !== formData.name) {
                updateFormData('name', value)
              }
            },
            required: true
          }),
          errors.name && h('div', { className: 'error-message' }, errors.name),
          h('p', { className: 'form-hint' }, '💡 Choisissez un nom accrocheur et mémorable pour votre boutique')
        ])
      ])
    ])

    // Étape 2 : Domaine d'activité
    const Step2 = () => h('div', { className: 'wizard-step' }, [
      h('div', { className: 'step-header' }, [
        h(Icon, { name: 'briefcase', className: 'step-icon' }),
        h('h2', { className: 'step-title' }, 'Étape 2 : Domaine d\'activité'),
        h('p', { className: 'step-description' }, 'Dans quel domaine vendez-vous ? Cela nous aide à personnaliser votre boutique.')
      ]),
      h('div', { className: 'step-content' }, [
        h('div', { className: 'categories-grid' }, 
          categories.map(cat => 
            h('label', {
              key: cat.id,
              className: `category-card ${formData.category === cat.id ? 'selected' : ''}`,
              onClick: () => updateFormData('category', cat.id)
            }, [
              h('input', {
                type: 'radio',
                name: 'category',
                value: cat.id,
                checked: formData.category === cat.id,
                onChange: () => updateFormData('category', cat.id),
                style: { display: 'none' }
              }),
              h(Icon, { name: cat.icon, className: 'category-icon' }),
              h('span', { className: 'category-name' }, cat.name),
              formData.category === cat.id && h(Icon, { name: 'check-circle', className: 'category-check' })
            ])
          )
        ),
        errors.category && h('div', { className: 'error-message' }, errors.category)
      ])
    ])

    // Étape 3 : Description
    const Step3 = () => h('div', { className: 'wizard-step', key: 'step-3' }, [
      h('div', { className: 'step-header' }, [
        h(Icon, { name: 'align-left', className: 'step-icon' }),
        h('h2', { className: 'step-title' }, 'Étape 3 : Description de votre boutique'),
        h('p', { className: 'step-description' }, 'Décrivez votre boutique en quelques mots. Cette description apparaîtra sur votre page d\'accueil.')
      ]),
      h('div', { className: 'step-content' }, [
        h('div', { className: 'form-group' }, [
          h('label', { className: 'form-label' }, [
            h(Icon, { name: 'file-alt', className: 'icon-inline' }),
            ' Description *'
          ]),
          h('textarea', {
            id: 'store-description-textarea',
            ref: descInputRef,
            className: `form-textarea ${errors.description ? 'input-error' : ''}`,
            placeholder: 'Ex: Votre destination pour les produits de mode tendance et abordables...',
            rows: 6,
            defaultValue: formData.description,
            onInput: (e) => {
              // Ne pas mettre à jour formData ici pour éviter les re-renders
              // La valeur sera récupérée lors du submit ou onBlur
            },
            onBlur: (e) => {
              // Mettre à jour formData seulement quand l'utilisateur quitte le champ
              const value = e.target.value.trim()
              if (value !== formData.description) {
                updateFormData('description', value)
              }
            },
            required: true
          }),
          errors.description && h('div', { className: 'error-message' }, errors.description),
          h('p', { className: 'form-hint' }, '💡 Décrivez ce qui rend votre boutique unique et attrayante')
        ])
      ])
    ])

    // Étape 4 : Bannière
    const Step4 = () => h('div', { className: 'wizard-step' }, [
      h('div', { className: 'step-header' }, [
        h(Icon, { name: 'image', className: 'step-icon' }),
        h('h2', { className: 'step-title' }, 'Étape 4 : Choisissez votre bannière'),
        h('p', { className: 'step-description' }, 'Sélectionnez une bannière qui représente votre boutique. Vous pourrez la modifier plus tard.')
      ]),
      h('div', { className: 'step-content' }, [
        h('div', { className: 'banners-grid' },
          bannerTemplates.map(banner => 
            h('label', {
              key: banner.id,
              className: `banner-card ${formData.banner === banner.id ? 'selected' : ''}`,
              onClick: () => updateFormData('banner', banner.id)
            }, [
              h('input', {
                type: 'radio',
                name: 'banner',
                value: banner.id,
                checked: formData.banner === banner.id,
                onChange: () => updateFormData('banner', banner.id),
                style: { display: 'none' }
              }),
              h('div', {
                className: 'banner-preview',
                style: { background: banner.preview }
              }),
              h('div', { className: 'banner-info' }, [
                h('div', { className: 'banner-name' }, banner.name),
                h('div', { className: 'banner-desc' }, banner.description)
              ]),
              formData.banner === banner.id && h(Icon, { name: 'check-circle', className: 'banner-check' })
            ])
          )
        ),
        errors.banner && h('div', { className: 'error-message' }, errors.banner)
      ])
    ])

    // Étape 5 : Thème
    const Step5 = () => {
      // Si pas de templates, utiliser le thème par défaut et permettre de continuer
      if (!templates || templates.length === 0) {
        return h('div', { className: 'wizard-step' }, [
          h('div', { className: 'step-header' }, [
            h(Icon, { name: 'palette', className: 'step-icon' }),
            h('h2', { className: 'step-title' }, 'Étape 5 : Thème de votre boutique'),
            h('p', { className: 'step-description' }, 'Un thème par défaut sera appliqué. Vous pourrez le changer lors de la personnalisation.')
          ]),
          h('div', { className: 'step-content' }, [
            h('div', { className: 'empty-templates' }, [
              h(Icon, { name: 'info-circle', className: 'empty-icon' }),
              h('p', { className: 'empty-text' }, 'Un thème par défaut sera appliqué. Vous pourrez choisir un autre thème lors de la personnalisation de votre boutique.')
            ])
          ])
        ])
      }

      return h('div', { className: 'wizard-step' }, [
        h('div', { className: 'step-header' }, [
          h(Icon, { name: 'palette', className: 'step-icon' }),
          h('h2', { className: 'step-title' }, 'Étape 5 : Choisissez votre thème'),
          h('p', { className: 'step-description' }, 'Sélectionnez le design de votre boutique. Vous pourrez le personnaliser après la création.')
        ]),
        h('div', { className: 'step-content' }, [
          h('div', { className: 'templates-grid' },
            templates.map(template => {
              const isSelected = String(formData.template_id) === String(template.id)
              return h('label', {
                key: template.id,
                className: `template-card ${isSelected ? 'selected' : ''}`,
                onClick: () => updateFormData('template_id', template.id)
              }, [
                h('input', {
                  type: 'radio',
                  name: 'template_id',
                  value: template.id,
                  checked: isSelected,
                  onChange: () => updateFormData('template_id', template.id),
                  style: { display: 'none' }
                }),
                h('div', { className: 'template-preview' }, [
                  h('div', { className: 'template-icon-wrapper' }, [
                    h(Icon, { 
                      name: template.name === 'Classic' ? 'book' : (template.name === 'Modern' ? 'rocket' : 'crown'),
                      className: 'template-icon'
                    })
                  ]),
                  h('div', { className: 'template-badge' }, template.name),
                  h('div', { className: 'template-description' }, template.description || 'Aucune description'),
                  h('div', { className: 'template-features' }, [
                    template.name === 'Classic' && h('div', { className: 'template-feature' }, [
                      h(Icon, { name: 'check', className: 'feature-icon' }),
                      ' Simple et épuré'
                    ]),
                    template.name === 'Modern' && h('div', { className: 'template-feature' }, [
                      h(Icon, { name: 'check', className: 'feature-icon' }),
                      ' Animations modernes'
                    ]),
                    template.name === 'Premium' && h('div', { className: 'template-feature' }, [
                      h(Icon, { name: 'check', className: 'feature-icon' }),
                      ' Design luxueux'
                    ])
                  ])
                ]),
                isSelected && h(Icon, { name: 'check-circle', className: 'template-check' })
              ])
            })
          ),
          errors.template_id && h('div', { className: 'error-message' }, errors.template_id)
        ])
      ])
    }

    const renderStep = () => {
      switch(currentStep) {
        case 1: return h(Step1)
        case 2: return h(Step2)
        case 3: return h(Step3)
        case 4: return h(Step4)
        // Étape 5 (thème) désactivée - thème par défaut appliqué automatiquement
        default: return h(Step1)
      }
    }

    // Plus besoin de synchroniser, on utilise useMemo maintenant

    return h('div', { className: 'store-wizard-container' }, [
      // Progress Bar
      h('div', { className: 'wizard-progress' }, [
        h('div', { className: 'progress-bar' }, [
          h('div', {
            className: 'progress-fill',
            style: { width: `${(currentStep / totalSteps) * 100}%` }
          })
        ]),
        h('div', { className: 'progress-steps' },
          Array.from({ length: totalSteps }, (_, i) => i + 1).map(step =>
            h('div', {
              key: step,
              className: `progress-step ${step <= currentStep ? 'active' : ''} ${step === currentStep ? 'current' : ''}`
            }, [
              h('div', { className: 'step-number' }, step),
              step < totalSteps && h('div', { className: 'step-connector' })
            ])
          )
        )
      ]),

      // Affichage des erreurs globales (bien visible en haut)
      (errors.submit || Object.keys(errors).length > 0) && h('div', {
        className: 'alert alert-danger',
        style: {
          margin: '20px 0',
          padding: '15px 20px',
          backgroundColor: '#fee',
          border: '2px solid #fcc',
          borderRadius: '8px',
          color: '#c33',
          fontSize: '16px',
          fontWeight: 'bold'
        }
      }, [
        h('i', { className: 'fas fa-exclamation-triangle', style: { marginRight: '10px' } }),
        errors.submit || 'Veuillez corriger les erreurs ci-dessous'
      ]),

      // Affichage détaillé des erreurs dans la console (pour debug)
      (() => {
        if (Object.keys(errors).length > 0) {
          console.error('❌ ERREURS DÉTECTÉES:', errors)
        }
        return null
      })(),

      // Step Content
      h('div', { className: 'wizard-content' }, renderStep()),

      // Navigation
      h('div', { className: 'wizard-actions' }, [
        currentStep > 1 && h('button', {
          type: 'button',
          className: 'btn-secondary',
          onClick: handleBack
        }, [
          h(Icon, { name: 'arrow-left', className: 'icon-inline' }),
          ' Précédent'
        ]),
        currentStep < totalSteps ? h('button', {
          type: 'button',
          className: 'btn-primary btn-large',
          onClick: handleNext
        }, [
          'Suivant',
          h(Icon, { name: 'arrow-right', className: 'icon-inline' })
        ]) : h('button', {
          type: 'button',
          className: 'btn-primary btn-large',
          onClick: handleSubmit,
          id: 'submit-store-btn'
        }, [
          h(Icon, { name: 'check', className: 'icon-inline' }),
          ' Créer ma boutique'
        ])
      ])
    ])
  }

  const root = createRoot(rootEl)
  root.render(h(StoreWizard))
  
  // Réinitialiser AOS après le rendu React
  setTimeout(() => {
    if (window.AOS) {
      AOS.refresh()
    }
  }, 100)
})()

