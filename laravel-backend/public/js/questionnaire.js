// Script pour le questionnaire de création de boutique
(function() {
    'use strict';

    // Initialiser AOS
    if (typeof AOS !== 'undefined') {
        AOS.init();
    }
    
    // Animation des options
    document.querySelectorAll('.option-card input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.querySelectorAll('.option-card').forEach(card => {
                card.classList.remove('selected');
            });
            if (this.checked) {
                this.closest('.option-card').classList.add('selected');
            }
        });
    });

    // Fonction pour obtenir l'emoji du drapeau depuis le code pays ISO
    function getCountryFlagEmoji(countryCode) {
        if (!countryCode || countryCode.length !== 2) return '🌍';
        const codePoints = countryCode
            .toUpperCase()
            .split('')
            .map(char => 127397 + char.charCodeAt());
        return String.fromCodePoint(...codePoints);
    }

    // Charger les pays depuis l'API REST Countries
    async function loadCountriesAndCurrencies() {
        const countrySelect = document.getElementById('country-select');
        const countryFlagDisplay = document.getElementById('country-flag-display');
        
        if (countrySelect) {
            try {
                // Charger les pays depuis l'API REST Countries
                const response = await fetch('https://restcountries.com/v3.1/all?fields=name,cca2,flags');
                const countries = await response.json();
                
                // Trier les pays par nom
                const sortedCountries = countries.sort((a, b) => {
                    const nameA = a.name?.common || a.name || '';
                    const nameB = b.name?.common || b.name || '';
                    return nameA.localeCompare(nameB);
                });
                
                // Vider le select d'abord
                countrySelect.innerHTML = '<option value="">Sélectionnez votre pays</option>';
                
                sortedCountries.forEach(country => {
                    const countryName = country.name?.common || country.name || 'Unknown';
                    const countryCode = country.cca2 || '';
                    const flagEmoji = countryCode ? getCountryFlagEmoji(countryCode) : '🌍';
                    
                    const option = document.createElement('option');
                    option.value = countryName;
                    option.textContent = `${flagEmoji} ${countryName}`;
                    option.dataset.flag = flagEmoji;
                    option.dataset.code = countryCode;
                    countrySelect.appendChild(option);
                });

                // Afficher le drapeau quand un pays est sélectionné
                countrySelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    if (selectedOption && selectedOption.dataset.flag && selectedOption.value) {
                        countryFlagDisplay.innerHTML = `<span class="flag-large">${selectedOption.dataset.flag}</span>`;
                        countryFlagDisplay.style.display = 'block';
                    } else {
                        countryFlagDisplay.innerHTML = '';
                        countryFlagDisplay.style.display = 'none';
                    }
                });
            } catch (error) {
                console.error('Erreur lors du chargement des pays:', error);
                // Fallback: charger depuis un fichier local si l'API échoue
                countrySelect.innerHTML = '<option value="">Erreur de chargement. Veuillez rafraîchir la page.</option>';
            }
        }

        // Charger les devises (liste simplifiée avec les principales)
        const currencies = [
            { code: 'USD', name: 'Dollar américain', symbol: '$', flag: '🇺🇸' },
            { code: 'EUR', name: 'Euro', symbol: '€', flag: '🇪🇺' },
            { code: 'GBP', name: 'Livre sterling', symbol: '£', flag: '🇬🇧' },
            { code: 'JPY', name: 'Yen japonais', symbol: '¥', flag: '🇯🇵' },
            { code: 'CNY', name: 'Yuan chinois', symbol: '¥', flag: '🇨🇳' },
            { code: 'INR', name: 'Roupie indienne', symbol: '₹', flag: '🇮🇳' },
            { code: 'GNF', name: 'Franc guinéen', symbol: 'FG', flag: '🇬🇳' },
            { code: 'XOF', name: 'Franc CFA (Ouest)', symbol: 'CFA', flag: '🌍' },
            { code: 'XAF', name: 'Franc CFA (Centre)', symbol: 'CFA', flag: '🌍' },
            { code: 'NGN', name: 'Naira nigérian', symbol: '₦', flag: '🇳🇬' },
            { code: 'ZAR', name: 'Rand sud-africain', symbol: 'R', flag: '🇿🇦' },
            { code: 'EGP', name: 'Livre égyptienne', symbol: 'E£', flag: '🇪🇬' },
            { code: 'MAD', name: 'Dirham marocain', symbol: 'DH', flag: '🇲🇦' },
            { code: 'TND', name: 'Dinar tunisien', symbol: 'DT', flag: '🇹🇳' },
            { code: 'DZD', name: 'Dinar algérien', symbol: 'DA', flag: '🇩🇿' },
        ];

        const currencySelect = document.getElementById('currency-select');
        const currencyDisplay = document.getElementById('currency-display');
        const currencyRateInput = document.getElementById('currency-rate');
        
        if (currencySelect) {
            // Vider le select d'abord
            currencySelect.innerHTML = '<option value="">Sélectionnez une devise</option>';
            
            currencies.forEach(currency => {
                const option = document.createElement('option');
                option.value = currency.code;
                option.textContent = `${currency.flag} ${currency.code} (${currency.symbol}) - ${currency.name}`;
                option.dataset.symbol = currency.symbol;
                option.dataset.flag = currency.flag;
                currencySelect.appendChild(option);
            });

            // Afficher le symbole et initialiser le taux de change
            currencySelect.addEventListener('change', async function() {
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption && selectedOption.dataset.symbol && selectedOption.value) {
                    currencyDisplay.innerHTML = `<span class="currency-symbol-large">${selectedOption.dataset.flag} ${selectedOption.dataset.symbol}</span>`;
                    currencyDisplay.style.display = 'block';
                    
                    // Initialiser le taux de change (pour conversion future)
                    if (currencyRateInput) {
                        currencyRateInput.value = '1';
                    }
                } else {
                    currencyDisplay.innerHTML = '';
                    currencyDisplay.style.display = 'none';
                }
            });
        }
    }

    // Démarrer le chargement
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', loadCountriesAndCurrencies);
    } else {
        loadCountriesAndCurrencies();
    }
})();

