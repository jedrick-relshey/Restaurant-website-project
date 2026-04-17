<?php $token = trim((string) ($_GET['token'] ?? '')); ?>
<?php $pageError = trim((string) ($_GET['error'] ?? '')); ?>

<section class="auth-layout auth-layout-single">
    <div class="auth-column auth-column-single">
        <div class="auth-panel-wrap">
            <?php if ($errorMessage !== null): ?>
                <div class="alert alert-error"><?= h($errorMessage) ?></div>
            <?php endif; ?>

            <?php if ($pageError !== ''): ?>
                <div class="alert alert-error"><?= h($pageError) ?></div>
            <?php endif; ?>

            <section class="auth-card is-active">
                <div class="card-head">
                    <h2>Reset Password</h2>
                    <p>Create a new password for your account.</p>
                </div>

                <form method="POST" action="<?= h(actionUrl('reset-password')) ?>" class="auth-form" autocomplete="off">
                    <input type="hidden" name="token" value="<?= h($token) ?>">

                    <label>
                        <span>New Password</span>
                        <div class="password-field input-shell">
                            <input type="password" name="password" placeholder="At least 8 characters" required autocomplete="new-password" autocapitalize="off" autocorrect="off" spellcheck="false">
                            <button class="password-toggle" type="button" data-toggle-password aria-label="Show password">&#128065;</button>
                        </div>
                    </label>

                    <label>
                        <span>Confirm Password</span>
                        <div class="password-field input-shell">
                            <input type="password" name="password_confirmation" placeholder="Repeat your password" required autocomplete="new-password" autocapitalize="off" autocorrect="off" spellcheck="false">
                            <button class="password-toggle" type="button" data-toggle-password aria-label="Show password">&#128065;</button>
                        </div>
                    </label>

                    <button class="button button-primary" type="submit">Update Password</button>
                    <a class="forgot-link" href="<?= h(routeUrl('auth')) ?>">Back to Sign In</a>
                </form>
            </section>
        </div>
    </div>
</section>
