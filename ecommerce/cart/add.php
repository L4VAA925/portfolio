<?php
require_once __DIR__ . '/../includes/init.php';
requireLogin();

if (isAdmin()) {
    setFlash('error', 'Administratori ne mogu koristiti korpu.');
    redirect('/admin/index.php');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/index.php');
}

$productId = (int) ($_POST['product_id'] ?? 0);
$quantity = max(1, (int) ($_POST['quantity'] ?? 1));
$userId = currentUserId();

$db = getDB();

$stmt = $db->prepare('SELECT id, stock, name FROM products WHERE id = ?');
$stmt->execute([$productId]);
$product = $stmt->fetch();

if (!$product) {
    setFlash('error', 'Proizvod nije pronađen.');
    redirect('/index.php');
}

if ((int) $product['stock'] < $quantity) {
    setFlash('error', 'Nema dovoljno proizvoda na stanju.');
    redirect('/product.php?id=' . $productId);
}

$check = $db->prepare('SELECT id, quantity FROM cart_items WHERE user_id = ? AND product_id = ?');
$check->execute([$userId, $productId]);
$existing = $check->fetch();

if ($existing) {
    $newQty = (int) $existing['quantity'] + $quantity;
    if ($newQty > (int) $product['stock']) {
        setFlash('error', 'Ukupna količina u korpi premašuje dostupno stanje.');
        redirect('/product.php?id=' . $productId);
    }
    $update = $db->prepare('UPDATE cart_items SET quantity = ? WHERE id = ?');
    $update->execute([$newQty, $existing['id']]);
} else {
    $insert = $db->prepare('INSERT INTO cart_items (user_id, product_id, quantity) VALUES (?, ?, ?)');
    $insert->execute([$userId, $productId, $quantity]);
}

setFlash('success', 'Proizvod "' . $product['name'] . '" je dodan u korpu.');
redirect('/cart/index.php');
