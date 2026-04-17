document.addEventListener('submit', (event) => {
    const form = event.target;

    if (!(form instanceof HTMLFormElement) || !form.matches('[data-validate]')) {
        return;
    }

    const inputs = Array.from(form.querySelectorAll('input'));
    let firstInvalidField = null;

    const setError = (input, message) => {
        if (!(input instanceof HTMLInputElement)) {
            return;
        }

        if (firstInvalidField === null) {
            firstInvalidField = input;
        }

        input.classList.add('is-invalid');

        const errorTarget = input.closest('label')?.querySelector(`[data-error-for="${input.name}"]`);
        if (errorTarget instanceof HTMLElement) {
            errorTarget.textContent = message;
        }
    };

    inputs.forEach((input) => {
        if (!(input instanceof HTMLInputElement)) {
            return;
        }

        input.classList.remove('is-invalid');
        const errorTarget = input.closest('label')?.querySelector(`[data-error-for="${input.name}"]`);
        if (errorTarget instanceof HTMLElement) {
            errorTarget.textContent = '';
        }
    });

    const email = form.querySelector('input[name="email"]');
    const password = form.querySelector('input[name="password"]');
    const confirmation = form.querySelector('input[name="password_confirmation"]');
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (email instanceof HTMLInputElement) {
        const value = email.value.trim();

        if (value === '') {
            setError(email, 'Enter your email address.');
        } else if (!emailPattern.test(value)) {
            setError(email, 'Invalid email address.');
        }
    }

    if (password instanceof HTMLInputElement) {
        const value = password.value.trim();

        if (value === '') {
            setError(password, 'Enter your password.');
        } else if (confirmation instanceof HTMLInputElement && value.length < 8) {
            setError(password, 'Password must be at least 8 characters.');
        }
    }

    if (confirmation instanceof HTMLInputElement) {
        const confirmationValue = confirmation.value.trim();

        if (confirmationValue === '') {
            setError(confirmation, 'Please confirm your password.');
        } else if (password instanceof HTMLInputElement && password.value !== confirmation.value) {
            setError(confirmation, 'Passwords do not match.');
        }
    }

    if (firstInvalidField instanceof HTMLInputElement) {
        event.preventDefault();
        firstInvalidField.focus();
    }
});
