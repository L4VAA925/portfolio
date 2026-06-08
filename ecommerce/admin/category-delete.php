<?php
require_once __DIR__ . '/../includes/init.php';
requireAdmin();

$id = (int) ($_GET['id'] ?? 0);
$db = getDB();

$check = $db->prepare('SELECT COUNT(*) FROM products WHERE category_id = ?');
$check->execute([$id]);
$count = (int) $check->fetchColumn();

if ($count > 0) {
    setFlash('error', 'Kategorija se ne može obrisati jer sadrži proizvode.');
    redirect('/admin/categories.php');
}

try {
    $stmt = $db->prepare('DELETE FROM categories WHERE id = ?');
    $stmt->execute([$id]);
    setFlash('success', 'Kategorija je obrisana.');
} catch (PDOException $e) {
    setFlash('error', 'Greška pri brisanju kategorije.');
}

redirect('/admin/categories.php');
