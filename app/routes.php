<?php

declare(strict_types=1);

return [
    'auth' => [
        'view' => 'pages/auth',
        'title' => 'Login / Sign Up',
        'guest_only' => true,
    ],
    'home' => [
        'view' => 'pages/home',
        'title' => 'Home',
        'auth_only' => true,
    ],
    'forgot-password' => [
        'view' => 'pages/forgot-password',
        'title' => 'Forgot Password',
        'guest_only' => true,
    ],
    'reset-password' => [
        'view' => 'pages/reset-password',
        'title' => 'Reset Password',
        'guest_only' => true,
    ],
    'dashboard' => [
        'view' => 'pages/dashboard',
        'title' => 'Dashboard',
        'auth_only' => true,
    ],
];
