<?php
session_start();
include "config/database.php";


if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit;
}
$product_id = (int) $_GET['id'];


$stmt = $pdo->prepare("
    SELECT p.id,
           p.name,
           p.price,
           p.description,
           sc.id   AS sub_category_id,
           sc.name AS sub_category_name,
           c.id    AS category_id,
           c.name  AS category_name
    FROM products p
    LEFT JOIN sub_categories sc ON p.sub_category_id = sc.id
    LEFT JOIN categories c ON sc.category_id = c.id
    WHERE p.id = ?
");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: index.php');
    exit;
}


$stmt = $pdo->prepare("SELECT * FROM images WHERE product_id = ? ORDER BY id");
$stmt->execute([$product_id]);
$images = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?></title>
    <link href="https:
    <link rel="stylesheet" href="https:
<style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
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

        .product-wrapper {
            background: white;
            border-radius: 20px;
            box-shadow: 0 5px 30px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            animation: fadeInUp 0.5s ease-out;
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

        .main-image-area {
            background: var(--primary-gradient);
            min-height: 360px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .main-image-area::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(to bottom right,
                    rgba(255, 255, 255, 0.1) 0%,
                    rgba(255, 255, 255, 0.3) 50%,
                    rgba(255, 255, 255, 0.1) 100%);
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

        .main-image-area img {
            max-height: 360px;
            max-width: 100%;
            object-fit: contain;
            z-index: 1;
            border-radius: 8px;
        }

        .main-image-placeholder {
            z-index: 1;
            color: rgba(255, 255, 255, 0.85);
            font-size: 6rem;
        }

        .thumb-strip {
            display: flex;
            gap: 10px;
            padding: 1rem 1.5rem;
            overflow-x: auto;
            background: #f8f9fa;
            border-top: 1px solid #eee;
        }

        .thumb-strip::-webkit-scrollbar {
            height: 4px;
        }

        .thumb-strip::-webkit-scrollbar-thumb {
            background: #c0c0c0;
            border-radius: 4px;
        }

        .thumb {
            flex-shrink: 0;
            width: 80px;
            height: 80px;
            border-radius: 10px;
            overflow: hidden;
            cursor: pointer;
            border: 3px solid transparent;
            transition: all 0.25s ease;
            background: var(--primary-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .thumb.active,
        .thumb:hover {
            border-color: #667eea;
            box-shadow: 0 3px 10px rgba(102, 126, 234, 0.35);
            transform: translateY(-3px);
        }

        .thumb-placeholder {
            color: rgba(255, 255, 255, 0.8);
            font-size: 1.8rem;
        }

        .info-panel {
            padding: 2rem;
        }

        .product-title {
            font-size: 1.9rem;
            font-weight: 700;
            color: #333;
            line-height: 1.3;
        }

        .category-badge {
            display: inline-block;
            background: var(--secondary-gradient);
            color: white;
            padding: 0.35rem 1.2rem;
            border-radius: 30px;
            font-weight: 600;
            font-size: 0.85rem;
            margin-bottom: 1rem;
            box-shadow: 0 3px 10px rgba(240, 147, 251, 0.3);
        }

        .category-badge i {
            margin-right: 5px;
        }

        .subcategory-badge {
            display: inline-block;
            background: var(--success-gradient);
            color: white;
            padding: 0.3rem 1rem;
            border-radius: 25px;
            font-weight: 500;
            font-size: 0.8rem;
            margin-bottom: 1rem;
            box-shadow: 0 3px 10px rgba(132, 250, 176, 0.3);
        }
        .subcategory-badge i {
            margin-right: 5px;
        }

        .price-container {
            background: #f8f9fa;
            padding: 1.2rem 1.5rem;
            border-radius: 12px;
            margin: 1.5rem 0;
        }

        .price-label {
            font-size: 0.8rem;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .price-value {
            font-size: 2rem;
            font-weight: 700;
            background: var(--price-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1.2;
        }

        .price-value small {
            font-size: 1rem;
            font-weight: 400;
            background: none;
            -webkit-text-fill-color: #888;
        }

        .description-box {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 1.2rem 1.5rem;
            margin-bottom: 1.5rem;
            color: #555;
            line-height: 1.7;
            font-size: 0.97rem;
        }

        .description-box h6 {
            font-weight: 700;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .btn-add-to-cart {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 0.85rem 2rem;
            border: none;
            border-radius: 12px;
            background: var(--primary-gradient);
            color: white;
            font-weight: 600;
            font-size: 1rem;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .btn-add-to-cart:hover {
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.45);
        }

        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 0.75rem 1.5rem;
            border: 2px solid #667eea;
            border-radius: 30px;
            background: white;
            color: #667eea;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .back-button:hover {
            background: #667eea;
            color: white;
            transform: translateX(-5px);
        }

        .img-count-badge {
            position: absolute;
            bottom: 15px;
            right: 15px;
            background: rgba(0, 0, 0, 0.45);
            color: white;
            padding: 0.3rem 0.9rem;
            border-radius: 20px;
            font-size: 0.8rem;
            z-index: 2;
            backdrop-filter: blur(4px);
        }

        .no-image-strip {
            padding: 0.8rem 1.5rem;
            background: #f8f9fa;
            color: #aaa;
            font-size: 0.85rem;
            border-top: 1px solid #eee;
        }

        @media (max-width: 768px) {
            .product-title {
                font-size: 1.5rem;
            }

            .main-image-area {
                min-height: 250px;
            }

            .main-image-placeholder {
                font-size: 4rem;
            }

            .info-panel {
                padding: 1.5rem;
            }
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

    <div class="container mb-5">

        <div class="mb-3">
            <?php if (!empty($product['sub_category_id'])): ?>
            <a href="products.php?sub_category_id=<?php echo $product['sub_category_id']; ?>" class="back-button">
                <i class="bi bi-arrow-left"></i> Back to <?php echo htmlspecialchars($product['sub_category_name']); ?>
            </a>
        <?php elseif (!empty($product['category_id'])): ?>
            <a href="products.php?category_id=<?php echo $product['category_id']; ?>" class="back-button">
                <i class="bi bi-arrow-left"></i> Back to <?php echo htmlspecialchars($product['category_name']); ?>
            </a>
        <?php else: ?>
            <a href="index.php" class="back-button">
                <i class="bi bi-arrow-left"></i> Back to Categories
            </a>
        <?php endif; ?>
        </div>

        <div class="product-wrapper">
            <div class="row g-0">

                <!-- Left: Images -->
                <div class="col-lg-6">
                    <div class="main-image-area" id="mainImageArea">
                        <?php if (!empty($images)): ?>
                            <?php
                            
                            $firstSrc = $images[0]['image_url'] ?? $images[0]['url'] ?? $images[0]['path'] ?? $images[0]['filename'] ?? '';
                            ?>
                            <img id="mainImage"
                                src="<?php echo htmlspecialchars($firstSrc); ?>"
                                alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <span class="img-count-badge">
                                <i class="bi bi-images"></i>
                                <span id="imgCounter">1</span> / <?php echo count($images); ?>
                            </span>
                        <?php else: ?>
                            <i class="bi bi-box-seam main-image-placeholder"></i>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($images)): ?>
                        <div class="thumb-strip">
                            <?php foreach ($images as $idx => $img):
                                $src = $img['image_url'] ?? $img['url'] ?? $img['path'] ?? $img['filename'] ?? '';
                            ?>
                                <div class="thumb <?php echo $idx === 0 ? 'active' : ''; ?>"
                                    onclick="switchImage(this, '<?php echo htmlspecialchars($src); ?>', <?php echo $idx + 1; ?>)">
                                    <img src="<?php echo htmlspecialchars($src); ?>"
                                        alt="Thumbnail <?php echo $idx + 1; ?>"
                                        onerror="this.style.display='none'; this.parentElement.innerHTML+='<i class=\'bi bi-image thumb-placeholder\'></i>'">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="no-image-strip">
                            <i class="bi bi-image me-1"></i> No images available for this product.
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Right: Product Info -->
                <div class="col-lg-6">
                    <div class="info-panel">

                        <?php if (!empty($product['category_name'])): ?>
                            <span class="category-badge">
                                <i class="bi bi-tag"></i> <?php echo htmlspecialchars($product['category_name']); ?>
                            </span>
                        <?php endif; ?>
                        <?php if (!empty($product['sub_category_name'])): ?>
                            <span class="subcategory-badge">
                                <i class="bi bi-tags"></i> <?php echo htmlspecialchars($product['sub_category_name']); ?>
                            </span>
                        <?php endif; ?>

                        <h1 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h1>

                        <div class="price-container">
                            <div class="price-label">Price</div>
                            <div class="price-value">
                                $<?php echo number_format($product['price'], 2); ?>
                                <small>USD</small>
                            </div>
                        </div>

                        <div class="description-box">
                            <h6><i class="bi bi-info-circle me-1"></i> Description</h6>
                            <?php
                            $desc = trim($product['description'] ?? '');
                            echo $desc !== '' ? nl2br(htmlspecialchars($desc)) : '<span class="text-muted">No description available.</span>';
                            ?>
                        </div>

                        <div class="d-flex gap-3 flex-wrap">
                            <a href="cart.php?action=add&id=<?php echo $product['id']; ?>" class="btn-add-to-cart">
                                <i class="bi bi-cart-plus"></i> Add to Cart
                            </a>
                        </div>

                    </div>
                </div>

            </div>
        </div>

    </div>

    <footer class="py-4 text-center text-muted">
        <div class="container">
            <small>&copy; <?php echo date('Y'); ?> E-Commerce. All rights reserved.</small>
        </div>
    </footer>

    <script src="https:
    <script>
        function switchImage(thumbEl, src, index) {
            const mainImg = document.getElementById('mainImage');
            if (mainImg) mainImg.src = src;
            document.getElementById('imgCounter').textContent = index;
            document.querySelectorAll('.thumb').forEach(t => t.classList.remove('active'));
            thumbEl.classList.add('active');
        }
    </script>
</body>

</html>
