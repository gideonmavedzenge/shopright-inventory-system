<?php
namespace ShopRight;

class NotificationService {
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['notifications'])) {
            $_SESSION['notifications'] = [];
        }
    }

    // Sends a low-stock alert by storing a notification in the session.
    public function sendLowStockAlert(array $product): void {
        $message = "Low stock alert: " . $product['name'] . " (ID: " . $product['id'] . ") is low on stock. Current stock: " . $product['stock'] . ".";
        $_SESSION['notifications'][] = $message;
    }
}