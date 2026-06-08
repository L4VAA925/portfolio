<?php
require_once __DIR__ . '/../includes/init.php';
requireAdmin();

$products = getDB()->query(
    'SELECT p.*, c.name AS category_name
     FROM products p
     JOIN categories c ON p.category_id = c.id
     ORDER BY p.created_at DESC'
)->fetchAll();

$pageTitle = 'Upravljanje proizvodima';
$bodyClass = 'admin-page';
require_once __DIR__ . '/../includes/header.php';
?>

<section class="page-header flex-header">
    <div>
        <h1>Proizvodi</h1>
        <p>Dodavanje, izmjena i brisanje proizvoda.</p>
    </div>
    <a href="<?= BASE_URL ?>/admin/product-form.php" class="btn btn-primary">+ Novi proizvod</a>
</section>

<?php if (empty($products)): ?>
    <div class="empty-state">
        <p>Nema proizvoda. Dodajte prvi proizvod.</p>
    </div>
<?php else: ?>
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Naziv</th>
                <th>Kategorija</th>
                <th>Cijena</th>
                <th>Stanje</th>
                <th>Akcije</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?= $product['id'] ?></td>
                    <td><?= e($product['name']) ?></td>
                    <td><?= e($product['category_name']) ?></td>
                    <td><?= formatPrice((float) $product['price']) ?></td>
                    <td><?= (int) $product['stock'] ?></td>
                    <td class="actions">
                        <a href="<?= BASE_URL ?>/admin/product-form.php?id=<?= $product['id'] ?>" class="btn btn-outline btn-sm">Izmijeni</a>
                        <a href="<?= BASE_URL ?>/admin/product-delete.php?id=<?= $product['id'] ?>" class="btn btn-danger btn-sm"
                           onclick="return confirm('Obrisati proizvod?')">Obriši</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<p class="back-link"><a href="<?= BASE_URL ?>/admin/index.php">← Nazad na panel</a></p>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
