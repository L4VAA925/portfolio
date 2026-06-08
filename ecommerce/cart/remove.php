<?php
require_once __DIR__ . '/../includes/init.php';
requireLogin();

if (isAdmin()) {
    redirect('/admin/index.php');
}

$cartId = (int) ($_GET['id'] ?? 0);
$userId = currentUserId();

$stmt = getDB()->prepare('DELETE FROM cart_items WHERE id = ? AND user_id = ?');
$stmt->execute([$cartId, $userId]);

setFlash('success', 'Proizvod je uklonjen iz korpe.');
redirect('/cart/index.php');
