<section class="auth-layout auth-layout-single">
    <div class="auth-column auth-column-single">
        <div class="auth-panel-wrap">
            <?php if ($successMessage !== null): ?>
                <div class="alert alert-success"><?= h($successMessage) ?></div>
            <?php endif; ?>

            <?php if ($errorMessage !== null): ?>
                <div class="alert alert-error"><?= h($errorMessage) ?></div>
            <?php endif; ?>

            <?php if ($forgotNotice !== null): ?>
                <div class="alert alert-success"><?= h($forgotNotice) ?></div>
            <?php endif; ?>

            <section class="auth-card is-active">
                <div class="card-head">
                    <h2>Forgot Password</h2>
                    <p>Enter your account email to generate a reset link.</p>
                </div>

                <form method="POST" action="<?= h(actionUrl('forgot-password')) ?>" class="auth-form" autocomplete="off">
                    <label>
                        <span>Email Address</span>
                        <div class="input-shell">
                            <input type="email" name="email" value="" placeholder="Enter your Email" required autocomplete="off" autocapitalize="off" autocorrect="off" spellcheck="false">
                        </div>
                    </label>

                    <button class="button button-primary" type="submit">Send Reset Link</button>
                    <a class="forgot-link" href="<?= h(routeUrl('auth')) ?>">Back to Sign In</a>
                </form>

                <?php if (is_string($forgotLink) && $forgotLink !== ''): ?>
                    <div class="reset-link-box">
                        <span>Reset Link</span>
                        <a href="<?= h($forgotLink) ?>"><?= h($forgotLink) ?></a>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </div>
</section>
