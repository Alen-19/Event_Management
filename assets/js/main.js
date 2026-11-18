/**
 * Main Client-Side JavaScript
 * Event Management System
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Form Validation for Registration Form
    const registrationForm = document.getElementById('registrationForm');
    if (registrationForm) {
        const nameInput = document.getElementById('user_name');
        const emailInput = document.getElementById('user_email');
        const eventSelect = document.getElementById('event_id');

        const validateName = () => {
            const val = nameInput.value.trim();
            if (val === '') {
                setFieldInvalid(nameInput, 'Full Name is required.');
                return false;
            } else if (val.length < 2) {
                setFieldInvalid(nameInput, 'Name must be at least 2 characters long.');
                return false;
            } else {
                setFieldValid(nameInput);
                return true;
            }
        };

        const validateEmail = () => {
            const val = emailInput.value.trim();
            const emailRegex = /^(?=[a-zA-Z0-9._-]{6,64}@)[a-zA-Z0-9]+(?:[._-][a-zA-Z0-9]+)*@[a-zA-Z0-9]+(?:-[a-zA-Z0-9]+)*(?:\.[a-zA-Z0-9]+(?:-[a-zA-Z0-9]+)*)*\.[a-zA-Z]{2,}$/;
            if (val === '') {
                setFieldInvalid(emailInput, 'Email Address is required.');
                return false;
            } else if (!emailRegex.test(val)) {
                setFieldInvalid(emailInput, 'Please enter a valid email address (username must be at least 6 characters, e.g. alexander@example.com).');
                return false;
            } else {
                setFieldValid(emailInput);
                return true;
            }
        };

        const validateEvent = () => {
            const val = eventSelect.value.trim();
            if (val === '' || val === '0') {
                setFieldInvalid(eventSelect, 'Please select an event to register.');
                return false;
            } else {
                setFieldValid(eventSelect);
                return true;
            }
        };

        // Real-time input listeners
        nameInput.addEventListener('input', validateName);
        nameInput.addEventListener('blur', validateName);

        emailInput.addEventListener('input', validateEmail);
        emailInput.addEventListener('blur', validateEmail);

        eventSelect.addEventListener('change', validateEvent);

        // Form submit validation
        registrationForm.addEventListener('submit', (e) => {
            const isNameValid = validateName();
            const isEmailValid = validateEmail();
            const isEventValid = validateEvent();

            if (!isNameValid || !isEmailValid || !isEventValid) {
                e.preventDefault();
                // Focus the first invalid field
                if (!isNameValid) nameInput.focus();
                else if (!isEmailValid) emailInput.focus();
                else if (!isEventValid) eventSelect.focus();
            }
        });
    }

    // Helper functions for field states
    function setFieldInvalid(inputEl, message) {
        inputEl.classList.add('is-invalid');
        const feedbackEl = inputEl.parentElement.querySelector('.invalid-feedback');
        if (feedbackEl) {
            feedbackEl.textContent = message;
        }
    }

    function setFieldValid(inputEl) {
        inputEl.classList.remove('is-invalid');
        const feedbackEl = inputEl.parentElement.querySelector('.invalid-feedback');
        if (feedbackEl) {
            feedbackEl.textContent = '';
        }
    }
});
