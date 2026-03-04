<?php
session_start();
include "config/database.php";


$category     = null;
$subcat       = null;
$products     = [];
$category_id  = null;
$subcat_id    = null;

if (isset($_GET['sub_category_id']) && is_numeric($_GET['sub_category_id'])) {
    
    $subcat_id = (int) $_GET['sub_category_id'];

    
    $stmt = $pdo->prepare("SELECT sc.name, sc.category_id, c.name AS category_name
                          FROM sub_categories sc
                          LEFT JOIN categories c ON sc.category_id = c.id
                          WHERE sc.id = ?");
    $stmt->execute([$subcat_id]);
    $subcat = $stmt->fetch();
    if (!$subcat) {
        header('Location: index.php');
        exit;
    }
    $category_id = $subcat['category_id'];

    
    $stmt = $pdo->prepare("SELECT id, name, price, quantity FROM products WHERE sub_category_id = ? ORDER BY name");
    $stmt->execute([$subcat_id]);
    $products = $stmt->fetchAll();
} elseif (isset($_GET['category_id']) && is_numeric($_GET['category_id'])) {
    
    $category_id = (int) $_GET['category_id'];

    
    $stmt = $pdo->prepare("SELECT name FROM categories WHERE id = ?");
    $stmt->execute([$category_id]);
    $category = $stmt->fetch();
    if (!$category) {
        header('Location: index.php');
        exit;
    }

    
    $stmt = $pdo->prepare(
        "SELECT p.id, p.name, p.price
         FROM products p
         JOIN sub_categories sc ON p.sub_category_id = sc.id
         WHERE sc.category_id = ?
         ORDER BY p.name"
    );
    $stmt->execute([$category_id]);
    $products = $stmt->fetchAll();
} else {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products in <?php echo htmlspecialchars($category['name']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --success-gradient: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%);
            --price-gradient: linear-gradient(135deg, #ff6b6b 0%, #ff8e53 100%);
        }

        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        
        .navbar {
            background: var(--primary-gradient) !important;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            color: white !important;
            font-weight: 700;
            font-size: 1.5rem;
            letter-spacing: 1px;
        }

        .navbar-brand i {
            margin-right: 8px;
        }

        .navbar-nav .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
            padding: 0.5rem 1rem !important;
            border-radius: 25px;
            transition: all 0.3s ease;
        }

        .navbar-nav .nav-link:hover {
            background: rgba(255, 255, 255, 0.2);
            color: white !important;
            transform: translateY(-2px);
        }

        .navbar-nav .nav-link i {
            margin-right: 5px;
        }

        
        .page-header {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }

        .page-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: var(--primary-gradient);
        }

        .page-header h1 {
            font-size: 2.2rem;
            font-weight: 700;
            color: #333;
            margin: 0;
        }

        .page-header h1 i {
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-right: 10px;
        }

        .category-badge {
            display: inline-block;
            background: var(--secondary-gradient);
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 30px;
            font-weight: 600;
            margin-top: 1rem;
            box-shadow: 0 3px 10px rgba(240, 147, 251, 0.3);
        }

        .category-badge i {
            margin-right: 5px;
        }

        .subcategory-badge {
            display: inline-block;
            background: var(--success-gradient);
            color: white;
            padding: 0.4rem 1.2rem;
            border-radius: 25px;
            font-weight: 500;
            margin-top: 1rem;
            box-shadow: 0 3px 10px rgba(132, 250, 176, 0.3);
        }

        .subcategory-badge i {
            margin-right: 5px;
        }

        
        .product-card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            height: 100%;
            background: white;
            position: relative;
        }

        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px rgba(102, 126, 234, 0.15);
        }

        .product-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: var(--secondary-gradient);
            color: white;
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            z-index: 1;
            box-shadow: 0 3px 10px rgba(240, 147, 251, 0.3);
        }

        .product-badge i {
            margin-right: 3px;
            font-size: 0.7rem;
        }

        .product-image {
            height: 180px;
            background: var(--primary-gradient);
            opacity: 0.9;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .product-image::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(to bottom right,
                    rgba(255, 255, 255, 0.2) 0%,
                    rgba(255, 255, 255, 0.2) 30%,
                    rgba(255, 255, 255, 0.4) 50%,
                    rgba(255, 255, 255, 0.2) 70%,
                    rgba(255, 255, 255, 0.2) 100%);
            transform: rotate(45deg);
            animation: shimmer 3s infinite;
        }

        @keyframes shimmer {
            0% {
                transform: translateX(-100%) rotate(45deg);
            }

            100% {
                transform: translateX(100%) rotate(45deg);
            }
        }

        .product-image i {
            font-size: 4rem;
            color: rgba(255, 255, 255, 0.9);
            z-index: 1;
        }

        .product-card .card-body {
            padding: 1.5rem;
        }

        .product-card .card-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 1rem;
            line-height: 1.4;
            height: 2.8rem;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        .price-container {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 10px;
            margin: 1rem 0;
        }

        .price-label {
            font-size: 0.85rem;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .price-value {
            font-size: 1.5rem;
            font-weight: 700;
            background: var(--price-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1.2;
        }

        .price-value small {
            font-size: 0.9rem;
            font-weight: 400;
            background: none;
            -webkit-text-fill-color: #666;
        }

        .btn-add-to-cart {
            width: 100%;
            padding: 0.75rem;
            border: none;
            border-radius: 10px;
            background: var(--primary-gradient);
            color: white;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-add-to-cart:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-add-to-cart:active {
            transform: translateY(0);
        }

        .btn-add-to-cart i {
            margin-right: 8px;
            transition: transform 0.3s ease;
        }

        .btn-add-to-cart:hover i {
            transform: translateX(3px);
        }

        .btn-show {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 0.75rem 1rem;
            border-radius: 10px;
            background: #f8f9fa;
            color: #667eea;
            font-weight: 600;
            text-decoration: none;
            border: 2px solid #667eea;
            transition: all 0.3s ease;
            white-space: nowrap;
        }

        .btn-show:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
        }

        
        .back-button {
            display: inline-flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            border: 2px solid transparent;
            border-radius: 30px;
            background: white;
            color: #667eea;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            margin-top: 2rem;
        }

        .back-button:hover {
            border-color: #667eea;
            background: transparent;
            color: #667eea;
            transform: translateX(-5px);
        }

        .back-button i {
            margin-right: 8px;
            transition: transform 0.3s ease;
        }

        .back-button:hover i {
            transform: translateX(-3px);
        }

        
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        }

        .empty-state i {
            font-size: 4rem;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1rem;
        }

        .empty-state p {
            font-size: 1.2rem;
            color: #666;
            margin-bottom: 2rem;
        }

        .empty-state .btn {
            display: inline-flex;
            align-items: center;
            padding: 0.75rem 2rem;
            border-radius: 30px;
            background: var(--primary-gradient);
            color: white;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .empty-state .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .empty-state .btn i {
            margin-right: 8px;
            background: none;
            -webkit-text-fill-color: white;
            font-size: 1rem;
        }

        
        .row {
            margin: -0.75rem;
        }

        .col-sm-6,
        .col-md-4,
        .col-lg-3 {
            padding: 0.75rem;
        }

        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .col-sm-6 {
            animation: fadeInUp 0.5s ease-out forwards;
            animation-fill-mode: both;
        }

        
        <?php for ($i = 1; $i <= 12; $i++): ?>.col-sm-6:nth-child(<?php echo $i; ?>) {
            animation-delay: <?php echo $i * 0.05; ?>s;
        }

        <?php endfor; ?>
        
        @media (max-width: 768px) {
            .page-header h1 {
                font-size: 1.8rem;
            }

            .product-image {
                height: 150px;
            }

            .product-image i {
                font-size: 3rem;
            }

            .price-value {
                font-size: 1.3rem;
            }
        }

        
        .product-count {
            display: inline-block;
            background: rgba(102, 126, 234, 0.1);
            color: #667eea;
            padding: 0.25rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            margin-left: 1rem;
        }

        .product-count i {
            margin-right: 5px;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light mb-4">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="bi bi-shop"></i> E-Commerce
            </a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <?php if (isset($_SESSION['user_name'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="bi bi-person-circle"></i> Hello, <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="Auth/logout.php">
                                <i class="bi bi-box-arrow-right"></i> Logout
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="Auth/login.php">
                                <i class="bi bi-box-arrow-in-right"></i> Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="Auth/register.php">
                                <i class="bi bi-person-plus"></i> Register
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="page-header">
            <h1>
                <i class="bi bi-box-seam"></i>
                <?php
                if ($subcat) {
                    echo htmlspecialchars($subcat['name']);
                } elseif ($category) {
                    echo htmlspecialchars($category['name']);
                }
                ?> Products
            </h1>
            <div class="d-flex align-items-center flex-wrap gap-2">
                <?php if ($category): ?>
                    <span class="category-badge">
                        <i class="bi bi-tag"></i> Category: <?php echo htmlspecialchars($category['name']); ?>
                    </span>
                <?php endif; ?>
                <?php if ($subcat): ?>
                    <span class="subcategory-badge">
                        <i class="bi bi-tags"></i> Subcategory: <?php echo htmlspecialchars($subcat['name']); ?>
                    </span>
                <?php endif; ?>

                <?php if (isset($_SESSION['user_name'])): ?>
                    <a href="add_product.php?<?php echo $subcat ? 'sub_category_id='.$subcat_id : 'category_id='.$category_id; ?>" class="btn btn-success ms-auto">
                        <i class="bi bi-plus-circle"></i> Add Product
                    </a>
                <?php else: ?>
                    <a href="Auth/login.php" class="btn btn-outline-secondary ms-auto" title="You must log in to add products">
                        <i class="bi bi-person-circle"></i> Login to add
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <?php if (empty($products)): ?>
            <div class="empty-state">
                <i class="bi bi-archive"></i>
                <p>No products found<?php
                                    if ($subcat) {
                                        echo ' in this subcategory.';
                                    } elseif ($category) {
                                        echo ' in this category.';
                                    }
                                    ?></p>
                <?php if ($subcat): ?>
                    <a href="subcategories.php?category_id=<?php echo $category_id; ?>" class="btn">
                        <i class="bi bi-arrow-left"></i> Back to subcategories
                    </a>
                <?php elseif ($category): ?>
                    <a href="index.php" class="btn">
                        <i class="bi bi-arrow-left"></i> Browse other categories
                    </a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($products as $index => $prod): ?>
                    <?php if ($prod['quantity'] <= 0) continue; ?>
                    <div class="col-sm-6 col-md-4 col-lg-3 mb-4">
                        <div class="card product-card">
                            <span class="product-badge">
                                <i class="bi bi-star-fill"></i> New
                            </span>
                            <div class="product-image">
                                <?php
                                    $stmt = $pdo->prepare("SELECT path FROM images WHERE product_id = ? LIMIT 1");
                                    $stmt->execute([$prod['id']]);
                                    $img = $stmt->fetch();
                                    if ($img && !empty($img['path'])):
                                ?>
                                    <img src="<?php echo htmlspecialchars($img['path']); ?>" alt="<?php echo htmlspecialchars($prod['name']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                <?php else: ?>
                                    <i class="bi bi-box-seam"></i>
                                <?php endif; ?>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?php echo htmlspecialchars($prod['name']); ?></h5>
                                <div class="price-container">
                                    <div class="price-label">Price</div>
                                    <div class="price-value">
                                        $<?php echo number_format($prod['price'], 2); ?>
                                        <small>USD</small>
                                    </div>
                                </div>
                                <div class="d-flex gap-2 mt-2">
                                    <a href="cart.php?action=add&id=<?php echo $prod['id']; ?>" class="btn-add-to-cart flex-grow-1">
                                        <i class="bi bi-cart-plus"></i> Add to Cart
                                    </a>
                                    <a href="show.php?id=<?php echo $prod['id']; ?>" class="btn-show">
                                        <i class="bi bi-eye"></i> Show
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if ($subcat): ?>
                <a href="subcategories.php?category_id=<?php echo $category_id; ?>" class="back-button">
                    <i class="bi bi-arrow-left"></i> Back to Subcategories
                </a>
            <?php else: ?>
                <a href="index.php" class="back-button">
                    <i class="bi bi-arrow-left"></i> Back to Categories
                </a>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <!-- Optional: Add a footer -->
    <footer class="mt-5 py-4 text-center text-muted">
        <div class="container">
            <small>&copy; <?php echo date('Y'); ?> E-Commerce. All rights reserved.</small>
        </div>
    </footer>

    <script src="https:
</body>

</html>
