<?php
require_once __DIR__ . '/includes/init.php';

$db = getDB();

$categoryId = isset($_GET['category']) ? (int) $_GET['category'] : 0;
$search = trim($_GET['search'] ?? '');

$categories = $db->query(
    'SELECT c.id, c.name, COUNT(p.id) AS product_count
     FROM categories c
     LEFT JOIN products p ON c.id = p.category_id
     GROUP BY c.id
     ORDER BY c.name'
)->fetchAll();

$sql = 'SELECT p.*, c.name AS category_name
        FROM products p
        JOIN categories c ON p.category_id = c.id
        WHERE 1=1';
$params = [];

if ($categoryId > 0) {
    $sql .= ' AND p.category_id = ?';
    $params[] = $categoryId;
}
if ($search !== '') {
    $sql .= ' AND (p.name LIKE ? OR p.description LIKE ?)';
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
}

$sql .= ' ORDER BY p.created_at DESC';
$stmt = $db->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

$activeCategoryName = 'Svi proizvodi';
foreach ($categories as $cat) {
    if ((int) $cat['id'] === $categoryId) {
        $activeCategoryName = $cat['name'];
        break;
    }
}

$pageTitle = $search !== '' ? 'Pretraga: ' . $search : ($categoryId > 0 ? $activeCategoryName : 'Katalog proizvoda');
require_once __DIR__ . '/includes/header.php';
?>

<?php if ($categoryId === 0 && $search === ''): ?>
<div class="banner">
    <div class="container">
        <div class="slider-container has-scrollbar">
            <div class="slider-item">
                <img src="https://picsum.photos/seed/etrgovina1/1200/450" alt="Online prodaja" class="banner-img">
                <div class="banner-content">
                    <p class="banner-subtitle">Dobrodošli</p>
                    <h2 class="banner-title">E-Trgovina</h2>
                    <p class="banner-text">Kvalitetni proizvodi od <b>19,99 KM</b></p>
                    <a href="#proizvodi" class="banner-btn">Pogledaj ponudu</a>
                </div>
            </div>
            <div class="slider-item">
                <img src="https://picsum.photos/seed/etrgovina2/1200/450" alt="Elektronika" class="banner-img">
                <div class="banner-content">
                    <p class="banner-subtitle">Kategorija</p>
                    <h2 class="banner-title">Elektronika</h2>
                    <p class="banner-text">Računarska oprema i dodaci</p>
                    <a href="<?= BASE_URL ?>/index.php?category=1" class="banner-btn">Kupi sada</a>
                </div>
            </div>
            <div class="slider-item">
                <img src="https://picsum.photos/seed/etrgovina3/1200/450" alt="Knjige" class="banner-img">
                <div class="banner-content">
                    <p class="banner-subtitle">Ponuda</p>
                    <h2 class="banner-title">Knjige i literatura</h2>
                    <p class="banner-text">Udžbenici za studente</p>
                    <a href="<?= BASE_URL ?>/index.php?category=3" class="banner-btn">Pogledaj</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="category">
    <div class="container">
        <div class="category-item-container has-scrollbar">
            <a href="<?= BASE_URL ?>/index.php" class="category-item <?= $categoryId === 0 ? 'active' : '' ?>">
                <div class="category-content-box">
                    <div class="category-content-flex">
                        <h3 class="category-item-title">Sve kategorije</h3>
                    </div>
                    <span class="category-btn">Prikaži sve</span>
                </div>
            </a>
            <?php foreach ($categories as $cat): ?>
                <a href="<?= BASE_URL ?>/index.php?category=<?= $cat['id'] ?>"
                   class="category-item <?= $categoryId === (int) $cat['id'] ? 'active' : '' ?>">
                    <div class="category-content-box">
                        <div class="category-content-flex">
                            <h3 class="category-item-title"><?= e($cat['name']) ?></h3>
                            <p class="category-item-amount">(<?= (int) $cat['product_count'] ?>)</p>
                        </div>
                        <span class="category-btn">Prikaži</span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="product-container" id="proizvodi">
    <div class="container">
        <div class="catalog-layout">
            <aside class="catalog-sidebar">
                <div class="sidebar-category-box">
                    <h2 class="sidebar-title">Kategorije</h2>
                    <ul class="sidebar-category-list">
                        <li>
                            <a href="<?= BASE_URL ?>/index.php"
                               class="sidebar-link <?= $categoryId === 0 ? 'active' : '' ?>">Svi proizvodi</a>
                        </li>
                        <?php foreach ($categories as $cat): ?>
                            <li>
                                <a href="<?= BASE_URL ?>/index.php?category=<?= $cat['id'] ?>"
                                   class="sidebar-link <?= $categoryId === (int) $cat['id'] ? 'active' : '' ?>">
                                    <?= e($cat['name']) ?>
                                    <span class="sidebar-count"><?= (int) $cat['product_count'] ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </aside>

            <div class="product-box">
                <div class="product-main">
                    <h2 class="title"><?= e($activeCategoryName) ?></h2>
                    <?php if ($search !== ''): ?>
                        <p class="catalog-meta">Rezultati pretrage za: <strong><?= e($search) ?></strong></p>
                    <?php endif; ?>

                    <?php if (empty($products)): ?>
                        <div class="empty-state">
                            <p>Nema proizvoda za prikaz.</p>
                            <a href="<?= BASE_URL ?>/index.php" class="btn btn-primary">Prikaži sve proizvode</a>
                        </div>
                    <?php else: ?>
                        <div class="product-grid">
                            <?php foreach ($products as $product): ?>
                                <div class="showcase">
                                    <div class="showcase-banner">
                                        <a href="<?= BASE_URL ?>/product.php?id=<?= $product['id'] ?>">
                                            <?php if (!empty($product['image'])): ?>
                                                <img src="<?= e($product['image']) ?>"
                                                     alt="<?= e($product['name']) ?>"
                                                     class="product-img default" loading="lazy">
                                            <?php else: ?>
                                                <div class="product-img-placeholder">Nema slike</div>
                                            <?php endif; ?>
                                        </a>
                                        <?php if ((int) $product['stock'] <= 5 && (int) $product['stock'] > 0): ?>
                                            <p class="showcase-badge">Malo komada</p>
                                        <?php elseif ((int) $product['stock'] === 0): ?>
                                            <p class="showcase-badge black">Nema na stanju</p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="showcase-content">
                                        <a href="<?= BASE_URL ?>/index.php?category=<?= $product['category_id'] ?>"
                                           class="showcase-category"><?= e($product['category_name']) ?></a>
                                        <a href="<?= BASE_URL ?>/product.php?id=<?= $product['id'] ?>">
                                            <h3 class="showcase-title"><?= e($product['name']) ?></h3>
                                        </a>
                                        <div class="price-box">
                                            <p class="price"><?= formatPrice((float) $product['price']) ?></p>
                                        </div>
                                        <a href="<?= BASE_URL ?>/product.php?id=<?= $product['id'] ?>"
                                           class="btn-product-detail">Detalji proizvoda</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
