// Form validation functions
function validateName(name) {
    const nameRegex = /^[A-Za-z\s]{2,50}$/;
    return nameRegex.test(name);
}

function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function validatePassword(password) {
    return password.length >= 8;
}

// Login form validation
function validateLoginForm(event) {
    const email = document.getElementById('login-email').value;
    const password = document.getElementById('login-password').value;
    let isValid = true;
    let errorMessage = '';

    if (!validateEmail(email)) {
        errorMessage = 'Please enter a valid email address';
        isValid = false;
    }

    if (!validatePassword(password)) {
        errorMessage = 'Password must be at least 8 characters long';
        isValid = false;
    }

    if (!isValid) {
        event.preventDefault();
        showError(errorMessage);
    }
}

// Registration form validation
function validateRegistrationForm(event) {
    const username = document.getElementById('username').value;
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    let isValid = true;
    let errorMessage = '';

    if (!validateName(username)) {
        errorMessage = 'Username must contain only letters';
        isValid = false;
    }

    if (!validateEmail(email)) {
        errorMessage = 'Please enter a valid email address';
        isValid = false;
    }

    if (!validatePassword(password)) {
        errorMessage = 'Password must be at least 8 characters long';
        isValid = false;
    }

    if (password !== confirmPassword) {
        errorMessage = 'Passwords do not match';
        isValid = false;
    }

    if (!isValid) {
        event.preventDefault();
        showError(errorMessage);
    }
}

// Error display function
function showError(message) {
    let errorDiv = document.querySelector('.error-message');
    if (!errorDiv) {
        errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        const form = document.querySelector('form');
        form.insertBefore(errorDiv, form.firstChild);
    }
    errorDiv.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;
    errorDiv.style.display = 'block';
}
