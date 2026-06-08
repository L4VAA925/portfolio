<?php
require_once __DIR__ . '/../includes/init.php';
requireAdmin();

$db = getDB();
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$isEdit = $id > 0;

$product = [
    'name'        => '',
    'description' => '',
    'price'       => '',
    'stock'       => 0,
    'category_id' => '',
    'image'       => '',
];

if ($isEdit) {
    $stmt = $db->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->execute([$id]);
    $found = $stmt->fetch();
    if (!$found) {
        setFlash('error', 'Proizvod nije pronađen.');
        redirect('/admin/products.php');
    }
    $product = $found;
}

$categories = $db->query('SELECT id, name FROM categories ORDER BY name')->fetchAll();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product['name']        = trim($_POST['name'] ?? '');
    $product['description'] = trim($_POST['description'] ?? '');
    $product['price']       = trim($_POST['price'] ?? '');
    $product['stock']       = (int) ($_POST['stock'] ?? 0);
    $product['category_id'] = (int) ($_POST['category_id'] ?? 0);
    $product['image']       = trim($_POST['image'] ?? '');

    if ($product['name'] === '') {
        $errors[] = 'Naziv proizvoda je obavezan.';
    }
    if (!is_numeric($product['price']) || (float) $product['price'] < 0) {
        $errors[] = 'Unesite ispravnu cijenu.';
    }
    if ($product['stock'] < 0) {
        $errors[] = 'Stanje ne može biti negativno.';
    }
    if ($product['category_id'] <= 0) {
        $errors[] = 'Odaberite kategoriju.';
    }

    if (empty($errors)) {
        if ($isEdit) {
            $stmt = $db->prepare(
                'UPDATE products SET category_id = ?, name = ?, description = ?, price = ?, stock = ?, image = ? WHERE id = ?'
            );
            $stmt->execute([
                $product['category_id'],
                $product['name'],
                $product['description'],
                $product['price'],
                $product['stock'],
                $product['image'] ?: null,
                $id,
            ]);
            setFlash('success', 'Proizvod je ažuriran.');
        } else {
            $stmt = $db->prepare(
                'INSERT INTO products (category_id, name, description, price, stock, image) VALUES (?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([
                $product['category_id'],
                $product['name'],
                $product['description'],
                $product['price'],
                $product['stock'],
                $product['image'] ?: null,
            ]);
            setFlash('success', 'Proizvod je dodan.');
        }
        redirect('/admin/products.php');
    }
}

$pageTitle = $isEdit ? 'Izmjena proizvoda' : 'Novi proizvod';
$bodyClass = 'admin-page';
require_once __DIR__ . '/../includes/header.php';
?>

<section class="page-header">
    <h1><?= $isEdit ? 'Izmjena proizvoda' : 'Novi proizvod' ?></h1>
</section>

<?php if (empty($categories)): ?>
    <div class="alert alert-error">
        Prvo dodajte barem jednu kategoriju prije dodavanja proizvoda.
        <a href="<?= BASE_URL ?>/admin/category-form.php">Dodaj kategoriju</a>
    </div>
<?php else: ?>

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
            <input type="text" id="name" name="name" value="<?= e($product['name']) ?>" required>
        </div>
        <div class="form-group">
            <label for="category_id">Kategorija</label>
            <select id="category_id" name="category_id" required>
                <option value="">-- Odaberite --</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= (int) $product['category_id'] === (int) $cat['id'] ? 'selected' : '' ?>>
                        <?= e($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="description">Opis</label>
            <textarea id="description" name="description" rows="4"><?= e($product['description']) ?></textarea>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="price">Cijena (KM)</label>
                <input type="number" id="price" name="price" step="0.01" min="0" value="<?= e($product['price']) ?>" required>
            </div>
            <div class="form-group">
                <label for="stock">Stanje</label>
                <input type="number" id="stock" name="stock" min="0" value="<?= (int) $product['stock'] ?>" required>
            </div>
        </div>
        <div class="form-group">
            <label for="image">URL slike</label>
            <input type="url" id="image" name="image" value="<?= e($product['image']) ?>" placeholder="https://primjer.com/slika.jpg">
            <small>Unesite punu URL adresu slike proizvoda.</small>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Sačuvaj</button>
            <a href="<?= BASE_URL ?>/admin/products.php" class="btn btn-outline">Otkaži</a>
        </div>
    </form>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
