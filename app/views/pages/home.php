<?php

declare(strict_types=1);

$featuredFoods = featuredFoods();
$featuredFoodsForView = $featuredFoods;

if ($featuredFoodsForView === []) {
    $featuredFoodsForView = [
        [
            'id' => 0,
            'name' => 'Signature Snapper',
            'description' => 'A crisp, golden fillet plated with fresh herbs and a bright citrus finish.',
            'image' => assetUrl('images/plate-1.png'),
            'rating' => 4.9,
        ],
        [
            'id' => 0,
            'name' => 'Pepper Roast Bites',
            'description' => 'Slow-roasted beef tossed in savory glaze, made for bold appetites.',
            'image' => assetUrl('images/plate-1.png'),
            'rating' => 4.8,
        ],
        [
            'id' => 0,
            'name' => 'Creamy Garden Pasta',
            'description' => 'Silky comfort pasta with a rich, velvety sauce and fresh seasonal garnish.',
            'image' => assetUrl('images/plate-1.png'),
            'rating' => 4.7,
        ],
    ];
}

$featuredFoodsJson = json_encode(
    array_map(
        static function (array $item): array {
            return [
                'id' => (int) ($item['id'] ?? 0),
                'name' => (string) $item['name'],
                'description' => (string) $item['description'],
                'image' => (string) $item['image'],
                'rating' => isset($item['rating']) ? (float) $item['rating'] : null,
            ];
        },
        $featuredFoodsForView
    ),
    JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT
);

$menuItems = [
    [
        'name' => 'Snapper Delight',
        'price' => 10.50,
        'description' => 'Crisp skin snapper with a light citrus glaze and roasted vegetables.',
        'image' => assetUrl('images/plate-1.png'),
        'category' => 'Mains',
    ],
    [
        'name' => 'Pepper Beef Bites',
        'price' => 12.25,
        'description' => 'Tender beef cubes tossed in a rich pepper sauce with caramelized onions.',
        'image' => assetUrl('images/plate-2.png'),
        'category' => 'Sides',
    ],
    [
        'name' => 'Creamy Garden Pasta',
        'price' => 9.95,
        'description' => 'Silky cream sauce, herbs, and seasonal vegetables folded into warm pasta.',
        'image' => assetUrl('images/plate-3.png'),
        'category' => 'Pasta',
    ],
    [
        'name' => 'Harvest Salad Bowl',
        'price' => 8.75,
        'description' => 'Fresh greens, roasted vegetables, and zesty dressing with crunchy toppings.',
        'image' => assetUrl('images/plate-1.png'),
        'category' => 'Salads',
    ],
    [
        'name' => 'Ham Sandwich Stack',
        'price' => 10.50,
        'description' => 'House-baked bread layered with smoked ham, cheese, and crisp lettuce.',
        'image' => assetUrl('images/plate-2.png'),
        'category' => 'Mains',
    ],
    [
        'name' => 'Golden Soup Bowl',
        'price' => 7.90,
        'description' => 'Comforting vegetable broth with pasta ribbons and herbs.',
        'image' => assetUrl('images/plate-3.png'),
        'category' => 'Sides',
    ],
];

$menuItemsJson = json_encode($menuItems, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);

$categoryCards = [
    ['label' => 'Mains', 'image' => assetUrl('images/plate-1.png')],
    ['label' => 'Sides', 'image' => assetUrl('images/plate-2.png')],
    ['label' => 'Pasta', 'image' => assetUrl('images/plate-3.png')],
    ['label' => 'Salads', 'image' => assetUrl('images/plate-1.png')],
    ['label' => 'Soups', 'image' => assetUrl('images/plate-3.png')],
    ['label' => 'Seafood', 'image' => assetUrl('images/plate-2.png')],
];
?>
<div
    class="restaurant-home"
    data-home-page
    data-featured-foods='<?= h($featuredFoodsJson ?: '[]') ?>'
    data-menu-items='<?= h($menuItemsJson ?: '[]') ?>'
>
    <section class="hero-restaurant" id="home">
        <div class="hero-copy-panel">
            <h1><span>Good</span> Food, Great <span>Mood</span></h1>
            <div class="hero-actions">
                <a class="button button-primary" href="#favorite-menu">View Menu</a>
            </div>
        </div>

        <div class="hero-visual-panel">
            <div class="hero-bg-dots" aria-hidden="true"></div>

            <?php if ($featuredFoodsForView !== []): ?>
                <?php $firstSlide = $featuredFoodsForView[0]; ?>
                <div class="featured-carousel-card" data-featured-carousel>
                    <div class="featured-carousel-copy">
                        <span class="featured-carousel-kicker">Featured Dish</span>
                        <h2 data-carousel-name><?= h((string) $firstSlide['name']) ?></h2>
                        <p data-carousel-description><?= h((string) $firstSlide['description']) ?></p>
                    </div>
                    <div class="featured-carousel-media">
                        <img
                            data-carousel-image
                            src="<?= h((string) $firstSlide['image']) ?>"
                            alt="<?= h((string) $firstSlide['name']) ?>"
                        >
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="category-section" id="menu">
        <div class="section-heading">
            <div>
                <h2>Order By Categories</h2>
            </div>
        </div>

        <div class="category-wrap">
            <button class="category-arrow category-arrow-left" type="button" data-category-prev aria-label="Scroll categories left">&#8592;</button>
            <div class="category-track" data-category-track>
                <?php foreach ($categoryCards as $category): ?>
                    <article class="category-card">
                        <img src="<?= h($category['image']) ?>" alt="<?= h($category['label']) ?>">
                        <span><?= h($category['label']) ?></span>
                    </article>
                <?php endforeach; ?>
            </div>
            <button class="category-arrow category-arrow-right" type="button" data-category-next aria-label="Scroll categories right">&#8594;</button>
        </div>
    </section>

    <section class="favorite-menu-section" id="favorite-menu">
        <div class="section-heading">
            <h2 class="favorite-title"><span class="favorite-icon" aria-hidden="true">🍴</span> Favorite <span>Menu</span></h2>
            <div class="menu-tools">
                <a class="text-link category-link" href="#favorite-menu">Explore Menu</a>
            </div>
        </div>

        <div class="menu-grid" data-menu-grid></div>
    </section>

    <div id="about" class="section-anchor" aria-hidden="true"></div>
    <div id="contact" class="section-anchor" aria-hidden="true"></div>
    <div id="profile" class="section-anchor" aria-hidden="true"></div>
    <div id="settings" class="section-anchor" aria-hidden="true"></div>
</div>
