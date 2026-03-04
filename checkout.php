<?php
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

if (!isset($_SESSION['user_id'])) {
    header('Location: Auth/login.php?redirect=checkout.php');
    exit;
}

$cart = getCart();
if (empty($cart)) {
    header('Location: cart.php');
    exit;
}

$errors = [];
$insufficient = [];
$success = false;
$order_id = null;


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_method = $_POST['payment_method'] ?? '';

    if (empty($payment_method)) {
        $errors[] = 'Please select a payment method.';
    } else {
        
        $placeholders = implode(',', array_fill(0, count($cart), '?'));
        $stmt = $pdo->prepare("SELECT id, name, price, quantity FROM products WHERE id IN ($placeholders) FOR UPDATE");
        $stmt->execute(array_keys($cart));
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        
        foreach ($products as $p) {
            $id = (int)$p['id'];
            $want = $cart[$id] ?? 0;
            $avail = (int)$p['quantity'];
            if ($want > $avail) {
                $insufficient[] = [
                    'id' => $id,
                    'name' => $p['name'],
                    'want' => $want,
                    'avail' => $avail,
                ];
            }
        }

        if (!empty($insufficient)) {
            $errors[] = 'Some products do not have enough stock.';
        } else {
            
            $total = 0;
            foreach ($products as $p) {
                $id = (int)$p['id'];
                $qty = $cart[$id] ?? 0;
                $total += $p['price'] * $qty;
            }

            try {
                $pdo->beginTransaction();

                
                $stmt = $pdo->prepare("INSERT INTO orders (user_id, total, created_at) VALUES (?, ?, NOW())");
                $stmt->execute([$_SESSION['user_id'], $total]);
                $order_id = $pdo->lastInsertId();

                
                $insertItem = $pdo->prepare("INSERT INTO order_items (order_id, product_id) VALUES (?, ?)");
                $updateStock = $pdo->prepare("UPDATE products SET quantity = quantity - ? WHERE id = ?");

                foreach ($products as $p) {
                    $id = (int)$p['id'];
                    $qty = $cart[$id] ?? 0;
                    if ($qty <= 0) continue;
                    for ($i = 0; $i < $qty; $i++) {
                        $insertItem->execute([$order_id, $id]);
                    }
                    $updateStock->execute([$qty, $id]);
                }

                $pdo->commit();

                
                saveCart([]);
                $success = true;
            } catch (Exception $e) {
                $pdo->rollBack();
                error_log('Checkout (checkout.php) error: ' . $e->getMessage());
                $errors[] = 'An error occurred while processing your order. Please try again.';
            }
        }
    }
}


$placeholders = implode(',', array_fill(0, count($cart), '?'));
$stmt = $pdo->prepare("SELECT id, name, price, quantity FROM products WHERE id IN ($placeholders)");
$stmt->execute(array_keys($cart));
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);


$total = 0;
foreach ($products as $p) {
    $id = (int)$p['id'];
    $qty = $cart[$id] ?? 0;
    $total += $p['price'] * $qty;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .box { background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 8px 30px rgba(0,0,0,0.05); }
        .price { font-weight: 700; color: #333; }
        .insufficient { background: #fff4f4; border: 1px solid #ffd6d6; padding: 0.75rem; border-radius: 8px; }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="box mx-auto" style="max-width:900px;">
        <?php if ($success): ?>
            <div class="text-center">
                <i class="bi bi-check2-circle" style="font-size:4rem;color:#28a745"></i>
                <h2 class="mt-3">Order Placed Successfully!</h2>
                <p>Your order <strong>#<?php echo htmlspecialchars($order_id); ?></strong> has been received.</p>
                <a href="index.php" class="btn btn-primary mt-3">Continue Shopping</a>
            </div>
        <?php else: ?>
            <h2>Checkout</h2>

            <?php if ($errors): ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $err) echo '<div>' . htmlspecialchars($err) . '</div>'; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($insufficient)): ?>
                <div class="insufficient mb-3">
                    <strong>Insufficient stock for:</strong>
                    <ul class="mb-0">
                        <?php foreach ($insufficient as $it): ?>
                            <li><?php echo htmlspecialchars($it['name']); ?> — requested <?php echo $it['want']; ?>, available <?php echo $it['avail']; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-7">
                    <h5>Items</h5>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $p):
                                $id = (int)$p['id'];
                                $qty = $cart[$id] ?? 0;
                                $subtotal = $p['price'] * $qty;
                            ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($p['name']); ?></td>
                                    <td>$<?php echo number_format($p['price'],2); ?></td>
                                    <td><?php echo $qty; ?></td>
                                    <td>$<?php echo number_format($subtotal,2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="col-md-5">
                    <h5>Payment & Summary</h5>
                    <div class="mb-3">
                        <strong>Total:</strong>
                        <div class="price">$<?php echo number_format($total,2); ?></div>
                    </div>

                    <form method="post">
                        <div class="mb-3">
                            <label class="form-label"><strong>Choose payment method</strong></label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="pm_card" value="card" <?php echo (isset($_POST['payment_method']) && $_POST['payment_method']==='card')?'checked':''; ?>>
                                <label class="form-check-label" for="pm_card">Credit/Debit Card</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="pm_paypal" value="paypal" <?php echo (isset($_POST['payment_method']) && $_POST['payment_method']==='paypal')?'checked':''; ?>>
                                <label class="form-check-label" for="pm_paypal">PayPal</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="pm_cod" value="cod" <?php echo (isset($_POST['payment_method']) && $_POST['payment_method']==='cod')?'checked':''; ?>>
                                <label class="form-check-label" for="pm_cod">Cash on Delivery</label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success">Confirm & Pay</button>
                        <a href="cart.php" class="btn btn-secondary ms-2">Back to Cart</a>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
