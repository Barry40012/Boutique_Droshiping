// Script pour afficher le toast de succès après création de boutique
(function() {
    'use strict';
    
    // Vérifier si on doit afficher le toast (via data attribute)
    const successToast = document.getElementById('success-toast');
    const shouldShowToast = document.body.dataset.showSuccessToast === 'true';
    
    if (successToast && shouldShowToast) {
        setTimeout(() => {
            successToast.style.display = 'block';
            setTimeout(() => {
                successToast.style.opacity = '1';
                successToast.style.transform = 'translateY(0)';
            }, 100);
            
            // Masquer après 5 secondes
            setTimeout(() => {
                successToast.style.opacity = '0';
                successToast.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    successToast.style.display = 'none';
                }, 300);
            }, 5000);
        }, 500);
    }
})();

