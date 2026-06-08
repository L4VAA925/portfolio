<?php
/**
 * Instalaciona skripta — pokrenite JEDNOM nakon importa baze
 * URL: http://localhost/ecommerce/install/setup.php
 *
 * Kreira administratorski nalog ako ne postoji.
 * Default: admin@shop.local / Admin123!
 */

require_once __DIR__ . '/../config/database.php';

$adminEmail = 'admin@shop.local';
$adminPassword = 'Admin123!';
$adminName = 'System Administrator';

$messages = [];
$errors = [];

try {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    $check = $pdo->prepare('SELECT id FROM users WHERE email = ?');
    $check->execute([$adminEmail]);

    if ($check->fetch()) {
        $messages[] = 'Admin nalog već postoji (' . $adminEmail . ').';
    } else {
        $hash = password_hash($adminPassword, PASSWORD_DEFAULT);
        $insert = $pdo->prepare('INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, ?)');
        $insert->execute([$adminName, $adminEmail, $hash, 'admin']);
        $messages[] = 'Admin nalog je uspješno kreiran.';
        $messages[] = 'E-mail: ' . $adminEmail;
        $messages[] = 'Lozinka: ' . $adminPassword;
        $messages[] = 'Promijenite lozinku nakon prve prijave (ručno u bazi ili dodajte funkciju kasnije).';
    }

    $productCount = (int) $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
    $messages[] = 'Proizvoda u bazi: ' . $productCount;

} catch (PDOException $e) {
    $errors[] = 'Greška konekcije: ' . $e->getMessage();
    $errors[] = 'Provjerite da li ste importovali sql/database.sql i da su podaci u config/database.php ispravni.';
}
?>
<!DOCTYPE html>
<html lang="bs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalacija — E-Trgovina</title>
    <style>
        body { font-family: system-ui, sans-serif; max-width: 600px; margin: 40px auto; padding: 0 20px; }
        .ok { background: #d4edda; padding: 16px; border-radius: 8px; color: #155724; }
        .err { background: #f8d7da; padding: 16px; border-radius: 8px; color: #721c24; }
        a { color: #2563eb; }
    </style>
</head>
<body>
    <h1>Instalacija E-Trgovine</h1>

    <?php if (!empty($errors)): ?>
        <div class="err">
            <?php foreach ($errors as $err): ?>
                <p><?= htmlspecialchars($err) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($messages)): ?>
        <div class="ok">
            <?php foreach ($messages as $msg): ?>
                <p><?= htmlspecialchars($msg) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <p><a href="<?= BASE_URL ?>/index.php">→ Idi na početnu stranicu</a></p>
    <p><a href="<?= BASE_URL ?>/auth/login.php">→ Prijava</a></p>
</body>
</html>
