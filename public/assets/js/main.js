document.addEventListener('DOMContentLoaded', () => {
    const tabs = document.querySelectorAll('[data-auth-tab]');
    const panels = document.querySelectorAll('[data-auth-panel]');
    const passwordToggles = document.querySelectorAll('[data-toggle-password]');
    const googleButton = document.querySelector('[data-google-placeholder]');
    const facebookButton = document.querySelector('[data-facebook-placeholder]');
    const googleMessage = document.querySelector('[data-google-message]');

    if (tabs.length && panels.length) {
        const activate = (name) => {
            tabs.forEach((tab) => {
                const active = tab.dataset.authTab === name;
                tab.classList.toggle('is-active', active);
                tab.setAttribute('aria-selected', active ? 'true' : 'false');
            });

            panels.forEach((panel) => {
                panel.classList.toggle('is-active', panel.dataset.authPanel === name);
            });
        };

        tabs.forEach((tab) => {
            tab.addEventListener('click', () => activate(tab.dataset.authTab));
        });
    }

    passwordToggles.forEach((toggle) => {
        toggle.addEventListener('click', () => {
            const wrapper = toggle.closest('.password-field');
            const input = wrapper?.querySelector('input');

            if (!(input instanceof HTMLInputElement)) {
                return;
            }

            const shouldShow = input.type === 'password';
            input.type = shouldShow ? 'text' : 'password';
            toggle.textContent = shouldShow ? 'Hide' : 'Show';
        });
    });

    if (googleButton instanceof HTMLButtonElement && googleMessage instanceof HTMLElement) {
        googleButton.addEventListener('click', () => {
            googleMessage.textContent = 'Google sign-in is currently disabled for authentication.';
        });
    }

    if (facebookButton instanceof HTMLButtonElement && googleMessage instanceof HTMLElement) {
        facebookButton.addEventListener('click', () => {
            googleMessage.textContent = 'Facebook sign-in is currently disabled for authentication.';
        });
    }
});
