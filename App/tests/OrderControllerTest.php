<?php

use App\Controller\OrderController;
use App\Lib\Request;
use App\Lib\Response;
use App\Lib\Router;
use PHPUnit\Framework\TestCase;

final class OrderControllerTest extends TestCase
{
  /**
   * @covers OrderController::getAllOrders
   */
  public function testGetAllOrders(): void
  {
    $orderList = json_decode(file_get_contents(__DIR__ . './../mocks/orders.json'), true);

    $response = (new OrderController)->getAllOrders();

    if (!empty($response)) {
      $this->assertEquals($orderList, $response);
    } else {
      $this->assertEmpty($response);
    }
  }

  /**
   * @covers OrderController::getOrderById
   */
  public function testCanGetOrderById(): void
  {
    $orders = (new OrderController)->getAllOrders();

    $orderId = !empty($orders) ? $orders[0]['id'] : 0;

    $response = (new OrderController)->getOrderById($orderId);

    if (!empty($orders)) {
      $this->assertEquals($orders[0], $response);
    } else {
      $this->assertEmpty($response);
    }
  }

  /**
   * @covers OrderController::getExpiredOrders
   */
  public function testCanGetExpiredOrders(): void
  {
    $ordersArray = json_decode(file_get_contents(__DIR__ . './../mocks/orders.json'), true);

    $orderId = !empty($ordersArray) ? $ordersArray[0]['id'] + 1 : 1;
    
    // create expired orders for testing
    $sampleExpiredOrder = [
      "id" => $orderId,
      "product_id" => 1,
      "quantity" => 10,
      "frequency" => 'monthly',
      "next_billing_date" => (new DateTime())->modify('-1 day')->format('Y-m-d')
    ];

    // update orders.json with the new order data
    $ordersArray[] = $sampleExpiredOrder;
    file_put_contents(__DIR__ . './../mocks/orders.json', json_encode($ordersArray));

    $response = (new OrderController)->getExpiredOrders();

    $currentDate = new DateTime();

    $expiredOrders = array_filter($ordersArray, function ($order) use ($currentDate) {
      return new DateTime($order['next_billing_date']) < $currentDate;
    });

    $this->assertEquals($expiredOrders, $response);
  }

  /**
   * @covers OrderController::createOrder
   */
  public function testCanCreateOrderWithMonthlyFrequency(): void
  {
    $nextBillingDate = (new DateTime())->modify('+1 month')->format('Y-m-d');

    $order = [
      "product_id" => 1,
      "quantity" => 2,
      "frequency" => 'monthly',
      "next_billing_date" => $nextBillingDate
    ];

    $_SERVER['REQUEST_METHOD'] = 'POST';
    $_SERVER['REQUEST_URI'] = '/order';
    $_SERVER['CONTENT_TYPE'] = 'application/json';

    $request = new Request(["params" => $order]);
    $response = (new OrderController)->createOrder($request);

    $this->assertEquals(end($response)['next_billing_date'], $nextBillingDate);
  }
  
  /**
   * @covers OrderController::createOrder
   */
  public function testCanCreateOrderWithAnnualFrequency(): void
  {
    $nextBillingDate = (new DateTime())->modify('+1 year')->format('Y-m-d');

    $order = [
      "product_id" => 1,
      "quantity" => 2,
      "frequency" => 'annually',
      "next_billing_date" => $nextBillingDate
    ];

    $_SERVER['REQUEST_METHOD'] = 'POST';
    $_SERVER['REQUEST_URI'] = '/order';
    $_SERVER['CONTENT_TYPE'] = 'application/json';

    $request = new Request(["params" => $order]);
    $response = (new OrderController)->createOrder($request);

    $this->assertEquals(end($response)['next_billing_date'], $nextBillingDate);
  }
  
  /**
   * @covers OrderController::createOrder
   */
  public function testCannotCreateOrderWithInvalidFrequency(): void
  {
    $nextBillingDate = (new DateTime())->modify('+1 year')->format('Y-m-d');

    $order = [
      "product_id" => 1,
      "quantity" => 2,
      "frequency" => 'weekly',
      "next_billing_date" => $nextBillingDate
    ];

    $_SERVER['REQUEST_METHOD'] = 'POST';
    $_SERVER['REQUEST_URI'] = '/order';
    $_SERVER['CONTENT_TYPE'] = 'application/json';

    $request = new Request(["params" => $order]);
    $response = (new OrderController)->createOrder($request);

    $this->assertEquals($response, [
      'error' => [
        'message' => 'Invalid frequency'
      ]
    ]);
  }
}
