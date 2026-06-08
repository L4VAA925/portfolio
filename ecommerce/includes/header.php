<?php
$pageTitle = $pageTitle ?? 'E-Trgovina';
$bodyClass = $bodyClass ?? '';
$cartCount = isLoggedIn() && !isAdmin() ? getCartCount(currentUserId()) : 0;
$searchQuery = trim($_GET['search'] ?? '');

$navCategories = getDB()->query(
    'SELECT c.id, c.name, COUNT(p.id) AS product_count
     FROM categories c
     LEFT JOIN products p ON c.id = p.category_id
     GROUP BY c.id
     ORDER BY c.name'
)->fetchAll();

$activeCategoryId = isset($_GET['category']) ? (int) $_GET['category'] : 0;
?>
<!DOCTYPE html>
<html lang="bs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> | E-Trgovina</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body class="<?= e($bodyClass) ?>">

<div class="overlay" data-overlay></div>

<header>
    <div class="header-top">
        <div class="container">
            <p class="header-alert-news">
                <b>Besplatna dostava</b> — za narudžbe iznad 50 KM
            </p>
        </div>
    </div>

    <div class="header-main">
        <div class="container">
            <a href="<?= BASE_URL ?>/index.php" class="header-logo">
                <span class="logo-text">E-Trgovina</span>
            </a>

            <form method="get" action="<?= BASE_URL ?>/index.php" class="header-search-container">
                <input type="search" name="search" class="search-field"
                       placeholder="Pretraži proizvode..."
                       value="<?= e($searchQuery) ?>">
                <button type="submit" class="search-btn" aria-label="Pretraži">
                    <ion-icon name="search-outline"></ion-icon>
                </button>
            </form>

            <div class="header-user-actions">
                <?php if (isLoggedIn()): ?>
                    <a href="<?= isAdmin() ? BASE_URL . '/admin/index.php' : BASE_URL . '/orders/history.php' ?>"
                       class="action-btn" title="<?= isAdmin() ? 'Admin panel' : 'Narudžbe' ?>">
                        <ion-icon name="person-outline"></ion-icon>
                    </a>
                    <?php if (!isAdmin()): ?>
                        <a href="<?= BASE_URL ?>/cart/index.php" class="action-btn" title="Korpa">
                            <ion-icon name="bag-handle-outline"></ion-icon>
                            <?php if ($cartCount > 0): ?>
                                <span class="count"><?= $cartCount ?></span>
                            <?php endif; ?>
                        </a>
                    <?php endif; ?>
                    <a href="<?= BASE_URL ?>/auth/logout.php" class="action-btn" title="Odjava">
                        <ion-icon name="log-out-outline"></ion-icon>
                    </a>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>/auth/login.php" class="action-btn" title="Prijava">
                        <ion-icon name="person-outline"></ion-icon>
                    </a>
                    <a href="<?= BASE_URL ?>/auth/register.php" class="btn-header-register">Registracija</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <nav class="desktop-navigation-menu">
        <div class="container">
            <ul class="desktop-menu-category-list">
                <li class="menu-category">
                    <a href="<?= BASE_URL ?>/index.php" class="menu-title">Početna</a>
                </li>
                <?php foreach ($navCategories as $cat): ?>
                    <li class="menu-category">
                        <a href="<?= BASE_URL ?>/index.php?category=<?= $cat['id'] ?>"
                           class="menu-title <?= $activeCategoryId === (int) $cat['id'] ? 'active' : '' ?>">
                            <?= e($cat['name']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
                <?php if (isAdmin()): ?>
                    <li class="menu-category">
                        <a href="<?= BASE_URL ?>/admin/index.php" class="menu-title menu-title-admin">Admin</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <div class="mobile-bottom-navigation">
        <button class="action-btn" data-mobile-menu-open-btn aria-label="Meni">
            <ion-icon name="menu-outline"></ion-icon>
        </button>
        <?php if (isLoggedIn() && !isAdmin()): ?>
            <a href="<?= BASE_URL ?>/cart/index.php" class="action-btn">
                <ion-icon name="bag-handle-outline"></ion-icon>
                <?php if ($cartCount > 0): ?><span class="count"><?= $cartCount ?></span><?php endif; ?>
            </a>
        <?php else: ?>
            <a href="<?= BASE_URL ?>/auth/login.php" class="action-btn">
                <ion-icon name="bag-handle-outline"></ion-icon>
            </a>
        <?php endif; ?>
        <a href="<?= BASE_URL ?>/index.php" class="action-btn">
            <ion-icon name="home-outline"></ion-icon>
        </a>
        <?php if (isLoggedIn()): ?>
            <a href="<?= isAdmin() ? BASE_URL . '/admin/index.php' : BASE_URL . '/orders/history.php' ?>" class="action-btn">
                <ion-icon name="person-outline"></ion-icon>
            </a>
        <?php else: ?>
            <a href="<?= BASE_URL ?>/auth/register.php" class="action-btn">
                <ion-icon name="person-outline"></ion-icon>
            </a>
        <?php endif; ?>
        <button class="action-btn" data-mobile-menu-open-btn aria-label="Kategorije">
            <ion-icon name="grid-outline"></ion-icon>
        </button>
    </div>

    <nav class="mobile-navigation-menu" data-mobile-menu>
        <div class="menu-top">
            <h2 class="menu-title">Meni</h2>
            <button class="menu-close-btn" data-mobile-menu-close-btn>
                <ion-icon name="close-outline"></ion-icon>
            </button>
        </div>

        <ul class="mobile-menu-category-list">
            <li class="menu-category">
                <a href="<?= BASE_URL ?>/index.php" class="menu-title">Svi proizvodi</a>
            </li>
            <?php foreach ($navCategories as $cat): ?>
                <li class="menu-category">
                    <a href="<?= BASE_URL ?>/index.php?category=<?= $cat['id'] ?>" class="menu-title">
                        <?= e($cat['name']) ?>
                        <span class="menu-count">(<?= (int) $cat['product_count'] ?>)</span>
                    </a>
                </li>
            <?php endforeach; ?>
            <?php if (isLoggedIn() && !isAdmin()): ?>
                <li class="menu-category"><a href="<?= BASE_URL ?>/cart/index.php" class="menu-title">Korpa</a></li>
                <li class="menu-category"><a href="<?= BASE_URL ?>/orders/history.php" class="menu-title">Narudžbe</a></li>
            <?php endif; ?>
            <?php if (isAdmin()): ?>
                <li class="menu-category"><a href="<?= BASE_URL ?>/admin/index.php" class="menu-title">Admin panel</a></li>
            <?php endif; ?>
            <?php if (isLoggedIn()): ?>
                <li class="menu-category">
                    <a href="<?= BASE_URL ?>/auth/logout.php" class="menu-title">Odjava (<?= e($_SESSION['user_name']) ?>)</a>
                </li>
            <?php else: ?>
                <li class="menu-category"><a href="<?= BASE_URL ?>/auth/login.php" class="menu-title">Prijava</a></li>
                <li class="menu-category"><a href="<?= BASE_URL ?>/auth/register.php" class="menu-title">Registracija</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<main class="main-wrapper">
    <?php $flash = getFlash(); if ($flash): ?>
        <div class="container">
            <div class="alert alert-<?= e($flash['type']) ?>">
                <?= e($flash['message']) ?>
            </div>
        </div>
    <?php endif; ?>
