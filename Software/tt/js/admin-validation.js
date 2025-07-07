// Admin form validation functions

// Validate admin login form
function validateAdminLogin() {
    const username = document.getElementById('username');
    const password = document.getElementById('password');
    let isValid = true;

    // Reset error states
    resetErrorState(username);
    resetErrorState(password);

    // Username validation
    if (!username.value.trim()) {
        showError(username, 'Username is required');
        isValid = false;
    }

    // Password validation
    if (!password.value) {
        showError(password, 'Password is required');
        isValid = false;
    } else if (password.value.length < 8) {
        showError(password, 'Password must be at least 8 characters long');
        isValid = false;
    }

    return isValid;
}

// Validate add artist form
function validateAddArtist() {
    const username = document.getElementById('artist_username');
    const email = document.getElementById('artist_email');
    const password = document.getElementById('artist_password');
    const specialization = document.getElementById('artist_specialization');
    const image = document.getElementById('artist_image');
    let isValid = true;

    // Reset all error states
    [username, email, password, specialization, image].forEach(resetErrorState);

    // Username validation
    const usernameValue = username.value.trim();
    if (!usernameValue) {
        showError(username, 'Username is required');
        isValid = false;
    } else if (usernameValue.length < 3) {
        showError(username, 'Username must be at least 3 characters long');
        isValid = false;
    } else if (usernameValue.length > 50) {
        showError(username, 'Username cannot exceed 50 characters');
        isValid = false;
    } else if (!/^[A-Za-z ]+$/.test(usernameValue)) {
        showError(username, 'Username can only contain letters and spaces');
        isValid = false;
    }

    // Email validation
    if (!email.value.trim()) {
        showError(email, 'Email is required');
        isValid = false;
    } else if (!isValidEmail(email.value)) {
        showError(email, 'Please enter a valid email address');
        isValid = false;
    }

    // Password validation with comprehensive checks
    if (!password.value) {
        showError(password, 'Password is required');
        isValid = false;
    } else {
        const passwordErrors = validatePassword(password.value);
        if (passwordErrors.length > 0) {
            showError(password, passwordErrors[0]); // Show first error
            isValid = false;
        }
    }

    // Specialization validation
    if (!specialization.value.trim()) {
        showError(specialization, 'Specialization is required');
        isValid = false;
    }

    // Image validation
    if (image.files.length > 0) {
        const file = image.files[0];
        const fileSize = file.size / 1024 / 1024; // Convert to MB
        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];

        if (!allowedTypes.includes(file.type)) {
            showError(image, 'Please upload a valid image file (JPG, JPEG, or PNG)');
            isValid = false;
        } else if (fileSize > 5) {
            showError(image, 'Image size should not exceed 5MB');
            isValid = false;
        }
    }

    return isValid;
}

// Comprehensive password validation
function validatePassword(password) {
    const errors = [];
    
    if (password.length < 8) {
        errors.push('Password must be at least 8 characters long');
    }
    if (!/[A-Z]/.test(password)) {
        errors.push('Password must contain at least one uppercase letter');
    }
    if (!/[a-z]/.test(password)) {
        errors.push('Password must contain at least one lowercase letter');
    }
    if (!/[0-9]/.test(password)) {
        errors.push('Password must contain at least one number');
    }
    
    return errors;
}

// Helper functions
function showError(element, message) {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;
    element.classList.add('error');
    
    // Remove any existing error message
    const existingError = element.parentElement.querySelector('.error-message');
    if (existingError) {
        existingError.remove();
    }
    
    element.parentElement.appendChild(errorDiv);
}

function resetErrorState(element) {
    element.classList.remove('error');
    const errorMessage = element.parentElement.querySelector('.error-message');
    if (errorMessage) {
        errorMessage.remove();
    }
}

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}
