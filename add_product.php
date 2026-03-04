<?php
session_start();
include "config/database.php";


$category_id = null;
$subcat_id = null;
if (isset($_GET['sub_category_id']) && is_numeric($_GET['sub_category_id'])) {
    $subcat_id = (int) $_GET['sub_category_id'];
    
    $stmt = $pdo->prepare("SELECT * FROM sub_categories WHERE id = ?");
    $stmt->execute([$subcat_id]);
    $subcat = $stmt->fetch();
    if (!$subcat) {
        header('Location: index.php');
        exit;
    }
    $category_id = $subcat['category_id'];
} elseif (isset($_GET['category_id']) && is_numeric($_GET['category_id'])) {
    $category_id = (int) $_GET['category_id'];
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$category_id]);
    $cat = $stmt->fetch();
    if (!$cat) {
        header('Location: index.php');
        exit;
    }
}


$categories = [];
$subcategories = [];
if (!$category_id) {
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
    $categories = $stmt->fetchAll();
}
if ($category_id) {
    $stmt = $pdo->prepare("SELECT * FROM sub_categories WHERE category_id = ? ORDER BY name");
    $stmt->execute([$category_id]);
    $subcategories = $stmt->fetchAll();
} else {
    
    $stmt = $pdo->query("SELECT * FROM sub_categories ORDER BY name");
    $subcategories = $stmt->fetchAll();
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
    $categories = $stmt->fetchAll();
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $price = $_POST['price'] ?? '';
    $quantity = $_POST['quantity'] ?? '';
    $desc = trim($_POST['description'] ?? '');
    $catId = $_POST['category_id'] ?? null;
    $subId = $_POST['sub_category_id'] ?? null;

    if ($name === '') {
        $errors[] = 'Name is required.';
    }
    if (!is_numeric($price) || $price < 0) {
        $errors[] = 'Price must be a positive number.';
    }
    if (!is_numeric($quantity) || $quantity < 0) {
        $errors[] = 'Quantity must be a non‑negative integer.';
    }
    if (!is_numeric($subId)) {
        $errors[] = 'Subcategory must be selected.';
    }
    
    if (empty($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'Product image is required.';
    } else {
        $allowedExt = ['jpg','jpeg','png','gif'];
        $file = $_FILES['image'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        if (!in_array($ext, $allowedExt, true)) {
            $errors[] = 'Image extension must be jpg, jpeg, png, or gif.';
        }
        if (!in_array($mime, ['image/jpeg','image/png','image/gif'], true)) {
            $errors[] = 'Uploaded file is not a valid image.';
        }
    }

    if (empty($errors)) {
        
        $stmt = $pdo->prepare("INSERT INTO products (name, price, quantity, description, sub_category_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $price, $quantity, $desc, $subId]);
        $productId = $pdo->lastInsertId();

        
        $targetDir = __DIR__ . '/images';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }
        $newName = uniqid('prod_', true) . '.' . $ext;
        $targetPath = $targetDir . '/' . $newName;
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            
            $relative = 'images/' . $newName;
            $stmt = $pdo->prepare("INSERT INTO images (product_id, path) VALUES (?, ?)");
            $stmt->execute([$productId, $relative]);
        }

        
        $params = '';
        if ($subId) {
            $params = 'sub_category_id=' . (int)$subId;
        } elseif ($catId) {
            $params = 'category_id=' . (int)$catId;
        }
        header('Location: products.php?' . $params);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-light mb-4" style="background: #667eea;">
    <div class="container">
        <a class="navbar-brand text-white" href="index.php">
            <i class="bi bi-shop"></i> E-Commerce
        </a>
    </div>
</nav>
<div class="container">
    <h2 class="mb-4">Add New Product</h2>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $err): ?>
                    <li><?php echo htmlspecialchars($err); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Price (USD)</label>
            <input type="number" step="0.01" name="price" class="form-control" value="<?php echo htmlspecialchars($_POST['price'] ?? ''); ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Quantity</label>
            <input type="number" name="quantity" class="form-control" value="<?php echo htmlspecialchars($_POST['quantity'] ?? ''); ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="4"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Category</label>
                <select name="category_id" id="category" class="form-select" <?php echo $category_id ? 'disabled' : ''; ?>>
                    <option value="">-- select --</option>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?php echo $c['id']; ?>" <?php echo ((isset($_POST['category_id']) && $_POST['category_id']==$c['id']) || ($category_id && $category_id==$c['id'])) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($c['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Subcategory</label>
                <select name="sub_category_id" id="subcategory" class="form-select">
                    <option value="">-- select --</option>
                    <?php foreach ($subcategories as $s): ?>
                        <option value="<?php echo $s['id']; ?>" data-cat="<?php echo $s['category_id']; ?>"
                            <?php echo ((isset($_POST['sub_category_id']) && $_POST['sub_category_id']==$s['id']) || ($subcat_id && $subcat_id==$s['id'])) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($s['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Image (jpg, png, gif)</label>
            <input type="file" name="image" class="form-control" accept="image/*">
        </div>
        <button type="submit" class="btn btn-primary">Save</button>
        <a href="<?php echo $subcat_id ? 'products.php?sub_category_id=' . $subcat_id : ($category_id ? 'products.php?category_id=' . $category_id : 'index.php'); ?>" class="btn btn-secondary ms-2">Cancel</a>
    </form>
</div>

<script>
(function(){
    
    var catSelect = document.getElementById('category');
    var subSelect = document.getElementById('subcategory');
    if (catSelect) {
        catSelect.addEventListener('change', function() {
            var val = this.value;
            Array.from(subSelect.options).forEach(function(opt){
                if (!opt.value) return; 
                if (val === '' || opt.getAttribute('data-cat') === val) {
                    opt.style.display='';
                } else {
                    opt.style.display='none';
                }
            });
            
            if (subSelect.value && subSelect.options[subSelect.selectedIndex].style.display==='none') {
                subSelect.value='';
            }
        });
        
        catSelect.dispatchEvent(new Event('change'));
    }
})();
</script>
</body>
</html>
