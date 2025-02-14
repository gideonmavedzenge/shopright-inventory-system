<?php
namespace ShopRight;

class OrderProcessor {
    private InventoryManager $inventoryManager;
    private LogService $logService;
    private NotificationService $notificationService;

    public function __construct(
        InventoryManager $inventoryManager,
        LogService $logService,
        NotificationService $notificationService
    ) {
        $this->inventoryManager = $inventoryManager;
        $this->logService = $logService;
        $this->notificationService = $notificationService;
    }

    // Processes an order: validates, updates stock, logs the order, and triggers notifications.
    public function processOrder(int $productId, int $quantity): string {
        if ($quantity <= 0) {
            return "Invalid order quantity.";
        }

        // Retrieve current inventory from the cache.
        $inventory = $this->inventoryManager->getInventory();
        $productFound = false;
        foreach ($inventory as $product) {
            if ($product['id'] === $productId) {
                $productFound = true;
                if ($product['stock'] < $quantity) {
                    $this->logService->logError(
                        "Not enough stock for product: " . $product['name'] . 
                        " (ID: " . $product['id'] . "). Requested: $quantity, Available: " . $product['stock']
                    );
                    return "Error: Not enough stock.";
                }
                break;
            }
        }

        if (!$productFound) {
            $this->logService->logError("Product not found: Product ID: $productId.");
            return "Error: Product not found.";
        }

        // Update product stock.
        $updatedProduct = $this->inventoryManager->updateProductStock($productId, $quantity);
        if ($updatedProduct === null) {
            $this->logService->logError("Failed to update product stock for product ID: $productId.");
            return "Error: Could not update product stock.";
        }

        // Log the order.
        $order = [
            'product_id' => $productId,
            'quantity'   => $quantity,
            'timestamp'  => time()
        ];
        $this->logService->logOrder($order);

        // Trigger a low-stock notification if stock falls below 5.
        if ($updatedProduct['stock'] < 5) {
            $this->notificationService->sendLowStockAlert($updatedProduct);
        }
        return "Order processed successfully for " . $updatedProduct['name'] . ".";
    }
}