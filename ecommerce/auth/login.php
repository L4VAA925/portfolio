<?php
require_once __DIR__ . '/../includes/init.php';

if (isLoggedIn()) {
    redirect(isAdmin() ? '/admin/index.php' : '/index.php');
}

$errors = [];
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Unesite ispravnu e-mail adresu.';
    }
    if ($password === '') {
        $errors[] = 'Lozinka je obavezna.';
    }

    if (empty($errors)) {
        $stmt = getDB()->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            loginUser($user);
            setFlash('success', 'Uspješno ste se prijavili.');
            redirect($user['role'] === 'admin' ? '/admin/index.php' : '/index.php');
        }
        $errors[] = 'Pogrešan e-mail ili lozinka.';
    }
}

$pageTitle = 'Prijava';
$bodyClass = 'app-page';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container">
<section class="auth-section">
    <h1>Prijava</h1>
    <p class="subtitle">Prijavite se na svoj korisnički nalog.</p>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= e($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" class="form-card">
        <div class="form-group">
            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" value="<?= e($email) ?>" required>
        </div>
        <div class="form-group">
            <label for="password">Lozinka</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Prijavi se</button>
    </form>

    <p class="auth-switch">Nemate nalog? <a href="<?= BASE_URL ?>/auth/register.php">Registrujte se</a></p>
</section>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
