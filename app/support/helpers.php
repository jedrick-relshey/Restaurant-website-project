<?php

declare(strict_types=1);

function appConfig(): array
{
    static $config;

    if ($config === null) {
        $config = require dirname(__DIR__, 2) . '/config/app.php';
    }

    return $config;
}

function dbConfig(): array
{
    static $config;

    if ($config === null) {
        $config = require dirname(__DIR__, 2) . '/config/database.php';
    }

    return $config;
}

function db(): PDO
{
    static $pdo;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $config = dbConfig();
    $dsn = sprintf(
        '%s:host=%s;port=%s;dbname=%s;charset=%s',
        $config['driver'],
        $config['host'],
        $config['port'],
        $config['database'],
        $config['charset']
    );

    try {
        $pdo = new PDO($dsn, $config['username'], $config['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    } catch (PDOException $exception) {
        throw new RuntimeException('Database connection failed: ' . $exception->getMessage(), 0, $exception);
    }

    return $pdo;
}

function routes(): array
{
    static $routes;

    if ($routes === null) {
        $routes = require dirname(__DIR__) . '/routes.php';
    }

    return $routes;
}

function routeUrl(string $page = 'home'): string
{
    return 'index.php?page=' . urlencode($page);
}

function actionUrl(string $action): string
{
    return 'index.php?action=' . urlencode($action);
}

function assetUrl(string $path): string
{
    return 'assets/' . ltrim($path, '/');
}

function currentUser(): ?array
{
    return $_SESSION['user'] ?? null;
}

function isAuthenticated(): bool
{
    return currentUser() !== null;
}

function loginUser(array $user): void
{
    session_regenerate_id(true);
    $_SESSION['user'] = [
        'id' => (int) $user['id'],
        'email' => (string) $user['email'],
        'created_at' => (string) ($user['created_at'] ?? ''),
    ];
}

function logoutUser(): void
{
    unset($_SESSION['user']);
    session_regenerate_id(true);
}

function flash(string $key, string $message): void
{
    $_SESSION['flash'][$key] = $message;
}

function flashValue(string $key, mixed $value): void
{
    $_SESSION['flash'][$key] = $value;
}

function getFlash(string $key): ?string
{
    if (!isset($_SESSION['flash'][$key])) {
        return null;
    }

    $message = $_SESSION['flash'][$key];
    unset($_SESSION['flash'][$key]);

    return is_string($message) ? $message : null;
}

function getFlashValue(string $key): mixed
{
    if (!isset($_SESSION['flash'][$key])) {
        return null;
    }

    $value = $_SESSION['flash'][$key];
    unset($_SESSION['flash'][$key]);

    return $value;
}

function withOldInput(array $input): void
{
    $_SESSION['old'] = $input;
}

function old(string $key, string $default = ''): string
{
    if (!isset($_SESSION['old'][$key])) {
        return $default;
    }

    $value = $_SESSION['old'][$key];

    return is_string($value) ? $value : $default;
}

function clearOldInput(): void
{
    unset($_SESSION['old']);
}

function redirect(string $url): never
{
    header('Location: ' . $url);
    exit;
}

function redirectToPage(string $page, ?string $status = null, ?string $message = null): never
{
    if ($status !== null && $message !== null) {
        flash($status, $message);
    }

    redirect(routeUrl($page));
}

function ensureGuest(): void
{
    if (isAuthenticated()) {
        redirect(routeUrl('dashboard'));
    }
}

function ensureAuthenticated(): void
{
    if (!isAuthenticated()) {
        redirectToPage('auth', 'error', 'Please log in first.');
    }
}

function h(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function render(string $view, array $data = []): void
{
    $currentUser = currentUser();
    $successMessage = getFlash('success');
    $errorMessage = getFlash('error');
    $loginError = getFlash('login_error');
    $loginEmailValue = getFlash('login_email_value');
    $forgotLink = getFlash('password_reset_link');
    $forgotNotice = getFlash('forgot_notice');
    $viewPath = dirname(__DIR__) . '/views/' . str_replace('.', '/', $view) . '.php';

    extract($data, EXTR_SKIP);

    require dirname(__DIR__) . '/views/layouts/app.php';

    clearOldInput();
}

function validateEmail(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function isMissingUsersTable(PDOException $exception): bool
{
    return $exception->getCode() === '42S02'
        || str_contains(strtolower($exception->getMessage()), 'users');
}

function authDatabaseSetupMessage(): string
{
    return 'Database setup incomplete. Import database/restaurant.sql into phpMyAdmin first.';
}

function randomToken(int $bytes = 32): string
{
    return bin2hex(random_bytes($bytes));
}

function passwordResetTokenHash(string $token): string
{
    return hash('sha256', $token);
}

function storePasswordResetToken(string $email): string
{
    $token = randomToken();
    $tokenHash = passwordResetTokenHash($token);
    $expiresAt = (new DateTimeImmutable('+1 hour'))->format('Y-m-d H:i:s');

    $deleteStatement = db()->prepare('DELETE FROM password_resets WHERE email = :email');
    $deleteStatement->execute(['email' => $email]);

    $insertStatement = db()->prepare(
        'INSERT INTO password_resets (email, token_hash, expires_at) VALUES (:email, :token_hash, :expires_at)'
    );
    $insertStatement->execute([
        'email' => $email,
        'token_hash' => $tokenHash,
        'expires_at' => $expiresAt,
    ]);

    return $token;
}

function passwordResetRequestByToken(string $token): ?array
{
    $statement = db()->prepare(
        'SELECT email, token_hash, expires_at, created_at FROM password_resets WHERE token_hash = :token_hash LIMIT 1'
    );
    $statement->execute(['token_hash' => passwordResetTokenHash($token)]);
    $record = $statement->fetch();

    if ($record === false) {
        return null;
    }

    if (strtotime((string) $record['expires_at']) < time()) {
        $deleteStatement = db()->prepare('DELETE FROM password_resets WHERE token_hash = :token_hash');
        $deleteStatement->execute(['token_hash' => passwordResetTokenHash($token)]);

        return null;
    }

    return $record;
}

function deletePasswordResetToken(string $token): void
{
    $statement = db()->prepare('DELETE FROM password_resets WHERE token_hash = :token_hash');
    $statement->execute(['token_hash' => passwordResetTokenHash($token)]);
}

function userByEmail(string $email): ?array
{
    $statement = db()->prepare('SELECT id, email, password, created_at FROM users WHERE email = :email LIMIT 1');
    $statement->execute(['email' => $email]);
    $user = $statement->fetch();

    return $user === false ? null : $user;
}
