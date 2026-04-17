<?php

declare(strict_types=1);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed.');
}

ensureGuest();

$email = strtolower(trim((string) ($_POST['email'] ?? '')));
$password = (string) ($_POST['password'] ?? '');
$passwordConfirmation = (string) ($_POST['password_confirmation'] ?? '');

withOldInput(['register_email' => $email]);

if ($email === '' || $password === '' || $passwordConfirmation === '') {
    redirectToPage('auth', 'error', 'Email, password, and confirmation are required.');
}

if (!validateEmail($email)) {
    redirectToPage('auth', 'error', 'Please enter a valid email address.');
}

if (strlen($password) < 8) {
    redirectToPage('auth', 'error', 'Password must be at least 8 characters long.');
}

if ($password !== $passwordConfirmation) {
    redirectToPage('auth', 'error', 'Password confirmation does not match.');
}

try {
    if (userByEmail($email) !== null) {
        redirectToPage('auth', 'error', 'This email is already registered.');
    }
} catch (PDOException $exception) {
    if (isMissingUsersTable($exception)) {
        redirectToPage('auth', 'error', authDatabaseSetupMessage());
    }

    throw $exception;
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

try {
    $statement = db()->prepare('INSERT INTO users (email, password) VALUES (:email, :password)');
    $statement->execute([
        'email' => $email,
        'password' => $hashedPassword,
    ]);

    $user = userByEmail($email);
} catch (PDOException $exception) {
    if (isMissingUsersTable($exception)) {
        redirectToPage('auth', 'error', authDatabaseSetupMessage());
    }

    throw $exception;
}

if ($user === null) {
    redirectToPage('auth', 'error', 'Account created, but automatic login failed. Please log in.');
}

loginUser($user);
clearOldInput();
redirectToPage('dashboard', 'success', 'Account created successfully.');
