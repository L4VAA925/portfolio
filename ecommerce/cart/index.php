<?php
require_once __DIR__ . '/../includes/init.php';
requireLogin();

if (isAdmin()) {
    redirect('/admin/index.php');
}

$userId = currentUserId();
$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $quantities = $_POST['quantity'] ?? [];
    foreach ($quantities as $cartId => $qty) {
        $cartId = (int) $cartId;
        $qty = max(0, (int) $qty);

        if ($qty === 0) {
            $del = $db->prepare('DELETE FROM cart_items WHERE id = ? AND user_id = ?');
            $del->execute([$cartId, $userId]);
            continue;
        }

        $stmt = $db->prepare(
            'SELECT ci.id, p.stock
             FROM cart_items ci
             JOIN products p ON ci.product_id = p.id
             WHERE ci.id = ? AND ci.user_id = ?'
        );
        $stmt->execute([$cartId, $userId]);
        $item = $stmt->fetch();

        if ($item && $qty <= (int) $item['stock']) {
            $upd = $db->prepare('UPDATE cart_items SET quantity = ? WHERE id = ? AND user_id = ?');
            $upd->execute([$qty, $cartId, $userId]);
        }
    }
    setFlash('success', 'Korpa je ažurirana.');
    redirect('/cart/index.php');
}

$stmt = $db->prepare(
    'SELECT ci.id, ci.quantity, p.id AS product_id, p.name, p.price, p.stock, p.image
     FROM cart_items ci
     JOIN products p ON ci.product_id = p.id
     WHERE ci.user_id = ?
     ORDER BY ci.updated_at DESC'
);
$stmt->execute([$userId]);
$items = $stmt->fetchAll();

$total = 0;
foreach ($items as $item) {
    $total += (float) $item['price'] * (int) $item['quantity'];
}

$pageTitle = 'Korpa';
$bodyClass = 'app-page';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container app-page">
<section class="page-header">
    <h1>Korpa za kupovinu</h1>
</section>

<?php if (empty($items)): ?>
    <div class="empty-state">
        <p>Vaša korpa je prazna.</p>
        <a href="<?= BASE_URL ?>/index.php" class="btn btn-primary">Pregledaj proizvode</a>
    </div>
<?php else: ?>
    <form method="post" class="cart-form">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Proizvod</th>
                    <th>Cijena</th>
                    <th>Količina</th>
                    <th>Ukupno</th>
                    <th>Akcija</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <?php $lineTotal = (float) $item['price'] * (int) $item['quantity']; ?>
                    <tr>
                        <td>
                            <div class="cart-product">
                                <?php if (!empty($item['image'])): ?>
                                    <img src="<?= e($item['image']) ?>" alt="" class="cart-thumb">
                                <?php endif; ?>
                                <a href="<?= BASE_URL ?>/product.php?id=<?= $item['product_id'] ?>"><?= e($item['name']) ?></a>
                            </div>
                        </td>
                        <td><?= formatPrice((float) $item['price']) ?></td>
                        <td>
                            <input type="number" name="quantity[<?= $item['id'] ?>]" value="<?= (int) $item['quantity'] ?>"
                                   min="0" max="<?= (int) $item['stock'] ?>" class="qty-input">
                        </td>
                        <td><?= formatPrice($lineTotal) ?></td>
                        <td>
                            <a href="<?= BASE_URL ?>/cart/remove.php?id=<?= $item['id'] ?>" class="btn btn-danger btn-sm"
                               onclick="return confirm('Ukloniti proizvod iz korpe?')">Ukloni</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="cart-summary">
            <p class="cart-total">Ukupno: <strong><?= formatPrice($total) ?></strong></p>
            <div class="cart-actions">
                <button type="submit" class="btn btn-outline">Ažuriraj korpu</button>
                <a href="<?= BASE_URL ?>/orders/checkout.php" class="btn btn-primary">Nastavi na plaćanje</a>
            </div>
        </div>
    </form>
<?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
