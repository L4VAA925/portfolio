<?php
require_once __DIR__ . '/../includes/init.php';
requireLogin();

if (isAdmin()) {
    redirect('/admin/index.php');
}

$userId = currentUserId();

$stmt = getDB()->prepare(
    'SELECT id, total_amount, status, created_at
     FROM orders
     WHERE user_id = ?
     ORDER BY created_at DESC'
);
$stmt->execute([$userId]);
$orders = $stmt->fetchAll();

$pageTitle = 'Historija narudžbi';
$bodyClass = 'app-page';
require_once __DIR__ . '/../includes/header.php';
?>

<section class="page-header">
    <h1>Historija narudžbi</h1>
    <p>Pregled svih vaših prethodnih narudžbi.</p>
</section>

<?php if (empty($orders)): ?>
    <div class="empty-state">
        <p>Još nemate narudžbi.</p>
        <a href="<?= BASE_URL ?>/index.php" class="btn btn-primary">Pregledaj proizvode</a>
    </div>
<?php else: ?>
    <table class="data-table">
        <thead>
            <tr>
                <th>Broj</th>
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
                    <td><?= e(date('d.m.Y H:i', strtotime($order['created_at']))) ?></td>
                    <td><?= formatPrice((float) $order['total_amount']) ?></td>
                    <td><span class="status-badge status-<?= e($order['status']) ?>"><?= e(orderStatusLabel($order['status'])) ?></span></td>
                    <td><a href="<?= BASE_URL ?>/orders/view.php?id=<?= $order['id'] ?>" class="btn btn-outline btn-sm">Detalji</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
