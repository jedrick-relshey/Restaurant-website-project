<?php

declare(strict_types=1);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed.');
}

ensureGuest();

$email = strtolower(trim((string) ($_POST['email'] ?? '')));
$password = (string) ($_POST['password'] ?? '');

withOldInput(['login_email' => $email]);
flashValue('login_email_value', $email);

if ($email === '' || $password === '') {
    redirectToPage('auth', 'error', 'Email and password are required.');
}

if (!validateEmail($email)) {
    redirectToPage('auth', 'error', 'Please enter a valid email address.');
}

try {
    $user = userByEmail($email);
} catch (PDOException $exception) {
    if (isMissingUsersTable($exception)) {
        redirectToPage('auth', 'error', authDatabaseSetupMessage());
    }

    throw $exception;
}

if ($user === null) {
    flash('login_error', 'No account was found for that email address.');
    redirect(routeUrl('auth'));
}

if (!password_verify($password, (string) $user['password'])) {
    flash('login_error', 'Wrong password. Please try again.');
    redirect(routeUrl('auth'));
}

loginUser($user);
clearOldInput();
redirectToPage('dashboard', 'success', 'Welcome back.');
