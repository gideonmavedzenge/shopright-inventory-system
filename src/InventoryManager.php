<?php
namespace ShopRight;

class InventoryManager {
    private string $productsFile;
    private CacheService $cacheService;

    public function __construct(CacheService $cacheService, string $productsFile = __DIR__ . '/../data/products.json') {
        $this->cacheService = $cacheService;
        $this->productsFile = $productsFile;
        if (!isset($_SESSION['inventory'])) {
            $this->cacheService->loadInventory($this->productsFile);
        }
    }

    // Returns the current inventory from the cache.
    public function getInventory(): array {
        return $this->cacheService->getCachedInventory();
    }

    // Updates product stock if sufficient stock is available.
    // Returns the updated product array or null on failure.
    public function updateProductStock(int $productId, int $quantity): ?array {
        $inventory = $this->cacheService->getCachedInventory();
        foreach ($inventory as &$product) {
            if ($product['id'] === $productId) {
                if ($product['stock'] < $quantity) {
                    return null; // Not enough stock.
                }
                $product['stock'] -= $quantity;
                $this->cacheService->updateCache($inventory);
                $this->saveInventory($inventory);
                return $product;
            }
        }
        return null;
    }

    // Saves the updated inventory back to the products JSON file.
    private function saveInventory(array $inventory): void {
        $data = json_encode($inventory, JSON_PRETTY_PRINT);
        if (file_put_contents($this->productsFile, $data) === false) {
            // Error handling is delegated to LogService.
        }
    }
}