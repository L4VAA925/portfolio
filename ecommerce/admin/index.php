<?php
require_once __DIR__ . '/../includes/init.php';
requireAdmin();

$db = getDB();

$productCount = (int) $db->query('SELECT COUNT(*) FROM products')->fetchColumn();
$categoryCount = (int) $db->query('SELECT COUNT(*) FROM categories')->fetchColumn();
$orderCount = (int) $db->query('SELECT COUNT(*) FROM orders')->fetchColumn();
$pendingCount = (int) $db->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn();

$recentOrders = $db->query(
    'SELECT o.id, o.total_amount, o.status, o.created_at, u.full_name
     FROM orders o
     JOIN users u ON o.user_id = u.id
     ORDER BY o.created_at DESC
     LIMIT 5'
)->fetchAll();

$pageTitle = 'Admin panel';
$bodyClass = 'admin-page';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container app-page">
<section class="page-header">
    <h1>Administratorski panel</h1>
    <p>Upravljanje proizvodima, kategorijama i narudžbama.</p>
</section>

<div class="admin-nav">
    <a href="<?= BASE_URL ?>/admin/products.php" class="admin-card">
        <h3>Proizvodi</h3>
        <p class="stat"><?= $productCount ?></p>
    </a>
    <a href="<?= BASE_URL ?>/admin/categories.php" class="admin-card">
        <h3>Kategorije</h3>
        <p class="stat"><?= $categoryCount ?></p>
    </a>
    <a href="<?= BASE_URL ?>/admin/orders.php" class="admin-card">
        <h3>Narudžbe</h3>
        <p class="stat"><?= $orderCount ?></p>
        <?php if ($pendingCount > 0): ?>
            <span class="badge"><?= $pendingCount ?> na čekanju</span>
        <?php endif; ?>
    </a>
</div>

<section class="card">
    <h2>Nedavne narudžbe</h2>
    <?php if (empty($recentOrders)): ?>
        <p>Nema narudžbi.</p>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Broj</th>
                    <th>Kupac</th>
                    <th>Datum</th>
                    <th>Iznos</th>
                    <th>Status</th>
                    <th>Akcija</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentOrders as $order): ?>
                    <tr>
                        <td>#<?= $order['id'] ?></td>
                        <td><?= e($order['full_name']) ?></td>
                        <td><?= e(date('d.m.Y H:i', strtotime($order['created_at']))) ?></td>
                        <td><?= formatPrice((float) $order['total_amount']) ?></td>
                        <td><span class="status-badge status-<?= e($order['status']) ?>"><?= e(orderStatusLabel($order['status'])) ?></span></td>
                        <td><a href="<?= BASE_URL ?>/admin/order-view.php?id=<?= $order['id'] ?>" class="btn btn-outline btn-sm">Pregled</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
