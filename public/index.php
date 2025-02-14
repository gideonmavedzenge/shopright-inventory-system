<?php
require_once __DIR__ . '/../vendor/autoload.php';

use ShopRight\CacheService;
use ShopRight\InventoryManager;
use ShopRight\LogService;
use ShopRight\NotificationService;
use ShopRight\OrderProcessor;

session_start();

$cacheService = new CacheService();
$inventoryManager = new InventoryManager($cacheService);
$logService = new LogService();
$notificationService = new NotificationService();
$orderProcessor = new OrderProcessor($inventoryManager, $logService, $notificationService);

$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = (int) ($_POST['product_id'] ?? 0);
    $quantity  = (int) ($_POST['quantity'] ?? 0);
    $message = $orderProcessor->processOrder($productId, $quantity);
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>ShopRight Inventory Management</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        margin: 20px;
    }

    .notification {
        background: #f8d7da;
        padding: 10px;
        margin: 10px 0;
        border: 1px solid #f5c6cb;
    }

    .order-log {
        background: #d4edda;
        padding: 10px;
        margin: 10px 0;
        border: 1px solid #c3e6cb;
    }
    </style>
</head>

<body>
    <h1>ShopRight Inventory Management</h1>
    <?php if($message): ?>
    <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <h2>Place an Order</h2>
    <form method="post" action="">
        <label for="product_id">Product ID:</label>
        <input type="number" name="product_id" required>
        <br><br>
        <label for="quantity">Quantity:</label>
        <input type="number" name="quantity" required>
        <br><br>
        <button type="submit">Process Order</button>
    </form>

    <h2>Low Stock Notifications</h2>
    <?php
    if (isset($_SESSION['notifications']) && count($_SESSION['notifications']) > 0) {
        foreach ($_SESSION['notifications'] as $note) {
            echo '<div class="notification">' . htmlspecialchars($note) . '</div>';
        }
    } else {
        echo '<p>No notifications.</p>';
    }
    ?>

    <h2>Order Log</h2>
    <?php
    $ordersFile = __DIR__ . '/../data/orders.json';
    if (file_exists($ordersFile)) {
        $ordersData = file_get_contents($ordersFile);
        $orders = json_decode($ordersData, true);
        if ($orders && count($orders) > 0) {
            echo '<ul>';
            foreach ($orders as $order) {
                echo '<li>Product ID: ' . htmlspecialchars($order['product_id']) . ', Quantity: ' . htmlspecialchars($order['quantity']) . ', Time: ' . date('Y-m-d H:i:s', $order['timestamp']) . '</li>';
            }
            echo '</ul>';
        } else {
            echo '<p>No orders logged.</p>';
        }
    } else {
        echo '<p>Order log file not found.</p>';
    }
    ?>
</body>

</html>