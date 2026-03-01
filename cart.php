<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
include "config/database.php";


function getCart(): array {
    if (isset($_COOKIE['cart'])) {
        $cart = json_decode($_COOKIE['cart'], true);
        if (!is_array($cart)) return [];
        return array_combine(
            array_map('intval', array_keys($cart)),
            array_values($cart)
        );
    }
    return [];
}

function saveCart(array $cart): void {
    setcookie('cart', json_encode($cart), time() + (7 * 24 * 60 * 60), '/');
}


$action = $_GET['action'] ?? '';

if ($action === 'add' && isset($_GET['id'])) {
    $id   = (int) $_GET['id'];
    $cart = getCart();
    if ($id > 0) {
        $cart[$id] = ($cart[$id] ?? 0) + 1;
        saveCart($cart);
    }
    header('Location: cart.php');
    exit;
}

if ($action === 'remove' && isset($_GET['id'])) {
    $id   = (int) $_GET['id'];
    $cart = getCart();
    unset($cart[$id]);
    saveCart($cart);
    header('Location: cart.php');
    exit;
}

if ($action === 'clear') {
    saveCart([]);
    header('Location: cart.php');
    exit;
}

if ($action === 'update' && isset($_GET['id'], $_GET['qty'])) {
    $id   = (int) $_GET['id'];
    $qty  = (int) $_GET['qty'];
    $cart = getCart();
    if ($qty <= 0) {
        unset($cart[$id]);
    } else {
        $cart[$id] = $qty;
    }
    saveCart($cart);
    header('Location: cart.php');
    exit;
}

if ($action === 'checkout') {
    if (!isset($_SESSION['user_id'])) {
        header('Location: Auth/login.php?redirect=checkout.php');
        exit;
    }

    $cart = getCart();

    if (empty($cart)) {
        header('Location: cart.php');
        exit;
    }

    header('Location: checkout.php');
    exit;
}

$cart  = getCart();
$items = [];
$total = 0;

if (!empty($cart)) {
    $placeholders = implode(',', array_fill(0, count($cart), '?'));
    $stmt = $pdo->prepare("SELECT id, name, price FROM products WHERE id IN ($placeholders)");
    $stmt->execute(array_keys($cart));
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    
    if ($action === 'checkout') {
        if (!isset($_SESSION['user_id'])) {
            header('Location: Auth/login.php?redirect=checkout.php');
            exit;
        }

        $cart = getCart();

        if (empty($cart)) {
            header('Location: cart.php');
            exit;
        }

        
        header('Location: checkout.php');
        exit;
    }
    <link href="https:
    <link rel="stylesheet" href="https:
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --price-gradient: linear-gradient(135deg, #ff6b6b 0%, #ff8e53 100%);
        }

        body { background: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }

        .navbar {
            background: var(--primary-gradient) !important;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .navbar-brand { color: white !important; font-weight: 700; font-size: 1.5rem; }
        .navbar-brand i { margin-right: 8px; }
        .navbar-nav .nav-link {
            color: rgba(255,255,255,0.9) !important;
            font-weight: 500; padding: 0.5rem 1rem !important;
            border-radius: 25px; transition: all 0.3s ease;
        }
        .navbar-nav .nav-link:hover { background: rgba(255,255,255,0.2); color: white !important; }
        .navbar-nav .nav-link i { margin-right: 5px; }

        .cart-wrapper {
            background: white;
            border-radius: 20px;
            box-shadow: 0 5px 30px rgba(0,0,0,0.08);
            overflow: hidden;
        }

        .cart-header {
            background: var(--primary-gradient);
            padding: 1.5rem 2rem;
            color: white;
        }
        .cart-header h1 { font-size: 1.8rem; font-weight: 700; margin: 0; }

        .table th { font-weight: 600; color: #555; border-bottom: 2px solid #eee; }
        .table td { vertical-align: middle; color: #333; }

        .price-text {
            font-weight: 700;
            background: var(--price-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .qty-controls {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .qty-btn {
            width: 30px; height: 30px;
            border-radius: 50%;
            border: 2px solid #667eea;
            background: white;
            color: #667eea;
            font-weight: 700;
            display: flex; align-items: center; justify-content: center;
            text-decoration: none;
            transition: all 0.2s ease;
            font-size: 1rem;
            line-height: 1;
        }
        .qty-btn:hover { background: #667eea; color: white; }

        .btn-remove {
            background: none;
            border: none;
            color: #ff6b6b;
            font-size: 1.1rem;
            cursor: pointer;
            padding: 0.3rem 0.5rem;
            border-radius: 8px;
            transition: all 0.2s ease;
            text-decoration: none;
        }
        .btn-remove:hover { background: #fff0f0; color: #e53e3e; }

        .total-row th { font-size: 1.1rem; color: #333; }

        .btn-checkout {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 0.85rem 2.5rem;
            border-radius: 12px;
            background: var(--primary-gradient);
            color: white; font-weight: 600; font-size: 1rem;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102,126,234,0.3);
            border: none;
        }
        .btn-checkout:hover {
            color: white; transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102,126,234,0.45);
        }

        .btn-clear {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 0.85rem 1.5rem;
            border-radius: 12px;
            background: white; color: #ff6b6b;
            font-weight: 600; font-size: 1rem;
            text-decoration: none;
            border: 2px solid #ff6b6b;
            transition: all 0.3s ease;
        }
        .btn-clear:hover { background: #ff6b6b; color: white; }

        .btn-continue {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 0.85rem 1.5rem;
            border-radius: 12px;
            background: white; color: #667eea;
            font-weight: 600; font-size: 1rem;
            text-decoration: none;
            border: 2px solid #667eea;
            transition: all 0.3s ease;
        }
        .btn-continue:hover { background: #667eea; color: white; }

        .empty-cart {
            text-align: center;
            padding: 4rem 2rem;
        }
        .empty-cart i {
            font-size: 5rem;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: block; margin-bottom: 1rem;
        }
        .empty-cart p { font-size: 1.2rem; color: #888; }

        .cookie-notice {
            background: rgba(102,126,234,0.08);
            border: 1px solid rgba(102,126,234,0.2);
            border-radius: 10px;
            padding: 0.6rem 1rem;
            font-size: 0.85rem;
            color: #667eea;
            margin-bottom: 1rem;
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

    <div class="cookie-notice">
        <i class="bi bi-info-circle me-1"></i>
        Your cart is saved in a cookie and will persist for 7 days even if you close the browser.
    </div>

    <div class="cart-wrapper">
        <div class="cart-header">
            <h1><i class="bi bi-cart3 me-2"></i> Your Cart
                <?php if (!empty($items)): ?>
                    <span style="font-size:1rem; font-weight:400; opacity:0.85;">
                        (<?php echo array_sum($cart); ?> items)
                    </span>
                <?php endif; ?>
            </h1>
        </div>

        <div class="p-4">
            <?php if (empty($items)): ?>
                <div class="empty-cart">
                    <i class="bi bi-cart-x"></i>
                    <p>Your cart is empty.</p>
                    <a href="index.php" class="btn-continue">
                        <i class="bi bi-arrow-left"></i> Start Shopping
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item):
                                $qty      = $cart[(int)$item['id']] ?? 1;
                                $subtotal = $item['price'] * $qty;
                                $total   += $subtotal;
                            ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($item['name']); ?></strong></td>
                                <td>$<?php echo number_format($item['price'], 2); ?></td>
                                <td>
                                    <div class="qty-controls">
                                        <a href="cart.php?action=update&id=<?php echo $item['id']; ?>&qty=<?php echo $qty - 1; ?>" class="qty-btn">−</a>
                                        <span><?php echo $qty; ?></span>
                                        <a href="cart.php?action=update&id=<?php echo $item['id']; ?>&qty=<?php echo $qty + 1; ?>" class="qty-btn">+</a>
                                    </div>
                                </td>
                                <td><span class="price-text">$<?php echo number_format($subtotal, 2); ?></span></td>
                                <td>
                                    <a href="cart.php?action=remove&id=<?php echo $item['id']; ?>" class="btn-remove" title="Remove">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="total-row">
                                <th colspan="3" class="text-end">Total:</th>
                                <th><span class="price-text">$<?php echo number_format($total, 2); ?></span></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="d-flex gap-3 flex-wrap mt-3">
                    <a href="cart.php?action=checkout" class="btn-checkout">
                        <i class="bi bi-bag-check"></i> Checkout
                    </a>
                    <a href="cart.php?action=clear" class="btn-clear">
                        <i class="bi bi-trash"></i> Clear Cart
                    </a>
                    <a href="index.php" class="btn-continue">
                        <i class="bi bi-arrow-left"></i> Continue Shopping
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<footer class="py-4 text-center text-muted">
    <div class="container">
        <small>&copy; <?php echo date('Y'); ?> E-Commerce. All rights reserved.</small>
    </div>
</footer>

<script src="https:
</body>
</html>
