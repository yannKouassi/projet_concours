class FormValidator {
    constructor() {
        this.form = document.getElementById('registerForm');
        this.fields = {
            nom: document.getElementById('nom'),
            prenom: document.getElementById('prenom'),
            email: document.getElementById('email'),
            password: document.getElementById('password'),
            confirmPassword: document.getElementById('confirmPassword')
        };
        this.initValidation();
    }

    initValidation() {
        // Validation en temps réel pour chaque champ
        Object.keys(this.fields).forEach(fieldName => {
            const field = this.fields[fieldName];
            field.addEventListener('blur', () => this.validateField(fieldName));
            field.addEventListener('input', () => this.validateField(fieldName));
        });

        // Validation spéciale pour le mot de passe
        this.fields.password.addEventListener('input', () => this.checkPasswordStrength());

    }

    validateField(fieldName) {
        const field = this.fields[fieldName];
        const errorElement = document.getElementById(fieldName + 'Error');
        const successElement = document.getElementById(fieldName + 'Success');

        let isValid = true;
        let errorMessage = '';

        // Reset des classes
        field.classList.remove('error', 'success');
        if (errorElement) errorElement.style.display = 'none';
        if (successElement) successElement.style.display = 'none';

        switch(fieldName) {
            case 'nom':
            case 'prenom':
                if (!field.value.trim()) {
                    errorMessage = 'Ce champ est obligatoire';
                    isValid = false;
                } else if (field.value.trim().length < 2) {
                    errorMessage = 'Minimum 2 caractères';
                    isValid = false;
                } else if (!/^[a-zA-ZÀ-ÿ\s-]+$/.test(field.value.trim())) {
                    errorMessage = 'Seulement des lettres et espaces';
                    isValid = false;
                }
                break;

            case 'email':
                if (!field.value.trim()) {
                    errorMessage = 'Email obligatoire';
                    isValid = false;
                } else if (!this.isValidEmail(field.value)) {
                    errorMessage = 'Format email invalide';
                    isValid = false;
                } else {
                    // Vérifier si email existe (simulation)

                }
                break;

            case 'password':
                if (!field.value) {
                    errorMessage = 'Mot de passe obligatoire';
                    isValid = false;
                } else if (field.value.length < 8) {
                    errorMessage = 'Minimum 8 caractères';
                    isValid = false;
                } else if (!this.isStrongPassword(field.value)) {
                    errorMessage = 'Doit contenir: majuscule, minuscule, chiffre';
                    isValid = false;
                }
                break;

            case 'confirmPassword':
                if (!field.value) {
                    errorMessage = 'Confirmation obligatoire';
                    isValid = false;
                } else if (field.value !== this.fields.password.value) {
                    errorMessage = 'Les mots de passe ne correspondent pas';
                    isValid = false;
                } else if (this.fields.password.value && field.value === this.fields.password.value) {
                    if (successElement) {
                        successElement.textContent = 'Mots de passe identiques ✓';
                        successElement.style.display = 'block';
                    }
                }
                break;
        }

        if (!isValid) {
            field.classList.add('error');
            if (errorElement) {
                errorElement.textContent = errorMessage;
                errorElement.style.display = 'block';
            }
        } else {
            field.classList.add('success');
        }

        return isValid;
    }

    isValidEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    isStrongPassword(password) {
        const hasUpper = /[A-Z]/.test(password);
        const hasLower = /[a-z]/.test(password);
        const hasNumber = /\d/.test(password);
        return hasUpper && hasLower && hasNumber;
    }

    checkPasswordStrength() {
        const password = this.fields.password.value;
        const strengthElement = document.getElementById('passwordStrength');

        if (!password) {
            strengthElement.textContent = '';
            return;
        }

        let strength = 0;
        let feedback = [];

        if (password.length >= 8) strength++;
        else feedback.push('8+ caractères');

        if (/[A-Z]/.test(password)) strength++;
        else feedback.push('une majuscule');

        if (/[a-z]/.test(password)) strength++;
        else feedback.push('une minuscule');

        if (/\d/.test(password)) strength++;
        else feedback.push('un chiffre');

        if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) strength++;

        let strengthText = '';
        let strengthClass = '';

        if (strength < 2) {
            strengthText = 'Faible - Manque: ' + feedback.join(', ');
            strengthClass = 'strength-weak';
        } else if (strength < 4) {
            strengthText = 'Moyen - Manque: ' + feedback.join(', ');
            strengthClass = 'strength-medium';
        } else {
            strengthText = 'Fort ✓';
            strengthClass = 'strength-strong';
        }

        strengthElement.textContent = strengthText;
        strengthElement.className = 'password-strength ' + strengthClass;
    }





}

// Initialiser la validation au chargement
document.addEventListener('DOMContentLoaded', () => {
    new FormValidator();
});