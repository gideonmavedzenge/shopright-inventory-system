<?php
use PHPUnit\Framework\TestCase;
use ShopRight\CacheService;
use ShopRight\InventoryManager;
use ShopRight\LogService;
use ShopRight\NotificationService;
use ShopRight\OrderProcessor;

class OrderProcessorTest extends TestCase {
    private $productsFile;
    private $ordersFile;
    private $logsFile;
    private $cacheService;
    private $inventoryManager;
    private $logService;
    private $notificationService;
    private $orderProcessor;

    protected function setUp(): void {
        // Remove the explicit session_start() if it's causing issues.
        // Instead, ensure $_SESSION is cleared.
        $_SESSION = [];
    
        // The rest of your setup...
        $this->productsFile = sys_get_temp_dir() . '/products_test.json';
        $this->ordersFile   = sys_get_temp_dir() . '/orders_test.json';
        $this->logsFile     = sys_get_temp_dir() . '/logs_test.json';
    
        // Create dummy files for testing.
        file_put_contents($this->productsFile, json_encode([
            ['id' => 1, 'name' => 'Test Product', 'stock' => 10, 'price' => 9.99]
        ], JSON_PRETTY_PRINT));
        file_put_contents($this->ordersFile, json_encode([], JSON_PRETTY_PRINT));
        file_put_contents($this->logsFile, json_encode([], JSON_PRETTY_PRINT));
    
        $this->cacheService = new \ShopRight\CacheService();
        $this->inventoryManager = new \ShopRight\InventoryManager($this->cacheService, $this->productsFile);
        $this->logService = new \ShopRight\LogService($this->ordersFile, $this->logsFile);
        $this->notificationService = new \ShopRight\NotificationService();
        $this->orderProcessor = new \ShopRight\OrderProcessor($this->inventoryManager, $this->logService, $this->notificationService);
    }
    

    protected function tearDown(): void {
         // Remove temporary files.
         @unlink($this->productsFile);
         @unlink($this->ordersFile);
         @unlink($this->logsFile);
         $_SESSION = [];
    }

    public function testSuccessfulOrder() {
         $result = $this->orderProcessor->processOrder(1, 3);
         $this->assertStringContainsString("Order processed successfully", $result);
         // Verify that the product's stock has been reduced.
         $inventory = $this->inventoryManager->getInventory();
         $this->assertEquals(7, $inventory[0]['stock']);
    }

    public function testOutOfStockError() {
         // Try to order more than available.
         $result = $this->orderProcessor->processOrder(1, 20);
         $this->assertEquals("Error: Not enough stock.", $result);
         // Check that an error is logged.
         $logs = json_decode(file_get_contents($this->logsFile), true);
         $this->assertNotEmpty($logs);
         $lastLog = end($logs);
         $this->assertStringContainsString("Not enough stock", $lastLog['message']);
    }

    public function testProductNotFound() {
         // Try ordering a non-existing product.
         $result = $this->orderProcessor->processOrder(999, 1);
         $this->assertEquals("Error: Product not found.", $result);
         // Check that an error is logged.
         $logs = json_decode(file_get_contents($this->logsFile), true);
         $this->assertNotEmpty($logs);
         $lastLog = end($logs);
         $this->assertStringContainsString("Product not found", $lastLog['message']);
    }
}