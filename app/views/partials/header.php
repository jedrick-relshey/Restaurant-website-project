<header class="site-header">
    <?php if (($currentPage ?? '') === 'home' && isAuthenticated()): ?>
        <div class="restaurant-nav-shell">
            <nav class="restaurant-nav restaurant-nav-left" aria-label="Primary">
                <a class="is-active" href="#home">Home</a>
                <a href="#menu">Menu</a>
                <a href="#delivery">Delivery/Pickup</a>
                <a href="#about">About Us</a>
                <a href="#contact">Contact Us</a>
            </nav>

            <a class="brand-mark brand-mark-centered" href="<?= h(routeUrl('home')) ?>">
                <img src="<?= h(assetUrl('images/logo.png')) ?>" alt="Piggies logo">
            </a>

            <button
                class="nav-toggle"
                type="button"
                data-nav-toggle
                aria-expanded="false"
                aria-controls="restaurant-nav"
                aria-label="Toggle navigation"
            >
                <span></span>
                <span></span>
                <span></span>
            </button>

            <div class="restaurant-nav-right">
                <div class="profile-menu" data-profile-menu>
                    <button class="profile-trigger" type="button" data-profile-toggle aria-expanded="false">
                        <span class="profile-avatar profile-avatar-icon" aria-hidden="true">
                            <span class="profile-avatar-head"></span>
                            <span class="profile-avatar-body"></span>
                        </span>
                    </button>
                    <div class="profile-dropdown" data-profile-dropdown>
                        <a href="#profile">Profile</a>
                        <a href="#settings">Settings</a>
                        <a href="<?= h(actionUrl('logout')) ?>">Logout</a>
                    </div>
                </div>
            </div>

            <nav class="restaurant-nav restaurant-nav-mobile" id="restaurant-nav" data-nav-menu>
                <a href="#home">Home</a>
                <a href="#menu">Menu</a>
                <a href="#delivery">Delivery/Pickup</a>
                <a href="#about">About Us</a>
                <a href="#contact">Contact Us</a>
                <a href="#profile">Profile</a>
                <a href="#settings">Settings</a>
                <a href="<?= h(actionUrl('logout')) ?>">Logout</a>
            </nav>
        </div>
    <?php elseif (isAuthenticated()): ?>
        <nav class="site-nav site-nav-auth">
            <span class="nav-user"><?= h($currentUser['email'] ?? '') ?></span>
            <a class="nav-link nav-link-accent" href="<?= h(actionUrl('logout')) ?>">Logout</a>
        </nav>
    <?php endif; ?>
</header>
