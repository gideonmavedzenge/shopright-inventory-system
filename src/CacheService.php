<?php
namespace ShopRight;

class CacheService {
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }
    }

    // Loads inventory from the JSON file and caches it in the session.
    public function loadInventory(string $filePath): array {
        if (file_exists($filePath)) {
            $data = file_get_contents($filePath);
            $inventory = json_decode($data, true);
            if ($inventory === null) {
                return [];
            }
            $_SESSION['inventory'] = $inventory;
            return $inventory;
        }
        return [];
    }

    // Returns the cached inventory or an empty array if not set.
    public function getCachedInventory(): array {
        return $_SESSION['inventory'] ?? [];
    }

    // Updates the cached inventory in the session.
    public function updateCache(array $inventory): void {
        $_SESSION['inventory'] = $inventory;
    }
}