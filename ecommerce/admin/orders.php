<?php
require_once __DIR__ . '/../includes/init.php';
requireAdmin();

$orders = getDB()->query(
    'SELECT o.id, o.total_amount, o.status, o.created_at, u.full_name, u.email
     FROM orders o
     JOIN users u ON o.user_id = u.id
     ORDER BY o.created_at DESC'
)->fetchAll();

$pageTitle = 'Upravljanje narudžbama';
$bodyClass = 'admin-page';
require_once __DIR__ . '/../includes/header.php';
?>

<section class="page-header">
    <h1>Narudžbe</h1>
    <p>Pregled i upravljanje svim narudžbama kupaca.</p>
</section>

<?php if (empty($orders)): ?>
    <div class="empty-state">
        <p>Nema narudžbi.</p>
    </div>
<?php else: ?>
    <table class="data-table">
        <thead>
            <tr>
                <th>Broj</th>
                <th>Kupac</th>
                <th>E-mail</th>
                <th>Datum</th>
                <th>Iznos</th>
                <th>Status</th>
                <th>Akcija</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td>#<?= $order['id'] ?></td>
                    <td><?= e($order['full_name']) ?></td>
                    <td><?= e($order['email']) ?></td>
                    <td><?= e(date('d.m.Y H:i', strtotime($order['created_at']))) ?></td>
                    <td><?= formatPrice((float) $order['total_amount']) ?></td>
                    <td><span class="status-badge status-<?= e($order['status']) ?>"><?= e(orderStatusLabel($order['status'])) ?></span></td>
                    <td><a href="<?= BASE_URL ?>/admin/order-view.php?id=<?= $order['id'] ?>" class="btn btn-outline btn-sm">Pregled</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<p class="back-link"><a href="<?= BASE_URL ?>/admin/index.php">← Nazad na panel</a></p>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
