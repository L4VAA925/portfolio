<?php
require_once __DIR__ . '/../includes/init.php';
requireAdmin();

$id = (int) ($_GET['id'] ?? 0);

try {
    $stmt = getDB()->prepare('DELETE FROM products WHERE id = ?');
    $stmt->execute([$id]);
    setFlash('success', 'Proizvod je obrisan.');
} catch (PDOException $e) {
    setFlash('error', 'Proizvod se ne može obrisati jer postoji u narudžbama.');
}

redirect('/admin/products.php');
