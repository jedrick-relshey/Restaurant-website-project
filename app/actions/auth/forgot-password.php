<?php

declare(strict_types=1);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed.');
}

ensureGuest();

$email = strtolower(trim((string) ($_POST['email'] ?? '')));

if ($email === '' || !validateEmail($email)) {
    redirectToPage('forgot-password', 'error', 'Enter a valid email address.');
}

try {
    $user = userByEmail($email);
} catch (PDOException $exception) {
    if (isMissingUsersTable($exception)) {
        redirectToPage('forgot-password', 'error', authDatabaseSetupMessage());
    }

    throw $exception;
}

if ($user === null) {
    redirectToPage('forgot-password', 'error', 'No account was found for that email address.');
}

try {
    $token = storePasswordResetToken($email);
} catch (PDOException $exception) {
    redirectToPage('forgot-password', 'error', 'Password reset could not be created right now.');
}

$resetLink = routeUrl('reset-password') . '&token=' . urlencode($token);
flash('success', 'Password reset link created successfully.');
flash('forgot_notice', 'Open the reset link below to set a new password.');
flashValue('password_reset_link', $resetLink);

redirect(routeUrl('forgot-password'));
