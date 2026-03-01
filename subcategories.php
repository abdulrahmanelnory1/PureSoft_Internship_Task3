<?php

session_start();
include "config/database.php";


if (!isset($_GET['category_id']) || !is_numeric($_GET['category_id'])) {
    header('Location: index.php');
    exit;
}
$category_id = (int) $_GET['category_id'];


$stmt = $pdo->prepare("SELECT name FROM categories WHERE id = ?");
$stmt->execute([$category_id]);
$category = $stmt->fetch();
if (!$category) {
    header('Location: index.php');
    exit;
}


$stmt = $pdo->prepare("SELECT id, name FROM sub_categories WHERE category_id = ? ORDER BY name");
$stmt->execute([$category_id]);
$subcategories = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subcategories of <?php echo htmlspecialchars($category['name']); ?></title>
    <link href="https:
    <link rel="stylesheet" href="https:
    <style>
        
        :root{
            --primary-1: #667eea;
            --primary-2: #764ba2;
            --muted: #6c757d;
            --card-bg: #ffffff;
            --glass: rgba(255,255,255,0.6);
        }
        html,body{height:100%;}
        body {
            background: linear-gradient(180deg,#f6f8ff 0%, #f8f9fa 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            -webkit-font-smoothing:antialiased;
        }

        .navbar { background: linear-gradient(135deg,var(--primary-1) 0%, var(--primary-2) 100%) !important; box-shadow: 0 6px 30px rgba(102,126,234,0.12); }
        .navbar-brand { color: white !important; font-weight: 700; font-size: 1.4rem; letter-spacing: 0.6px; }
        .navbar-nav .nav-link { color: rgba(255,255,255,0.95) !important; font-weight: 600; padding: 0.45rem 0.9rem !important; border-radius: 20px; transition: all 0.18s ease; }
        .navbar-nav .nav-link:hover { background: rgba(255,255,255,0.08); transform: translateY(-2px); }

        .page-header {
            background: linear-gradient(180deg,var(--card-bg) 60%, rgba(255,255,255,0.85) 100%);
            padding: 2rem;
            border-radius: 14px;
            box-shadow: 0 8px 30px rgba(28,38,84,0.04);
            margin-bottom: 1.5rem;
            position: relative;
            overflow: hidden;
        }
        .page-header::before { content: ''; position: absolute; top: -10px; left: -40px; width: 220px; height: 220px; background: radial-gradient(circle at 30% 30%, rgba(102,126,234,0.12), transparent 30%); transform: rotate(12deg); }
        .page-header h1 { font-size: 2.2rem; font-weight: 700; color: #1f2937; margin: 0; }

        .row { margin: -0.9rem; }
        .col-sm-6, .col-md-4, .col-lg-3 { padding: 0.9rem; }

        .category-card {
            border: 0; border-radius: 12px; overflow: hidden; background: linear-gradient(180deg, rgba(255,255,255,0.8), rgba(255,255,255,0.95));
            transition: transform 0.28s cubic-bezier(.2,.9,.3,1), box-shadow 0.28s; position:relative; min-height:180px; display:flex; align-items:center;
            box-shadow: 0 6px 18px rgba(31,41,55,0.06);
        }
        .category-card:hover { transform: translateY(-10px); box-shadow: 0 18px 40px rgba(31,41,55,0.12); }

        .category-icon { width: 84px; height: 84px; margin: 0 auto 1rem; background: linear-gradient(135deg,var(--primary-1) 0%, var(--primary-2) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: transform 0.28s; box-shadow: 0 6px 20px rgba(118,75,162,0.15); }
        .category-card:hover .category-icon { transform: scale(1.06) rotate(-6deg); }
        .category-icon i { font-size: 2.2rem; color: white; }

        .category-card .card-body { padding: 1.6rem 1.2rem; text-align: center; }
        .category-card .card-title { font-size: 1.125rem; font-weight: 700; color: #111827; margin-bottom: 0.35rem; text-transform: capitalize; }
        .category-card .card-text { color: var(--muted); font-size: 0.92rem; margin: 0; }

        .category-count { position:absolute; top:12px; left:12px; display:inline-block; background: rgba(102,126,234,0.09); color: var(--primary-2); font-weight:700; padding:6px 10px; border-radius:10px; font-size:0.85rem; }

        .empty-state { text-align: center; padding: 3rem 2rem; background: linear-gradient(180deg, rgba(255,255,255,0.9), rgba(255,255,255,0.98)); border-radius: 12px; box-shadow: 0 10px 30px rgba(2,6,23,0.04); }
        .empty-state i { font-size: 3.6rem; background: linear-gradient(135deg,var(--primary-1) 0%, var(--primary-2) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin-bottom: 0.8rem; }
        .empty-state p { font-size: 1.05rem; color: var(--muted); margin: 0 0 1rem 0; }

        .back-button { display: inline-flex; align-items: center; gap:8px; padding: 0.6rem 1rem; border-radius: 10px; background: transparent; color: var(--primary-1); font-weight: 700; text-decoration: none; border: 2px solid rgba(102,126,234,0.12); transition: all .18s ease; }
        .back-button:hover { background: linear-gradient(135deg,var(--primary-1),var(--primary-2)); color: #fff; transform: translateX(-6px); box-shadow: 0 10px 30px rgba(102,126,234,0.14); }

        .back-to-products { display:inline-block; padding: 0.6rem 1rem; border-radius: 10px; background: linear-gradient(135deg,var(--primary-1),var(--primary-2)); color: white; text-decoration:none; font-weight:700; }
        .back-to-products:hover { opacity:0.95; transform: translateY(-2px); }

        @media (max-width: 768px) {
            .page-header h1 { font-size: 1.6rem; }
            .category-icon { width: 66px; height: 66px; }
            .category-icon i { font-size: 1.6rem; }
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
                <?php if(isset($_SESSION['user_name'])): ?>
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
            <i class="bi bi-grid-1x2-fill"></i>
            Subcategories of <?php echo htmlspecialchars($category['name']); ?>
        </h1>
    </div>

    <div class="row">
        <?php if(empty($subcategories)): ?>
            <div class="col-12">
                <div class="empty-state">
                    <i class="bi bi-inbox"></i>
                    <p>No subcategories found.</p>
                    <a href="products.php?category_id=<?php echo $category_id; ?>" class="back-to-products">
                        <i class="bi bi-box-seam"></i> Browse products in this category
                    </a>
                </div>
            </div>
        <?php else: ?>
            <?php foreach($subcategories as $index => $sub): ?>
                <div class="col-sm-6 col-md-4 col-lg-3 mb-4">
                    <a href="products.php?sub_category_id=<?php echo $sub['id']; ?>" class="text-decoration-none text-dark">
                        <div class="card category-card">
                            <span class="category-count"><?php echo $index + 1; ?></span>
                            <div class="card-body">
                                <div class="category-icon">
                                    <?php
                                    $icons = ['bi-bag', 'bi-phone', 'bi-laptop', 'bi-book', 'bi-camera', 'bi-watch', 'bi-gem', 'bi-cup', 'bi-controller', 'bi-headphones'];
                                    $icon = $icons[$index % count($icons)];
                                    ?>
                                    <i class="bi <?php echo $icon; ?>"></i>
                                </div>
                                <h5 class="card-title"><?php echo htmlspecialchars($sub['name']); ?></h5>
                                <p class="card-text">Explore products</p>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <a href="index.php" class="back-button">
        <i class="bi bi-arrow-left"></i> Back to Categories
    </a>
</div>

<footer class="mt-5 py-4 text-center text-muted">
    <div class="container">
        <small>&copy; <?php echo date('Y'); ?> E-Commerce. All rights reserved.</small>
    </div>
</footer>

<script src="https:
</body>
</html>
