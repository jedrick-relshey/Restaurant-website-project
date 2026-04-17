<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/bootstrap.php';
require_once dirname(__DIR__) . '/app/support/helpers.php';

$action = isset($_GET['action']) ? strtolower(trim((string) $_GET['action'])) : null;

if ($action !== null) {
    $actionMap = [
        'login' => dirname(__DIR__) . '/app/actions/auth/login.php',
        'register' => dirname(__DIR__) . '/app/actions/auth/register.php',
        'logout' => dirname(__DIR__) . '/app/actions/auth/logout.php',
        'forgot-password' => dirname(__DIR__) . '/app/actions/auth/forgot-password.php',
        'reset-password' => dirname(__DIR__) . '/app/actions/auth/reset-password.php',
    ];

    if (!isset($actionMap[$action])) {
        http_response_code(404);
        exit('Action not found.');
    }

    require $actionMap[$action];
    exit;
}

$page = isset($_GET['page']) ? strtolower(trim((string) $_GET['page'])) : 'auth';
$route = routes()[$page] ?? null;

if ($route === null) {
    http_response_code(404);
    render('pages/404', [
        'pageTitle' => 'Page Not Found | ' . appConfig()['name'],
        'currentPage' => '404',
    ]);
    exit;
}

if (($route['guest_only'] ?? false) === true) {
    ensureGuest();
}

if (($route['auth_only'] ?? false) === true) {
    ensureAuthenticated();
}

render($route['view'], [
    'pageTitle' => $route['title'] . ' | ' . appConfig()['name'],
    'currentPage' => $page,
]);
