<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

return [
    'name' => env('APP_NAME', 'Restaurant Website'),
    'env' => env('APP_ENV', 'local'),
    'debug' => env('APP_DEBUG', 'true') === 'true',
    'url' => rtrim(env('APP_URL', 'http://localhost/Restaurant-website-project/public'), '/'),
];
