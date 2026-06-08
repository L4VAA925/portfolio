<?php
require_once __DIR__ . '/../includes/init.php';
requireAdmin();

$db = getDB();
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$isEdit = $id > 0;

$category = ['name' => '', 'description' => ''];

if ($isEdit) {
    $stmt = $db->prepare('SELECT * FROM categories WHERE id = ?');
    $stmt->execute([$id]);
    $found = $stmt->fetch();
    if (!$found) {
        setFlash('error', 'Kategorija nije pronađena.');
        redirect('/admin/categories.php');
    }
    $category = $found;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category['name'] = trim($_POST['name'] ?? '');
    $category['description'] = trim($_POST['description'] ?? '');

    if ($category['name'] === '') {
        $errors[] = 'Naziv kategorije je obavezan.';
    }

    if (empty($errors)) {
        $checkSql = 'SELECT id FROM categories WHERE name = ?';
        $checkParams = [$category['name']];
        if ($isEdit) {
            $checkSql .= ' AND id != ?';
            $checkParams[] = $id;
        }
        $check = $db->prepare($checkSql);
        $check->execute($checkParams);
        if ($check->fetch()) {
            $errors[] = 'Kategorija s tim nazivom već postoji.';
        }
    }

    if (empty($errors)) {
        if ($isEdit) {
            $stmt = $db->prepare('UPDATE categories SET name = ?, description = ? WHERE id = ?');
            $stmt->execute([$category['name'], $category['description'], $id]);
            setFlash('success', 'Kategorija je ažurirana.');
        } else {
            $stmt = $db->prepare('INSERT INTO categories (name, description) VALUES (?, ?)');
            $stmt->execute([$category['name'], $category['description']]);
            setFlash('success', 'Kategorija je dodana.');
        }
        redirect('/admin/categories.php');
    }
}

$pageTitle = $isEdit ? 'Izmjena kategorije' : 'Nova kategorija';
$bodyClass = 'admin-page';
require_once __DIR__ . '/../includes/header.php';
?>

<section class="page-header">
    <h1><?= $isEdit ? 'Izmjena kategorije' : 'Nova kategorija' ?></h1>
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

<form method="post" class="form-card">
    <div class="form-group">
        <label for="name">Naziv</label>
        <input type="text" id="name" name="name" value="<?= e($category['name']) ?>" required>
    </div>
    <div class="form-group">
        <label for="description">Opis</label>
        <textarea id="description" name="description" rows="3"><?= e($category['description']) ?></textarea>
    </div>
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Sačuvaj</button>
        <a href="<?= BASE_URL ?>/admin/categories.php" class="btn btn-outline">Otkaži</a>
    </div>
</form>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
