<?php
/**
 * Pomoćne funkcije korištene kroz cijelu aplikaciju
 */

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): void
{
    header('Location: ' . BASE_URL . $path);
    exit;
}

function formatPrice(float $price): string
{
    return number_format($price, 2, ',', '.') . ' KM';
}

function setFlash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}

function getCartCount(int $userId): int
{
    $stmt = getDB()->prepare('SELECT COALESCE(SUM(quantity), 0) AS total FROM cart_items WHERE user_id = ?');
    $stmt->execute([$userId]);
    return (int) $stmt->fetch()['total'];
}

function orderStatusLabel(string $status): string
{
    $labels = [
        'pending'    => 'Na čekanju',
        'processing' => 'U obradi',
        'shipped'    => 'Poslano',
        'delivered'  => 'Isporučeno',
        'cancelled'  => 'Otkazano',
    ];
    return $labels[$status] ?? $status;
}

function roleLabel(string $role): string
{
    return $role === 'admin' ? 'Administrator' : 'Kupac';
}
