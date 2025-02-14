<?php
namespace ShopRight;

class LogService {
    private string $ordersFile;
    private string $logsFile;

    public function __construct(
        string $ordersFile = __DIR__ . '/../data/orders.json',
        string $logsFile = __DIR__ . '/../data/logs.json'
    ) {
        $this->ordersFile = $ordersFile;
        $this->logsFile = $logsFile;
    }

    // Appends an order entry to orders.json.
    public function logOrder(array $order): void {
        $orders = [];
        if (file_exists($this->ordersFile)) {
            $data = file_get_contents($this->ordersFile);
            $orders = json_decode($data, true) ?? [];
        }
        $orders[] = $order;
        file_put_contents($this->ordersFile, json_encode($orders, JSON_PRETTY_PRINT));
    }

    // Logs an error message to logs.json.
    public function logError(string $message): void {
        $logs = [];
        if (file_exists($this->logsFile)) {
            $data = file_get_contents($this->logsFile);
            $logs = json_decode($data, true) ?? [];
        }
        $logs[] = [
            'message' => $message,
            'timestamp' => time()
        ];
        file_put_contents($this->logsFile, json_encode($logs, JSON_PRETTY_PRINT));
    }
}