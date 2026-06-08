<?php
require_once __DIR__ . '/../includes/init.php';
requireLogin();

if (isAdmin()) {
    redirect('/admin/index.php');
}

$orderId = (int) ($_GET['id'] ?? 0);
$userId = currentUserId();
$db = getDB();

$stmt = $db->prepare('SELECT * FROM orders WHERE id = ? AND user_id = ?');
$stmt->execute([$orderId, $userId]);
$order = $stmt->fetch();

if (!$order) {
    setFlash('error', 'Narudžba nije pronađena.');
    redirect('/orders/history.php');
}

$itemsStmt = $db->prepare(
    'SELECT oi.*, p.name AS product_name
     FROM order_items oi
     JOIN products p ON oi.product_id = p.id
     WHERE oi.order_id = ?'
);
$itemsStmt->execute([$orderId]);
$items = $itemsStmt->fetchAll();

$pageTitle = 'Narudžba #' . $orderId;
$bodyClass = 'app-page';
require_once __DIR__ . '/../includes/header.php';
?>

<section class="page-header">
    <h1>Narudžba #<?= $order['id'] ?></h1>
    <p>Datum: <?= e(date('d.m.Y H:i', strtotime($order['created_at']))) ?></p>
</section>

<div class="order-detail">
    <div class="order-info card">
        <h2>Informacije o dostavi</h2>
        <p><strong>Adresa:</strong> <?= nl2br(e($order['shipping_address'])) ?></p>
        <p><strong>Telefon:</strong> <?= e($order['phone']) ?></p>
        <p><strong>Status:</strong> <span class="status-badge status-<?= e($order['status']) ?>"><?= e(orderStatusLabel($order['status'])) ?></span></p>
        <p><strong>Ukupno:</strong> <?= formatPrice((float) $order['total_amount']) ?></p>
    </div>

    <div class="order-items card">
        <h2>Stavke narudžbe</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Proizvod</th>
                    <th>Cijena</th>
                    <th>Količina</th>
                    <th>Ukupno</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= e($item['product_name']) ?></td>
                        <td><?= formatPrice((float) $item['unit_price']) ?></td>
                        <td><?= (int) $item['quantity'] ?></td>
                        <td><?= formatPrice((float) $item['subtotal']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <a href="<?= BASE_URL ?>/orders/history.php" class="btn btn-outline">← Nazad na historiju</a>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
