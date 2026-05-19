<?php

declare(strict_types=1);

$featuredFoods = featuredFoods();
$featuredFoodsForView = $featuredFoods;

if ($featuredFoodsForView === []) {
    $featuredFoodsForView = [
        [
            'id' => 0,
            'name' => 'lorem ipsum dolor sit amet',
            'description' => 'lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
            'image' => assetUrl('images/plate-1.png'),
            'rating' => 4.9,
        ],
        [
            'id' => 0,
            'name' => 'lorem ipsum dolor sit amet',
            'description' => 'lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
            'image' => assetUrl('images/plate-1.png'),
            'rating' => 4.8,
        ],
        [
            'id' => 0,
            'name' => 'lorem ipsum dolor sit amet',
            'description' => 'lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua',
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
        'name' => 'lorem ipsum',
        'price' => 10.50,
        'description' => 'lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
        'image' => assetUrl('images/food-1.jpg'),
        'category' => 'Mains',
    ],
    [
        'name' => 'lorem ipsum',
        'price' => 12.25,
        'description' => 'lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
        'image' => assetUrl('images/food-2.jpg'),
        'category' => 'Sides',
    ],
    [
        'name' => 'lorem ipsum',
        'price' => 9.95,
        'description' => 'lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
        'image' => assetUrl('images/food-3.jpg'),
        'category' => 'Pasta',
    ],
    [
        'name' => 'lorem ipsum',
        'price' => 8.75,
        'description' => 'lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
        'image' => assetUrl('images/food-4.jpg'),
        'category' => 'Salads',
    ],
    [
        'name' => 'lorem ipsum',
        'price' => 10.50,
        'description' => 'lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
        'image' => assetUrl('images/food-1.jpg'),
        'category' => 'Mains',
    ],
    [
        'name' => 'lorem ipsum',
        'price' => 7.90,
        'description' => 'lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
        'image' => assetUrl('images/food-2.jpg'),
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
    <section class="featured-carousel-section" id="featured">
        <div class="featured-carousel" data-featured-carousel>
            <img 
                class="featured-carousel-image" 
                data-carousel-image 
                src="<?= h($featuredFoodsForView[0]['image'] ?? '') ?>" 
                alt="<?= h($featuredFoodsForView[0]['name'] ?? '') ?>"
            >
            <div class="featured-carousel-content">
                <h2 data-carousel-name><?= h($featuredFoodsForView[0]['name'] ?? '') ?></h2>
                <p data-carousel-description><?= h($featuredFoodsForView[0]['description'] ?? '') ?></p>
            </div>
        </div>
    </section>

    <section class="category-section" id="menu">
        <div class="section-heading">
            <div>
                <h2>Categories</h2>
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