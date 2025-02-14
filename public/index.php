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

$inventory = $inventoryManager->getInventory();
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>ShopRight Inventory Management</title>
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        margin-top: 20px;
    }

    .notification {
        margin-bottom: 10px;
    }
    </style>
</head>

<body>
    <div class="container">
        <h1 class="mb-4">ShopRight Inventory Management</h1>

        <?php if($message): ?>
        <div id="alert-message" class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <div class="card mb-4">
            <div class="card-header">Place an Order</div>
            <div class="card-body">
                <form method="post" action="">
                    <div class="mb-3">
                        <label for="product_id" class="form-label">Product:</label>
                        <select name="product_id" class="form-select" required>
                            <?php foreach($inventory as $product): ?>
                            <option value="<?php echo htmlspecialchars($product['id']); ?>">
                                <?php echo htmlspecialchars($product['name']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity:</label>
                        <input type="number" name="quantity" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Process Order</button>
                </form>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">Low Stock Notifications</div>
            <div class="card-body">
                <?php if (isset($_SESSION['notifications']) && count($_SESSION['notifications']) > 0): ?>
                <?php foreach ($_SESSION['notifications'] as $note): ?>
                <div class="alert alert-warning notification"><?php echo htmlspecialchars($note); ?></div>
                <?php endforeach; ?>
                <?php else: ?>
                <p>No notifications.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">Order Log</div>
            <div class="card-body">
                <?php
            $ordersFile = __DIR__ . '/../data/orders.json';
            if (file_exists($ordersFile)) {
                $ordersData = file_get_contents($ordersFile);
                $orders = json_decode($ordersData, true);
                if ($orders && count($orders) > 0) {
                    echo '<ul class="list-group">';
                    foreach ($orders as $order) {
                        echo '<li class="list-group-item">';
                        echo 'Product ID: ' . htmlspecialchars($order['product_id']) . ', Quantity: ' . htmlspecialchars($order['quantity']) . ', Time: ' . date('Y-m-d H:i:s', $order['timestamp']);
                        echo '</li>';
                    }
                    echo '</ul>';
                } else {
                    echo '<p>No orders logged.</p>';
                }
            } else {
                echo '<p>Order log file not found.</p>';
            }
            ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Wait for the DOM to be fully loaded
    document.addEventListener('DOMContentLoaded', function() {
        // Select the alert element
        var alert = document.getElementById('alert-message');
        // Check if the alert exists
        if (alert) {
            // Set a timeout to remove the alert after 10 seconds
            setTimeout(function() {
                // Fade out the alert before removing
                alert.classList.add('fade');
                alert.classList.remove('show');
                // Wait for the fade transition to complete (150ms for Bootstrap's default)
                setTimeout(function() {
                    alert.remove();
                }, 150);
            }, 10000); // 10000 milliseconds = 10 seconds
        }
    });
    </script>
</body>

</html>