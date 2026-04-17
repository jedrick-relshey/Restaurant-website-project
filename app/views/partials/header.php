<header class="site-header">
    <?php if (isAuthenticated()): ?>
        <nav class="site-nav site-nav-auth">
            <span class="nav-user"><?= h($currentUser['email'] ?? '') ?></span>
            <a class="nav-link nav-link-accent" href="<?= h(actionUrl('logout')) ?>">Logout</a>
        </nav>
    <?php endif; ?>
</header>
