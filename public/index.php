<?php

session_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/Models/Store.php';
require_once __DIR__ . '/../app/Controllers/StoreController.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function format_price($value): string
{
    return number_format((float) $value, 0, ',', '.') . '₫';
}

$databaseReady = true;
$databaseMessage = null;

try {
    $store = new Store(database());
    $page = (new StoreController($store))->handle();
} catch (PDOException $exception) {
    $databaseReady = false;
    $databaseMessage = 'Chưa kết nối được MySQL. Hãy bật MySQL trong XAMPP, import database/schema.sql và database/seeds/products.sql.';
    $page = [
        'action' => 'store',
        'products' => Store::sampleProducts(),
        'cartItems' => [],
        'cartCount' => 0,
        'cartTotal' => 0,
        'csrfToken' => $_SESSION['csrf_token'],
        'error' => null,
        'notice' => null,
        'form' => ['customer_name' => '', 'phone' => '', 'address' => ''],
    ];
}

if (!$databaseReady) {
    http_response_code(503);
}

extract($page, EXTR_SKIP);
require __DIR__ . '/../app/Views/store.php';