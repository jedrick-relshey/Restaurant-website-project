<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($pageTitle ?? appConfig()['name']) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= h(assetUrl('css/style.css')) ?>">
    <link rel="stylesheet" href="<?= h(assetUrl('css/mobile.css')) ?>">
</head>
<body class="page-<?= h($currentPage ?? 'default') ?>">
    <?php require dirname(__DIR__) . '/partials/header.php'; ?>
    <main class="page-shell">
        <?php require $viewPath; ?>
    </main>
    <?php require dirname(__DIR__) . '/partials/footer.php'; ?>
    <script src="<?= h(assetUrl('js/main.js')) ?>" defer></script>
    <script src="<?= h(assetUrl('js/validation.js')) ?>" defer></script>
</body>
</html>
