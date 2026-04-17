<section class="auth-layout">
    <section class="showcase-panel">
        <div class="showcase-copy">
            <h2>&#10022; Reserve Your Table &#10022;</h2>
            <p>Book tables faster when you sign in. Enjoy delicious moments with seamless reservations.</p>
        </div>
        <div class="showcase-image-card">
            <img src="<?= h(assetUrl('images/login-image.jpg')) ?>" alt="Restaurant plated dish">
        </div>
    </section>

    <div class="auth-column">
        <div class="auth-panel-wrap">
        <?php if ($successMessage !== null): ?>
            <div class="alert alert-success"><?= h($successMessage) ?></div>
        <?php endif; ?>

        <?php if ($errorMessage !== null): ?>
            <div class="alert alert-error"><?= h($errorMessage) ?></div>
        <?php endif; ?>

        <?php if ($loginError !== null): ?>
            <div class="alert alert-error"><?= h($loginError) ?></div>
        <?php endif; ?>

        <div class="auth-tabs" role="tablist" aria-label="Authentication forms">
            <button class="auth-tab is-active" type="button" data-auth-tab="login" role="tab" aria-selected="true">Sign In</button>
            <button class="auth-tab" type="button" data-auth-tab="signup" role="tab" aria-selected="false">Sign Up</button>
        </div>

        <section class="auth-card is-active" id="login-panel" data-auth-panel="login">
            <form method="POST" action="<?= h(actionUrl('login')) ?>" class="auth-form" data-validate autocomplete="off">
                <label>
                    <span>Email Address</span>
                    <div class="input-shell">
                        <input type="email" name="email" value="" placeholder="Enter your Email" required autocomplete="off" autocapitalize="off" autocorrect="off" spellcheck="false">
                    </div>
                    <small class="field-error" data-error-for="email"></small>
                </label>

                <label>
                    <span>Password</span>
                    <div class="password-field input-shell">
                        <input type="password" name="password" placeholder="Enter your password" required autocomplete="new-password" autocapitalize="off" autocorrect="off" spellcheck="false">
                        <button class="password-toggle" type="button" data-toggle-password aria-label="Show password">&#128065;</button>
                    </div>
                    <small class="field-error" data-error-for="password"></small>
                </label>

                <div class="auth-options">
                    <label class="remember-row">
                        <input type="checkbox" name="remember">
                        <span>Remember me</span>
                    </label>
                    <a class="forgot-link" href="<?= h(routeUrl('forgot-password')) ?>">Forgot Password?</a>
                </div>

                <button class="button button-primary" type="submit">Log in</button>
            </form>

            <div class="divider"><span>or continue with</span></div>
            <div class="social-grid">
                <button class="button button-social" type="button" data-google-placeholder>Google</button>
                <button class="button button-social" type="button" data-facebook-placeholder>Facebook</button>
            </div>
            <small class="field-note" data-google-message></small>
        </section>

        <section class="auth-card" id="signup-panel" data-auth-panel="signup">
            <form method="POST" action="<?= h(actionUrl('register')) ?>" class="auth-form" data-validate autocomplete="off">
                <label>
                    <span>Email Address</span>
                    <div class="input-shell">
                        <input type="email" name="email" value="" placeholder="Email" required autocomplete="off" autocapitalize="off" autocorrect="off" spellcheck="false">
                    </div>
                    <small class="field-error" data-error-for="email"></small>
                </label>

                <label>
                    <span>Password</span>
                    <div class="password-field input-shell">
                        <input type="password" name="password" placeholder="At least 8 characters" minlength="8" required autocomplete="new-password" autocapitalize="off" autocorrect="off" spellcheck="false">
                        <button class="password-toggle" type="button" data-toggle-password aria-label="Show password">&#128065;</button>
                    </div>
                    <small class="field-error" data-error-for="password"></small>
                </label>

                <label>
                    <span>Confirm password</span>
                    <div class="password-field input-shell">
                        <input type="password" name="password_confirmation" placeholder="Repeat your password" minlength="8" required autocomplete="new-password" autocapitalize="off" autocorrect="off" spellcheck="false">
                        <button class="password-toggle" type="button" data-toggle-password aria-label="Show password">&#128065;</button>
                    </div>
                    <small class="field-error" data-error-for="password_confirmation"></small>
                </label>

                <button class="button button-primary" type="submit">Create Account</button>
            </form>
        </section>
        </div>
    </div>
</section>
