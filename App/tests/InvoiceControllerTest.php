<?php

use App\Controller\InvoiceController;
use App\Controller\OrderController;
use PHPUnit\Framework\TestCase;

final class InvoiceControllerTest extends TestCase
{
  /**
   * @covers InvoiceController::getAllInvoices
   */
  public function testGetAllInvoices(): void
  {
    $invoiceList = json_decode(file_get_contents(__DIR__ . './../mocks/invoices.json'), true);

    $response = (new InvoiceController)->getAllInvoices();

    $this->assertEquals($invoiceList, $response);
  }
  
  /**
   * @covers InvoiceController::createOrderInvoice
   */
  public function testCanCreateOrderInvoice(): void
  {
    // create order for testing
    $ordersArray = json_decode(file_get_contents(__DIR__ . './../mocks/orders.json'), true);
    $orderId = !empty($ordersArray) ? $ordersArray[0]['id'] + 1 : 1;
    
    $newOrder = [
      "id" => $orderId,
      "product_id" => 2,
      "quantity" => 5,
      "frequency" => 'annually',
      "next_billing_date" => (new DateTime())->modify('+1 year')->format('Y-m-d')
    ];

    // update orders.json with the new order data
    $ordersArray[] = $newOrder;
    file_put_contents(__DIR__ . './../mocks/orders.json', json_encode($ordersArray));

    (new OrderController)->getOrderById($orderId);

    $response = (new InvoiceController)->createOrderInvoice($orderId);
    
    $this->assertIsArray($response);
    $this->assertEquals($response, (new InvoiceController)->getAllInvoices());
  }
  
  /**
   * @covers InvoiceController::createOrderInvoice
   */
  public function testCannotCreateOrderInvoiceWithInvalidOrder(): void
  {
    // create order for testing
    $ordersArray = json_decode(file_get_contents(__DIR__ . './../mocks/orders.json'), true);
    $orderId = !empty($ordersArray) ? count($ordersArray) + 1 : 0;
    
    (new OrderController)->getOrderById($orderId);

    $response = (new InvoiceController)->createOrderInvoice($orderId);
    
    $this->assertIsArray($response);
    $this->assertEquals($response, [
      'error' => true,
      'message' => 'Order not found'
    ]);
  }
  
  /**
   * @covers InvoiceController::createOrderInvoice
   */
  public function testCannotCreateOrderInvoiceWithInvalidProduct(): void
  {
    // create order for testing
    $ordersArray = json_decode(file_get_contents(__DIR__ . './../mocks/orders.json'), true);
    $orderId = !empty($ordersArray) ? count($ordersArray) + 1 : 1;
    
    $newOrder = [
      "id" => $orderId,
      "product_id" => 5, // mock invalid product id
      "quantity" => 5,
      "frequency" => 'annually',
      "next_billing_date" => (new DateTime())->modify('+1 year')->format('Y-m-d')
    ];

    // update orders.json with the new order data
    $ordersArray[] = $newOrder;
    file_put_contents(__DIR__ . './../mocks/orders.json', json_encode($ordersArray));

    (new OrderController)->getOrderById($orderId);

    $response = (new InvoiceController)->createOrderInvoice($orderId);
    
    $this->assertIsArray($response);
    $this->assertEquals($response, [
      'error' => true,
      'message' => 'Product not found'
    ]);
  }
  
  /**
   * @covers InvoiceController::createInvoiceForExpiredOrders
   */
  public function testCanCreateInvoiceForExpiredOrders(): void
  {
    // create order for testing
    $ordersArray = json_decode(file_get_contents(__DIR__ . './../mocks/orders.json'), true);
    $orderId = !empty($ordersArray) ? $ordersArray[0]['id'] + 1 : 1;
    
    $newOrder = [
      "id" => $orderId,
      "product_id" => 2,
      "quantity" => 5,
      "frequency" => 'monthly',
      "next_billing_date" => (new DateTime())->modify('-1 day')->format('Y-m-d')
    ];

    // update orders.json with the new order data
    $ordersArray[] = $newOrder;
    file_put_contents(__DIR__ . './../mocks/orders.json', json_encode($ordersArray));

    $response = (new InvoiceController)->createInvoiceForExpiredOrders();

    $this->assertIsArray($response);
  }
}
