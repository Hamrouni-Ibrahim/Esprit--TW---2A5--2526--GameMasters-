
// Validation functions for forms

function validateEducationForm() {
    let isValid = true;
    
    // Reset error messages
    document.getElementById('titleError').textContent = '';
    document.getElementById('descriptionError').textContent = '';
    document.getElementById('dureeError').textContent = '';
    document.getElementById('categorieError').textContent = ''; // ADD THIS LINE
    
    // Validate title
    const title = document.getElementById('title').value.trim();
    if (title === '') {
        document.getElementById('titleError').textContent = 'Le titre est requis.';
        isValid = false;
    } else if (title.length < 3) {
        document.getElementById('titleError').textContent = 'Le titre doit contenir au moins 3 caractères.';
        isValid = false;
    } else if (title.length > 255) {
        document.getElementById('titleError').textContent = 'Le titre ne peut pas dépasser 255 caractères.';
        isValid = false;
    }
    
    // Validate description
    const description = document.getElementById('description').value.trim();
    if (description === '') {
        document.getElementById('descriptionError').textContent = 'La description est requise.';
        isValid = false;
    } else if (description.length < 10) {
        document.getElementById('descriptionError').textContent = 'La description doit contenir au moins 10 caractères.';
        isValid = false;
    }
    
    // Validate duration (duree) - required
    const duree = document.getElementById('duree').value.trim();
    if (duree === '') {
        document.getElementById('dureeError').textContent = 'La durée est requise.';
        isValid = false;
    } else if (isNaN(duree) || parseInt(duree) < 0) {
        document.getElementById('dureeError').textContent = 'La durée doit être un nombre positif.';
        isValid = false;
    }
    
    // ADD CATEGORY VALIDATION HERE
    const categorie = document.getElementById('categorie').value.trim();
    if (categorie === '') {
        document.getElementById('categorieError').textContent = 'La catégorie est requise.';
        isValid = false;
    } else if (categorie.length < 2) {
        document.getElementById('categorieError').textContent = 'La catégorie doit contenir au moins 2 caractères.';
        isValid = false;
    }
    
    return isValid;
}

function validateFormationForm() {
    let isValid = true;
    
    // Reset error messages
    document.getElementById('titleError').textContent = '';
    document.getElementById('descriptionError').textContent = '';
    document.getElementById('dureeError').textContent = '';
    document.getElementById('lienRessourcesError').textContent = '';
    document.getElementById('categorieError').textContent = ''; // ADD THIS LINE
    
    // Validate title
    const title = document.getElementById('title').value.trim();
    if (title === '') {
        document.getElementById('titleError').textContent = 'Le titre est requis.';
        isValid = false;
    } else if (title.length < 3) {
        document.getElementById('titleError').textContent = 'Le titre doit contenir au moins 3 caractères.';
        isValid = false;
    } else if (title.length > 255) {
        document.getElementById('titleError').textContent = 'Le titre ne peut pas dépasser 255 caractères.';
        isValid = false;
    }
    
    // Validate description
    const description = document.getElementById('description').value.trim();
    if (description === '') {
        document.getElementById('descriptionError').textContent = 'La description est requise.';
        isValid = false;
    } else if (description.length < 10) {
        document.getElementById('descriptionError').textContent = 'La description doit contenir au moins 10 caractères.';
        isValid = false;
    }
    
    // Validate duration (duree) - required
    const duree = document.getElementById('duree').value.trim();
    if (duree === '') {
        document.getElementById('dureeError').textContent = 'La durée est requise.';
        isValid = false;
    } else if (isNaN(duree) || parseInt(duree) < 0) {
        document.getElementById('dureeError').textContent = 'La durée doit être un nombre positif.';
        isValid = false;
    }
    
    // Validate resource link (lien_ressources) - required for formations
    const lienRessources = document.getElementById('lien_ressources').value.trim();
    if (lienRessources === '') {
        document.getElementById('lienRessourcesError').textContent = 'Le lien vers les ressources est requis.';
        isValid = false;
    } else if (!isValidUrl(lienRessources)) {
        document.getElementById('lienRessourcesError').textContent = 'Veuillez entrer une URL valide (commençant par http:// ou https://).';
        isValid = false;
    }
    
    // ADD CATEGORY VALIDATION HERE
    const categorie = document.getElementById('categorie').value.trim();
    if (categorie === '') {
        document.getElementById('categorieError').textContent = 'La catégorie est requise.';
        isValid = false;
    } else if (categorie.length < 2) {
        document.getElementById('categorieError').textContent = 'La catégorie doit contenir au moins 2 caractères.';
        isValid = false;
    }
    
    return isValid;
}

// URL validation function
function isValidUrl(string) {
    try {
        const url = new URL(string);
        return url.protocol === 'http:' || url.protocol === 'https:';
    } catch (_) {
        return false;
    }
}

// Real-time validation
document.addEventListener('DOMContentLoaded', function() {
    const titleInput = document.getElementById('title');
    const descriptionInput = document.getElementById('description');
    const dureeInput = document.getElementById('duree');
    const lienRessourcesInput = document.getElementById('lien_ressources');
    const categorieInput = document.getElementById('categorie'); // ADD THIS
    
    // Title validation
    if (titleInput) {
        titleInput.addEventListener('blur', function() {
            const title = this.value.trim();
            const errorElement = document.getElementById('titleError');
            
            if (title === '') {
                errorElement.textContent = 'Le titre est requis.';
            } else if (title.length < 3) {
                errorElement.textContent = 'Le titre doit contenir au moins 3 caractères.';
            } else if (title.length > 255) {
                errorElement.textContent = 'Le titre ne peut pas dépasser 255 caractères.';
            } else {
                errorElement.textContent = '';
            }
        });
    }
    
    // Description validation
    if (descriptionInput) {
        descriptionInput.addEventListener('blur', function() {
            const description = this.value.trim();
            const errorElement = document.getElementById('descriptionError');
            
            if (description === '') {
                errorElement.textContent = 'La description est requise.';
            } else if (description.length < 10) {
                errorElement.textContent = 'La description doit contenir au moins 10 caractères.';
            } else {
                errorElement.textContent = '';
            }
        });
    }
    
    // Duration validation
    if (dureeInput) {
        dureeInput.addEventListener('blur', function() {
            const duree = this.value.trim();
            const errorElement = document.getElementById('dureeError');
            
            if (duree === '') {
                errorElement.textContent = 'La durée est requise.';
            } else if (isNaN(duree) || parseInt(duree) < 0) {
                errorElement.textContent = 'La durée doit être un nombre positif.';
            } else {
                errorElement.textContent = '';
            }
        });
    }
    
    // Resource link validation (only for formations)
    if (lienRessourcesInput) {
        lienRessourcesInput.addEventListener('blur', function() {
            const lienRessources = this.value.trim();
            const errorElement = document.getElementById('lienRessourcesError');
            
            if (lienRessources === '') {
                errorElement.textContent = 'Le lien vers les ressources est requis.';
            } else if (!isValidUrl(lienRessources)) {
                errorElement.textContent = 'Veuillez entrer une URL valide (commençant par http:// ou https://).';
            } else {
                errorElement.textContent = '';
            }
        });
    }
    
    // ADD CATEGORY VALIDATION - Real-time
    if (categorieInput) {
        categorieInput.addEventListener('blur', function() {
            const categorie = this.value.trim();
            const errorElement = document.getElementById('categorieError');
            
            if (categorie === '') {
                errorElement.textContent = 'La catégorie est requise.';
            } else if (categorie.length < 2) {
                errorElement.textContent = 'La catégorie doit contenir au moins 2 caractères.';
            } else {
                errorElement.textContent = '';
            }
        });
    }
});