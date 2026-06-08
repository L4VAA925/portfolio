<?php
require_once __DIR__ . '/../includes/init.php';
requireAdmin();

$categories = getDB()->query(
    'SELECT c.*, COUNT(p.id) AS product_count
     FROM categories c
     LEFT JOIN products p ON c.id = p.category_id
     GROUP BY c.id
     ORDER BY c.name'
)->fetchAll();

$pageTitle = 'Upravljanje kategorijama';
$bodyClass = 'admin-page';
require_once __DIR__ . '/../includes/header.php';
?>

<section class="page-header flex-header">
    <div>
        <h1>Kategorije</h1>
        <p>Dodavanje, izmjena i brisanje kategorija proizvoda.</p>
    </div>
    <a href="<?= BASE_URL ?>/admin/category-form.php" class="btn btn-primary">+ Nova kategorija</a>
</section>

<?php if (empty($categories)): ?>
    <div class="empty-state">
        <p>Nema kategorija. Dodajte prvu kategoriju.</p>
    </div>
<?php else: ?>
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Naziv</th>
                <th>Opis</th>
                <th>Broj proizvoda</th>
                <th>Akcije</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categories as $category): ?>
                <tr>
                    <td><?= $category['id'] ?></td>
                    <td><?= e($category['name']) ?></td>
                    <td><?= e($category['description'] ?? '-') ?></td>
                    <td><?= (int) $category['product_count'] ?></td>
                    <td class="actions">
                        <a href="<?= BASE_URL ?>/admin/category-form.php?id=<?= $category['id'] ?>" class="btn btn-outline btn-sm">Izmijeni</a>
                        <a href="<?= BASE_URL ?>/admin/category-delete.php?id=<?= $category['id'] ?>" class="btn btn-danger btn-sm"
                           onclick="return confirm('Obrisati kategoriju?')">Obriši</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<p class="back-link"><a href="<?= BASE_URL ?>/admin/index.php">← Nazad na panel</a></p>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
