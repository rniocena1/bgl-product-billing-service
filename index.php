<?php

require __DIR__ . '/vendor/autoload.php';

use App\Controller\InvoiceController;
use App\Controller\OrderController;
use App\Controller\Product;
use App\Controller\ProductController;
use App\Lib\Logger;
use App\Lib\Request;
use App\Lib\Router;

Logger::enableSystemLogs();

Router::get('/products', function () {
  $productList = (new ProductController())->getAllProducts();

  echo "Available Products:" . PHP_EOL . json_encode($productList) . PHP_EOL;
});

Router::get('/orders', function () {
  $productList = (new OrderController())->getAllOrders();

  echo "Displaying all orders" . PHP_EOL . json_encode($productList) . PHP_EOL;
});

Router::post('/orders', function (Request $request) {
  return (new OrderController())->createOrder($request);
});

Router::post('/expired-orders', function () {
  return (new InvoiceController())->createInvoiceForExpiredOrders();
});

