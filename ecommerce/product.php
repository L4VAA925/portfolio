<?php
require_once __DIR__ . '/includes/init.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$stmt = getDB()->prepare(
    'SELECT p.*, c.name AS category_name, c.id AS cat_id
     FROM products p
     JOIN categories c ON p.category_id = c.id
     WHERE p.id = ?'
);
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    setFlash('error', 'Proizvod nije pronađen.');
    redirect('/index.php');
}

$pageTitle = $product['name'];
require_once __DIR__ . '/includes/header.php';
?>

<div class="product-detail-page">
    <div class="container">
        <nav class="breadcrumb">
            <a href="<?= BASE_URL ?>/index.php">Početna</a>
            <span>/</span>
            <a href="<?= BASE_URL ?>/index.php?category=<?= $product['cat_id'] ?>"><?= e($product['category_name']) ?></a>
            <span>/</span>
            <span><?= e($product['name']) ?></span>
        </nav>

        <div class="product-detail">
            <div class="product-detail-image">
                <?php if (!empty($product['image'])): ?>
                    <img src="<?= e($product['image']) ?>" alt="<?= e($product['name']) ?>" class="detail-img">
                <?php else: ?>
                    <div class="product-img-placeholder large">Nema slike</div>
                <?php endif; ?>
            </div>

            <div class="product-detail-info">
                <a href="<?= BASE_URL ?>/index.php?category=<?= $product['cat_id'] ?>"
                   class="showcase-category"><?= e($product['category_name']) ?></a>
                <h1 class="product-detail-title"><?= e($product['name']) ?></h1>
                <p class="product-price-large"><?= formatPrice((float) $product['price']) ?></p>
                <p class="stock-info">
                    <?php if ((int) $product['stock'] > 0): ?>
                        <span class="stock-ok">Na stanju: <?= (int) $product['stock'] ?> kom</span>
                    <?php else: ?>
                        <span class="stock-no">Trenutno nije dostupan</span>
                    <?php endif; ?>
                </p>

                <div class="product-description">
                    <h2>Opis proizvoda</h2>
                    <p><?= nl2br(e($product['description'] ?? 'Nema opisa.')) ?></p>
                </div>

                <?php if ((int) $product['stock'] > 0): ?>
                    <?php if (isLoggedIn() && !isAdmin()): ?>
                        <form action="<?= BASE_URL ?>/cart/add.php" method="post" class="add-to-cart-form">
                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                            <div class="form-group inline">
                                <label for="quantity">Količina</label>
                                <input type="number" id="quantity" name="quantity" value="1"
                                       min="1" max="<?= (int) $product['stock'] ?>">
                            </div>
                            <button type="submit" class="add-cart-btn">
                                <ion-icon name="bag-add-outline"></ion-icon>
                                Dodaj u korpu
                            </button>
                        </form>
                    <?php elseif (!isLoggedIn()): ?>
                        <p class="info-text">Za kupovinu se morate <a href="<?= BASE_URL ?>/auth/login.php">prijaviti</a> ili <a href="<?= BASE_URL ?>/auth/register.php">registrovati</a>.</p>
                    <?php endif; ?>
                <?php endif; ?>

                <a href="<?= BASE_URL ?>/index.php" class="btn btn-outline">← Nazad na katalog</a>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
