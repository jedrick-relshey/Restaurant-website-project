<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/config/bootstrap.php';
require_once dirname(__DIR__, 2) . '/app/support/helpers.php';

ensureAuthenticated();

$allowedMimeTypes = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
];

$uploadDirectory = publicPath('uploads/featured-foods');

if (!is_dir($uploadDirectory)) {
    mkdir($uploadDirectory, 0775, true);
}

$tableReady = featuredFoodsTableExists();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$tableReady) {
        flash('error', 'The featured_foods table is missing. Import database/restaurant.sql first.');
        redirect('featured-foods.php');
    }

    $intent = strtolower(trim((string) ($_POST['intent'] ?? '')));

    if ($intent === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        $record = featuredFoodById($id);

        if ($record === null) {
            flash('error', 'Featured food not found.');
            redirect('featured-foods.php');
        }

        $statement = db()->prepare('DELETE FROM featured_foods WHERE id = :id');
        $statement->execute(['id' => $id]);

        $imagePath = publicPath((string) $record['image']);

        if (is_file($imagePath) && str_contains(str_replace('\\', '/', $imagePath), '/uploads/featured-foods/')) {
            unlink($imagePath);
        }

        flash('success', 'Featured food deleted successfully.');
        redirect('featured-foods.php');
    }

    if ($intent === 'save') {
        $id = (int) ($_POST['id'] ?? 0);
        $name = trim((string) ($_POST['name'] ?? ''));
        $description = trim((string) ($_POST['description'] ?? ''));
        $ratingRaw = trim((string) ($_POST['rating'] ?? ''));
        $existingImage = trim((string) ($_POST['existing_image'] ?? ''));
        $errors = [];

        if ($name === '') {
            $errors[] = 'Food name is required.';
        }

        if ($description === '') {
            $errors[] = 'Description is required.';
        }

        $rating = null;

        if ($ratingRaw !== '') {
            if (!is_numeric($ratingRaw)) {
                $errors[] = 'Rating must be a number.';
            } else {
                $rating = round((float) $ratingRaw, 1);

                if ($rating < 0 || $rating > 5) {
                    $errors[] = 'Rating must be between 0 and 5.';
                }
            }
        }

        $uploadedFile = $_FILES['image'] ?? null;
        $hasNewImage = is_array($uploadedFile) && (int) ($uploadedFile['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;
        $imagePath = $existingImage;

        if ($hasNewImage) {
            $uploadError = (int) ($uploadedFile['error'] ?? UPLOAD_ERR_OK);

            if ($uploadError !== UPLOAD_ERR_OK) {
                $errors[] = 'Image upload failed. Please try again.';
            } else {
                $tmpName = (string) ($uploadedFile['tmp_name'] ?? '');
                $originalName = (string) ($uploadedFile['name'] ?? '');
                $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mimeType = $finfo !== false ? (string) finfo_file($finfo, $tmpName) : '';

                if ($finfo !== false) {
                    finfo_close($finfo);
                }

                if (!isset($allowedMimeTypes[$mimeType]) || !in_array($extension, ['jpg', 'jpeg', 'png'], true)) {
                    $errors[] = 'Only JPG, JPEG, and PNG images are allowed.';
                } else {
                    $generatedName = sprintf('featured-%s.%s', bin2hex(random_bytes(12)), $allowedMimeTypes[$mimeType]);
                    $targetPath = $uploadDirectory . DIRECTORY_SEPARATOR . $generatedName;

                    if (!move_uploaded_file($tmpName, $targetPath)) {
                        $errors[] = 'Unable to save the uploaded image.';
                    } else {
                        $imagePath = 'uploads/featured-foods/' . $generatedName;
                    }
                }
            }
        } elseif ($id === 0) {
            $errors[] = 'Image is required for a new featured food.';
        }

        if ($errors !== []) {
            if ($hasNewImage && $imagePath !== '' && $imagePath !== $existingImage) {
                $savedPath = publicPath($imagePath);

                if (is_file($savedPath)) {
                    unlink($savedPath);
                }
            }

            flashValue('featured_food_errors', $errors);
            flashValue('featured_food_old', [
                'id' => $id,
                'name' => $name,
                'description' => $description,
                'rating' => $ratingRaw,
                'image' => $existingImage,
            ]);

            $redirectUrl = 'featured-foods.php';

            if ($id > 0) {
                $redirectUrl .= '?edit=' . $id;
            }

            redirect($redirectUrl);
        }

        if ($id > 0) {
            $record = featuredFoodById($id);

            if ($record === null) {
                flash('error', 'Featured food not found.');
                redirect('featured-foods.php');
            }

            $statement = db()->prepare(
                'UPDATE featured_foods SET name = :name, description = :description, image = :image, rating = :rating WHERE id = :id'
            );
            $statement->bindValue(':id', $id, PDO::PARAM_INT);
            $statement->bindValue(':name', $name);
            $statement->bindValue(':description', $description);
            $statement->bindValue(':image', $imagePath);
            $statement->bindValue(':rating', $rating, $rating === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $statement->execute();

            if ($hasNewImage && $existingImage !== '') {
                $oldImagePath = publicPath($existingImage);

                if (is_file($oldImagePath) && str_contains(str_replace('\\', '/', $oldImagePath), '/uploads/featured-foods/')) {
                    unlink($oldImagePath);
                }
            }

            flash('success', 'Featured food updated successfully.');
            redirect('featured-foods.php');
        }

        $statement = db()->prepare(
            'INSERT INTO featured_foods (name, description, image, rating) VALUES (:name, :description, :image, :rating)'
        );
        $statement->bindValue(':name', $name);
        $statement->bindValue(':description', $description);
        $statement->bindValue(':image', $imagePath);
        $statement->bindValue(':rating', $rating, $rating === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $statement->execute();

        flash('success', 'Featured food added successfully.');
        redirect('featured-foods.php');
    }
}

$records = featuredFoods();
$editId = (int) ($_GET['edit'] ?? 0);
$editingRecord = $editId > 0 ? featuredFoodById($editId) : null;
$oldForm = getFlashValue('featured_food_old');
$formErrors = getFlashValue('featured_food_errors');
$successMessage = getFlash('success');
$errorMessage = getFlash('error');

$formData = [
    'id' => (int) ($oldForm['id'] ?? $editingRecord['id'] ?? 0),
    'name' => (string) ($oldForm['name'] ?? $editingRecord['name'] ?? ''),
    'description' => (string) ($oldForm['description'] ?? $editingRecord['description'] ?? ''),
    'rating' => (string) ($oldForm['rating'] ?? ($editingRecord['rating'] ?? '')),
    'image' => (string) ($oldForm['image'] ?? $editingRecord['image'] ?? ''),
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Featured Foods CMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/mobile.css">
</head>
<body class="page-admin-featured-foods">
    <main class="admin-page-shell">
        <section class="admin-topbar">
            <div>
                <p class="admin-kicker">Piggies CMS</p>
                <h1>Featured Foods Manager</h1>
                <p>Manage hero carousel items for the homepage in one lightweight admin screen.</p>
            </div>
            <div class="admin-topbar-actions">
                <a class="button button-ghost" href="../index.php?page=home">Back to Home</a>
                <a class="button button-primary" href="../index.php?action=logout">Logout</a>
            </div>
        </section>

        <?php if (is_array($formErrors) && $formErrors !== []): ?>
            <div class="alert alert-error">
                <?= h(implode(' ', $formErrors)) ?>
            </div>
        <?php endif; ?>

        <?php if ($successMessage !== null): ?>
            <div class="alert alert-success"><?= h($successMessage) ?></div>
        <?php endif; ?>

        <?php if ($errorMessage !== null): ?>
            <div class="alert alert-error"><?= h($errorMessage) ?></div>
        <?php endif; ?>

        <?php if (!$tableReady): ?>
            <div class="alert alert-error">
                The <code>featured_foods</code> table does not exist yet. Import
                <code>database/restaurant.sql</code> in phpMyAdmin, then refresh this page.
            </div>
        <?php endif; ?>

        <section class="featured-cms-layout">
            <article class="featured-cms-card">
                <div class="section-heading">
                    <div>
                        <p class="section-kicker"><?= $formData['id'] > 0 ? 'Edit entry' : 'Create new' ?></p>
                        <h2><?= $formData['id'] > 0 ? 'Update featured food' : 'Add featured food' ?></h2>
                    </div>
                    <?php if ($formData['id'] > 0): ?>
                        <a class="text-link" href="featured-foods.php">Clear form</a>
                    <?php endif; ?>
                </div>

                <form class="featured-cms-form" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="intent" value="save">
                    <input type="hidden" name="id" value="<?= h((string) $formData['id']) ?>">
                    <input type="hidden" name="existing_image" value="<?= h($formData['image']) ?>">

                    <label>
                        <span>Food name</span>
                        <div class="input-shell">
                            <input type="text" name="name" value="<?= h($formData['name']) ?>" placeholder="Ex. Citrus Glazed Salmon" required>
                        </div>
                    </label>

                    <label>
                        <span>Description</span>
                        <div class="textarea-shell">
                            <textarea name="description" rows="5" placeholder="Short hero description for the homepage." required><?= h($formData['description']) ?></textarea>
                        </div>
                    </label>

                    <label>
                        <span>Rating (optional)</span>
                        <div class="input-shell">
                            <input type="number" name="rating" value="<?= h($formData['rating']) ?>" min="0" max="5" step="0.1" placeholder="4.8">
                        </div>
                    </label>

                    <label>
                        <span>Upload image</span>
                        <div class="file-shell">
                            <input type="file" name="image" accept=".jpg,.jpeg,.png,image/jpeg,image/png">
                        </div>
                    </label>

                    <?php if ($formData['image'] !== ''): ?>
                        <div class="featured-preview">
                            <img src="../<?= h($formData['image']) ?>" alt="<?= h($formData['name'] ?: 'Featured food preview') ?>">
                            <div>
                                <strong>Current image</strong>
                                <p><?= h($formData['image']) ?></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <button class="button button-primary" type="submit" <?= !$tableReady ? 'disabled' : '' ?>>
                        <?= $formData['id'] > 0 ? 'Update Featured Food' : 'Publish Featured Food' ?>
                    </button>
                </form>
            </article>

            <article class="featured-cms-card">
                <div class="section-heading">
                    <div>
                        <p class="section-kicker">All entries</p>
                        <h2>Featured food library</h2>
                    </div>
                    <span class="pill"><?= count($records) ?> items</span>
                </div>

                <div class="featured-cms-list">
                    <?php if ($records === []): ?>
                        <div class="empty-state-card">
                            <h3>No featured foods yet</h3>
                            <p>Add your first item to power the homepage carousel.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($records as $record): ?>
                            <article class="featured-food-row">
                                <img src="../<?= h((string) $record['image']) ?>" alt="<?= h((string) $record['name']) ?>">
                                <div class="featured-food-row-copy">
                                    <div class="featured-food-row-head">
                                        <h3><?= h((string) $record['name']) ?></h3>
                                        <?php if ($record['rating'] !== null): ?>
                                            <span class="rating-badge"><?= h(number_format((float) $record['rating'], 1)) ?> / 5</span>
                                        <?php endif; ?>
                                    </div>
                                    <p><?= h((string) $record['description']) ?></p>
                                    <small>Added <?= h((string) date('M d, Y', strtotime((string) $record['created_at']))) ?></small>
                                </div>
                                <div class="featured-food-row-actions">
                                    <a class="button button-ghost" href="featured-foods.php?edit=<?= h((string) $record['id']) ?>">Edit</a>
                                    <form method="post" onsubmit="return confirm('Delete this featured food?');">
                                        <input type="hidden" name="intent" value="delete">
                                        <input type="hidden" name="id" value="<?= h((string) $record['id']) ?>">
                                        <button class="button button-danger" type="submit">Delete</button>
                                    </form>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </article>
        </section>
    </main>
</body>
</html>
