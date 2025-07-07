// Mobile menu toggle
document.addEventListener('DOMContentLoaded', () => {
    // Add mobile menu functionality when needed
    const mobileWidth = 768;
    
    // Form validation for appointments
    const appointmentForm = document.querySelector('#appointment-form');
    if (appointmentForm) {
        appointmentForm.addEventListener('submit', (e) => {
            e.preventDefault();
            validateAppointmentForm();
        });
    }
});

// Appointment form validation
function validateAppointmentForm() {
    const date = document.querySelector('#appointment-date').value;
    const time = document.querySelector('#appointment-time').value;
    const service = document.querySelector('#service-type').value;

    if (!date || !time || !service) {
        alert('Please fill in all required fields');
        return false;
    }

    // Check if date is in the future
    const selectedDate = new Date(date + ' ' + time);
    const now = new Date();
    
    if (selectedDate <= now) {
        alert('Please select a future date and time');
        return false;
    }

    document.querySelector('#appointment-form').submit();
}
