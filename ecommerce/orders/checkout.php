<?php
require_once __DIR__ . '/../includes/init.php';
requireLogin();

if (isAdmin()) {
    redirect('/admin/index.php');
}

$userId = currentUserId();
$db = getDB();

$stmt = $db->prepare(
    'SELECT ci.quantity, p.id, p.name, p.price, p.stock
     FROM cart_items ci
     JOIN products p ON ci.product_id = p.id
     WHERE ci.user_id = ?'
);
$stmt->execute([$userId]);
$items = $stmt->fetchAll();

if (empty($items)) {
    setFlash('error', 'Korpa je prazna.');
    redirect('/cart/index.php');
}

$total = 0;
foreach ($items as $item) {
    $total += (float) $item['price'] * (int) $item['quantity'];
}

$errors = [];
$address = '';
$phone = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = trim($_POST['shipping_address'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    if ($address === '') {
        $errors[] = 'Adresa za dostavu je obavezna.';
    }
    if ($phone === '') {
        $errors[] = 'Broj telefona je obavezan.';
    }

    if (empty($errors)) {
        try {
            $db->beginTransaction();

            foreach ($items as $item) {
                if ((int) $item['quantity'] > (int) $item['stock']) {
                    throw new Exception('Proizvod "' . $item['name'] . '" nema dovoljno na stanju.');
                }
            }

            $orderStmt = $db->prepare(
                'INSERT INTO orders (user_id, shipping_address, phone, total_amount, status)
                 VALUES (?, ?, ?, ?, ?)'
            );
            $orderStmt->execute([$userId, $address, $phone, $total, 'pending']);
            $orderId = (int) $db->lastInsertId();

            $itemStmt = $db->prepare(
                'INSERT INTO order_items (order_id, product_id, quantity, unit_price, subtotal)
                 VALUES (?, ?, ?, ?, ?)'
            );
            $stockStmt = $db->prepare('UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?');

            foreach ($items as $item) {
                $qty = (int) $item['quantity'];
                $price = (float) $item['price'];
                $subtotal = $price * $qty;

                $itemStmt->execute([$orderId, $item['id'], $qty, $price, $subtotal]);

                $stockStmt->execute([$qty, $item['id'], $qty]);
                if ($stockStmt->rowCount() === 0) {
                    throw new Exception('Nema dovoljno proizvoda na stanju: ' . $item['name']);
                }
            }

            $clearCart = $db->prepare('DELETE FROM cart_items WHERE user_id = ?');
            $clearCart->execute([$userId]);

            $db->commit();
            setFlash('success', 'Narudžba #' . $orderId . ' je uspješno kreirana.');
            redirect('/orders/view.php?id=' . $orderId);
        } catch (Exception $e) {
            $db->rollBack();
            $errors[] = $e->getMessage();
        }
    }
}

$pageTitle = 'Plaćanje';
$bodyClass = 'app-page';
require_once __DIR__ . '/../includes/header.php';
?>

<section class="page-header">
    <h1>Plaćanje narudžbe</h1>
    <p>Potvrdite podatke za dostavu i završite narudžbu.</p>
</section>

<?php if (!empty($errors)): ?>
    <div class="alert alert-error">
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?= e($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="checkout-layout">
    <div class="checkout-form">
        <form method="post" class="form-card">
            <h2>Podaci za dostavu</h2>
            <div class="form-group">
                <label for="shipping_address">Adresa za dostavu</label>
                <textarea id="shipping_address" name="shipping_address" rows="3" required><?= e($address) ?></textarea>
            </div>
            <div class="form-group">
                <label for="phone">Broj telefona</label>
                <input type="text" id="phone" name="phone" value="<?= e($phone) ?>" required>
            </div>
            <p class="info-text">Napomena: Online plaćanje nije uključeno u ovu verziju aplikacije. Narudžba se plaća prilikom dostave.</p>
            <button type="submit" class="btn btn-primary">Potvrdi narudžbu</button>
        </form>
    </div>

    <aside class="checkout-summary">
        <h2>Pregled narudžbe</h2>
        <ul class="summary-list">
            <?php foreach ($items as $item): ?>
                <li>
                    <span><?= e($item['name']) ?> × <?= (int) $item['quantity'] ?></span>
                    <span><?= formatPrice((float) $item['price'] * (int) $item['quantity']) ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
        <p class="cart-total">Ukupno: <strong><?= formatPrice($total) ?></strong></p>
    </aside>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
