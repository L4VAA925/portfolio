<?php
require_once __DIR__ . '/../includes/init.php';
requireAdmin();

$orderId = (int) ($_GET['id'] ?? 0);
$db = getDB();

$allowedStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'] ?? '';
    if (in_array($status, $allowedStatuses, true)) {
        $stmt = $db->prepare('UPDATE orders SET status = ? WHERE id = ?');
        $stmt->execute([$status, $orderId]);
        setFlash('success', 'Status narudžbe je ažuriran.');
    }
    redirect('/admin/order-view.php?id=' . $orderId);
}

$stmt = $db->prepare(
    'SELECT o.*, u.full_name, u.email
     FROM orders o
     JOIN users u ON o.user_id = u.id
     WHERE o.id = ?'
);
$stmt->execute([$orderId]);
$order = $stmt->fetch();

if (!$order) {
    setFlash('error', 'Narudžba nije pronađena.');
    redirect('/admin/orders.php');
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
$bodyClass = 'admin-page';
require_once __DIR__ . '/../includes/header.php';
?>

<section class="page-header">
    <h1>Narudžba #<?= $order['id'] ?></h1>
</section>

<div class="order-detail">
    <div class="order-info card">
        <h2>Podaci o kupcu</h2>
        <p><strong>Ime:</strong> <?= e($order['full_name']) ?></p>
        <p><strong>E-mail:</strong> <?= e($order['email']) ?></p>
        <p><strong>Adresa:</strong> <?= nl2br(e($order['shipping_address'])) ?></p>
        <p><strong>Telefon:</strong> <?= e($order['phone']) ?></p>
        <p><strong>Datum:</strong> <?= e(date('d.m.Y H:i', strtotime($order['created_at']))) ?></p>
        <p><strong>Ukupno:</strong> <?= formatPrice((float) $order['total_amount']) ?></p>

        <form method="post" class="status-form">
            <div class="form-group">
                <label for="status">Status narudžbe</label>
                <select id="status" name="status">
                    <?php foreach ($allowedStatuses as $status): ?>
                        <option value="<?= $status ?>" <?= $order['status'] === $status ? 'selected' : '' ?>>
                            <?= e(orderStatusLabel($status)) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Ažuriraj status</button>
        </form>
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

    <a href="<?= BASE_URL ?>/admin/orders.php" class="btn btn-outline">← Nazad na narudžbe</a>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
