document.addEventListener('DOMContentLoaded', () => {
    const tabs = document.querySelectorAll('[data-auth-tab]');
    const panels = document.querySelectorAll('[data-auth-panel]');
    const passwordToggles = document.querySelectorAll('[data-toggle-password]');
    const googleButton = document.querySelector('[data-google-placeholder]');
    const facebookButton = document.querySelector('[data-facebook-placeholder]');
    const googleMessage = document.querySelector('[data-google-message]');
    const homePage = document.querySelector('[data-home-page]');

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

    if (homePage instanceof HTMLElement) {
        const parseJson = (value) => {
            try {
                return JSON.parse(value || '[]');
            } catch (error) {
                return [];
            }
        };

        const featuredFoods = parseJson(homePage.dataset.featuredFoods);
        const menuItems = parseJson(homePage.dataset.menuItems);
        const navToggle = document.querySelector('[data-nav-toggle]');
        const navMenu = document.querySelector('[data-nav-menu]');
        const navLinks = document.querySelectorAll('[data-nav-menu] a[href^="#"]');
        const profileToggle = document.querySelector('[data-profile-toggle]');
        const profileDropdown = document.querySelector('[data-profile-dropdown]');
        const categoryTrack = document.querySelector('[data-category-track]');
        const categoryPrev = document.querySelector('[data-category-prev]');
        const categoryNext = document.querySelector('[data-category-next]');
        const menuGrid = document.querySelector('[data-menu-grid]');
        const cartCount = document.querySelector('[data-cart-count]');
        let cartTotal = 0;

        if (navToggle instanceof HTMLButtonElement && navMenu instanceof HTMLElement) {
            navToggle.addEventListener('click', () => {
                const isOpen = navMenu.classList.toggle('is-open');
                navToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            });
        }

        if (profileToggle instanceof HTMLButtonElement && profileDropdown instanceof HTMLElement) {
            profileToggle.addEventListener('click', () => {
                const isOpen = profileDropdown.classList.toggle('is-open');
                profileToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            });

            document.addEventListener('click', (event) => {
                const target = event.target;

                if (!(target instanceof Node)) {
                    return;
                }

                const profileMenu = document.querySelector('[data-profile-menu]');

                if (profileMenu instanceof HTMLElement && !profileMenu.contains(target)) {
                    profileDropdown.classList.remove('is-open');
                    profileToggle.setAttribute('aria-expanded', 'false');
                }
            });
        }

        navLinks.forEach((link) => {
            link.addEventListener('click', (event) => {
                const targetId = link.getAttribute('href');

                if (!targetId || !targetId.startsWith('#')) {
                    return;
                }

                const section = document.querySelector(targetId);

                if (!(section instanceof HTMLElement)) {
                    return;
                }

                event.preventDefault();
                section.scrollIntoView({ behavior: 'smooth', block: 'start' });
                navMenu?.classList.remove('is-open');
                navToggle?.setAttribute('aria-expanded', 'false');
            });
        });

        if (categoryTrack instanceof HTMLElement) {
            const scrollAmount = () => {
                const firstCard = categoryTrack.querySelector('.category-card');

                if (!(firstCard instanceof HTMLElement)) {
                    return Math.min(categoryTrack.clientWidth * 0.9, 340);
                }

                const styles = window.getComputedStyle(categoryTrack);
                const gap = Number.parseFloat(styles.columnGap || styles.gap || '0') || 0;

                return firstCard.getBoundingClientRect().width + gap;
            };

            categoryPrev?.addEventListener('click', () => {
                categoryTrack.scrollBy({ left: -scrollAmount(), behavior: 'smooth' });
            });

            categoryNext?.addEventListener('click', () => {
                categoryTrack.scrollBy({ left: scrollAmount(), behavior: 'smooth' });
            });
        }

        if (menuGrid instanceof HTMLElement && Array.isArray(menuItems)) {
            menuGrid.innerHTML = menuItems.map((item, index) => `
                <article class="menu-card" style="animation-delay:${index * 0.08}s">
                    <div class="menu-card-media">
                        <img src="${item.image}" alt="${item.name}">
                    </div>
                    <div class="menu-card-body">
                        <div class="menu-card-head">
                            <h3>${item.name}</h3>
                            <span class="menu-card-price">$${Number(item.price).toFixed(2)}</span>
                        </div>
                        <p>${item.description}</p>
                        <div class="menu-card-meta">
                            <span class="menu-tag">${item.category}</span>
                            <button class="button button-primary" type="button" data-add-cart>Add to Cart</button>
                        </div>
                    </div>
                </article>
            `).join('');

            menuGrid.querySelectorAll('[data-add-cart]').forEach((button) => {
                button.addEventListener('click', () => {
                    cartTotal += 1;

                    if (cartCount instanceof HTMLElement) {
                        cartCount.textContent = String(cartTotal);
                    }
                });
            });
        }

        if (Array.isArray(featuredFoods) && featuredFoods.length > 0) {
            const carousel = document.querySelector('[data-featured-carousel]');
            const imageNode = document.querySelector('[data-carousel-image]');
            let activeIndex = 0;
            let intervalId = null;

            const renderSlide = (index) => {
                if (!(carousel instanceof HTMLElement) || !(imageNode instanceof HTMLImageElement)) {
                    return;
                }

                const item = featuredFoods[index];

                carousel.classList.add('is-transitioning');

                window.setTimeout(() => {
                    imageNode.src = item.image;
                    imageNode.alt = item.name;
                    carousel.classList.remove('is-transitioning');
                }, 220);
            };

            const startCarousel = () => {
                if (intervalId !== null) {
                    window.clearInterval(intervalId);
                }

                intervalId = window.setInterval(() => {
                    activeIndex = (activeIndex + 1) % featuredFoods.length;
                    renderSlide(activeIndex);
                }, 4500);
            };

            renderSlide(activeIndex);
            startCarousel();
        }
    }
});
