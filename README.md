# ShopRight Inventory Management System

## Project Overview

This project is a simplified PHP 8 Inventory Management Microservice for the ShopRight e-commerce platform. It simulates real-time inventory checking, order processing, low-stock notifications, and order logging without using a database. Instead, it uses JSON files for persistent storage and PHP sessions for caching.

## Features

-   **Real-time Inventory Check:** Validates available stock before processing orders.
-   **Order Processing:** Deducts the ordered quantity from product stock.
-   **Low Stock Notifications:** Alerts users when a product's stock falls below 5.
-   **Order Logging:** Maintains an order history in a JSON file.
-   **Cache Service:** Uses PHP sessions to cache inventory data.
-   **Error Logging:** Logs errors in file operations to a separate log file.
-   **Composer Autoloading:** Utilizes Composer's PSR-4 autoloading for modular code organization.

## Project Structure

shopright-inventory-system/ ├── composer.json ├── public/ │ └── index.php ├── src/ │ ├── CacheService.php │ ├── InventoryManager.php │ ├── LogService.php │ ├── NotificationService.php │ └── OrderProcessor.php ├── data/ │ ├── products.json │ ├── orders.json │ └── logs.json └── README.md

## Setup Instructions

1. **Clone the Repository:**
   git clone https://github.com/yourusername/shopright-inventory-system.git

2. **Navigate to the Project Directory:**
   `cd shopright-inventory-system`

3. **Install Composer Dependencies:**
   `composer dump-autoload`

4. **Ensure PHP 8 is Installed:**
   Verify PHP version by running:
   `php -v`

5. **Start a Local PHP Server:**
   `php -S localhost:8000 -t public`

6. **Access the Application:**
   Open your browser and go to [http://localhost:8000/index.php](http://localhost:8000/index.php)

## Design Decisions

-   **Modular Code Structure:** The project is split into separate classes, each handling a single responsibility: inventory management, caching, order processing, notifications, and logging.
-   **Composer Autoloading:** PSR-4 autoloading is configured via Composer to automatically load classes from the `src/` directory.
-   **Data Storage:** JSON files (`products.json`, `orders.json`, and `logs.json`) are used to simplify data storage without a database.
-   **Session Caching:** PHP sessions cache the inventory data to reduce file I/O on every request.

## Running the Project Locally

Follow the steps in the **Setup Instructions** section. Use the form on the index page to simulate placing orders. Low-stock notifications and order logs will be displayed in real-time.

## Future Enhancements

-   Integrate with a database for production use.
-   Improve front-end design.
-   Add user authentication and more robust error handling.
