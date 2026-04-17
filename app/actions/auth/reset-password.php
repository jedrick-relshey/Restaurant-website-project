<?php

declare(strict_types=1);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed.');
}

ensureGuest();

$token = trim((string) ($_POST['token'] ?? ''));
$password = (string) ($_POST['password'] ?? '');
$passwordConfirmation = (string) ($_POST['password_confirmation'] ?? '');

if ($token === '') {
    redirectToPage('forgot-password', 'error', 'Reset token is missing or invalid.');
}

if ($password === '' || $passwordConfirmation === '') {
    redirect(routeUrl('reset-password') . '&token=' . urlencode($token) . '&error=' . urlencode('Enter your new password.'));
}

if (strlen($password) < 8) {
    redirect(routeUrl('reset-password') . '&token=' . urlencode($token) . '&error=' . urlencode('Password must be at least 8 characters.'));
}

if ($password !== $passwordConfirmation) {
    redirect(routeUrl('reset-password') . '&token=' . urlencode($token) . '&error=' . urlencode('Passwords do not match.'));
}

try {
    $resetRequest = passwordResetRequestByToken($token);
} catch (PDOException $exception) {
    redirectToPage('forgot-password', 'error', 'Password reset request could not be verified.');
}

if ($resetRequest === null) {
    redirectToPage('forgot-password', 'error', 'This reset link is invalid or has expired.');
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$statement = db()->prepare('UPDATE users SET password = :password WHERE email = :email');
$statement->execute([
    'password' => $hashedPassword,
    'email' => $resetRequest['email'],
]);

deletePasswordResetToken($token);

redirectToPage('auth', 'success', 'Password updated successfully. You can log in now.');
