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
        $updatedProduct = $this->inventoryManager->updateProductStock($productId, $quantity);
        if ($updatedProduct === null) {
            return "Error: Not enough stock or product not found.";
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