<?php
require_once __DIR__ . '/../includes/init.php';

if (isLoggedIn()) {
    redirect(isAdmin() ? '/admin/index.php' : '/index.php');
}

$errors = [];
$email = '';
$fullName = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($fullName === '') {
        $errors[] = 'Ime i prezime je obavezno.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Unesite ispravnu e-mail adresu.';
    }
    if (strlen($password) < 6) {
        $errors[] = 'Lozinka mora imati najmanje 6 karaktera.';
    }
    if ($password !== $confirm) {
        $errors[] = 'Lozinke se ne podudaraju.';
    }

    if (empty($errors)) {
        $db = getDB();
        $check = $db->prepare('SELECT id FROM users WHERE email = ?');
        $check->execute([$email]);
        if ($check->fetch()) {
            $errors[] = 'E-mail adresa je već registrovana.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare('INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, ?)');
            $stmt->execute([$fullName, $email, $hash, 'customer']);
            setFlash('success', 'Registracija uspješna. Možete se prijaviti.');
            redirect('/auth/login.php');
        }
    }
}

$pageTitle = 'Registracija';
$bodyClass = 'app-page';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container">
<section class="auth-section">
    <h1>Registracija korisnika</h1>
    <p class="subtitle">Kreirajte nalog za kupovinu u našoj online trgovini.</p>

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
            <label for="full_name">Ime i prezime</label>
            <input type="text" id="full_name" name="full_name" value="<?= e($fullName) ?>" required>
        </div>
        <div class="form-group">
            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" value="<?= e($email) ?>" required>
        </div>
        <div class="form-group">
            <label for="password">Lozinka</label>
            <input type="password" id="password" name="password" required minlength="6">
        </div>
        <div class="form-group">
            <label for="confirm_password">Potvrdite lozinku</label>
            <input type="password" id="confirm_password" name="confirm_password" required minlength="6">
        </div>
        <button type="submit" class="btn btn-primary btn-block">Registruj se</button>
    </form>

    <p class="auth-switch">Već imate nalog? <a href="<?= BASE_URL ?>/auth/login.php">Prijavite se</a></p>
</section>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
