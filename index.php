<?php

session_start();
include "config/database.php";


$stmt = $pdo->query("SELECT id, name FROM categories ORDER BY name");
$categories = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop Home</title>
    <link href="https:
    <link rel="stylesheet" href="https:
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --success-gradient: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%);
            --warning-gradient: linear-gradient(135deg, #fccb90 0%, #d57eeb 100%);
        }
        
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        
        .navbar {
            background: var(--primary-gradient) !important;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .navbar-brand {
            color: white !important;
            font-weight: 700;
            font-size: 1.5rem;
            letter-spacing: 1px;
        }
        
        .navbar-nav .nav-link {
            color: rgba(255,255,255,0.9) !important;
            font-weight: 500;
            padding: 0.5rem 1rem !important;
            border-radius: 25px;
            transition: all 0.3s ease;
        }
        
        .navbar-nav .nav-link:hover {
            background: rgba(255,255,255,0.2);
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
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
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
            font-size: 2.5rem;
            font-weight: 700;
            color: #333;
            margin: 0;
            position: relative;
        }
        
        .page-header h1 i {
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-right: 10px;
        }
        
        
        .category-card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            height: 100%;
            background: white;
            position: relative;
        }
        
        .category-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: var(--primary-gradient);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .category-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px rgba(102, 126, 234, 0.2);
        }
        
        .category-card:hover::before {
            opacity: 1;
        }
        
        .category-card .card-body {
            padding: 2rem 1.5rem;
            text-align: center;
        }
        
        .category-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 1.5rem;
            background: var(--primary-gradient);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        
        .category-card:hover .category-icon {
            transform: scale(1.1) rotate(5deg);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }
        
        .category-icon i {
            font-size: 2.5rem;
            color: white;
        }
        
        .category-card .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
            text-transform: capitalize;
        }
        
        .category-card .card-text {
            color: #666;
            font-size: 0.9rem;
            margin: 0;
        }
        
        
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
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
            margin: 0;
        }
        
        
        .category-count {
            position: absolute;
            top: 10px;
            right: 10px;
            background: var(--primary-gradient);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        
        .row {
            margin: -0.75rem;
        }
        
        .col-sm-6, .col-md-4, .col-lg-3 {
            padding: 0.75rem;
        }
        
        
        @keyframes fadeIn {
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
            animation: fadeIn 0.5s ease-out forwards;
            animation-fill-mode: both;
        }
        
        
        <?php for($i = 1; $i <= 12; $i++): ?>
        .col-sm-6:nth-child(<?php echo $i; ?>) {
            animation-delay: <?php echo $i * 0.05; ?>s;
        }
        <?php endfor; ?>
        
        
        @media (max-width: 768px) {
            .page-header h1 {
                font-size: 2rem;
            }
            
            .category-card .card-body {
                padding: 1.5rem 1rem;
            }
            
            .category-icon {
                width: 60px;
                height: 60px;
            }
            
            .category-icon i {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light mb-4">
    <div class="container">
        <a class="navbar-brand" href="/e-commerce/">
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
            <i class="bi bi-grid-3x3-gap-fill"></i>
            Shop by Category
        </h1>
        <p class="text-muted mt-2 mb-0">Browse our collection of categories</p>
    </div>
    
    <div class="row">
        <?php if(empty($categories)): ?>
            <div class="col-12">
                <div class="empty-state">
                    <i class="bi bi-inbox"></i>
                    <p>No categories found.</p>
                </div>
            </div>
        <?php else: ?>
            <?php foreach($categories as $index => $cat): ?>
                <div class="col-sm-6 col-md-4 col-lg-3 mb-4">
                    <!-- navigate to subcategories page instead of directly to products -->
                    <a href="subcategories.php?category_id=<?php echo $cat['id']; ?>" class="text-decoration-none text-dark">
                        <div class="card category-card">
                            <span class="category-count"><?php echo $index + 1; ?></span>
                            <div class="card-body">
                               
                                <h5 class="card-title"><?php echo htmlspecialchars($cat['name']); ?></h5>
                                <p class="card-text">Browse sub‑categories</p>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
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
