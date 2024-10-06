<?php namespace App\Controller;

use App\Lib\Request;
use DateTime;

class OrderController
{
  /**
   * Function to get all orders
   *
   * @return array
   *
   */
  public function getAllOrders()
  {
    $ordersJson = file_get_contents(__DIR__ . './../mocks/orders.json');

    return !empty($ordersJson) ? json_decode($ordersJson, true) : [];
  }

  /**
   * Function to get order details by ID
   *
   * @param integer $id
   * @return array
   */
  public function getOrderById(int $id)
  {
    $orders = $this->getAllOrders();

    // find object in array by id
    $ordersFilter = array_filter($orders, function ($order) use ($id) {
      return $order['id'] == $id;
    });

    return current($ordersFilter);
  }

  /**
   * Function to get expired orders
   *
   * @return array
   */
  public function getExpiredOrders() 
  {
    $orders = $this->getAllOrders();

    $currentDate = new DateTime();

    $expiredOrders = array_filter($orders, function ($order) use ($currentDate) {
      return new DateTime($order['next_billing_date']) < $currentDate;
    });

    return $expiredOrders ? $expiredOrders : [];
  }

  /**
   * Function to create an order
   *
   * @param Request $request
   * @return array
   */
  public function createOrder(Request $request)
  {
    $orderJsonPath = __DIR__ . './../mocks/orders.json';
    
    // read file
    $ordersArray = json_decode(file_get_contents($orderJsonPath), true);

    $hasRequestParams = !empty($request->params);

    // get data from request
    $product_id = $hasRequestParams ? $request->params['params']['product_id'] : $request->getJSON()->product_id;
    $quantity = $hasRequestParams ? $request->params['params']['quantity'] : $request->getJSON()->quantity;
    $frequency = $hasRequestParams ? $request->params['params']['frequency'] : $request->getJSON()->frequency;

    $nextBillingDate = $this->getNextBillingDate($frequency);

    if (empty($nextBillingDate)) {
      echo "Invalid subscription frequency. Order not created." . PHP_EOL;

      return [
        'error' => [
          'message' => 'Invalid frequency'
        ]
      ];
    }

    $orderId = $ordersArray ? count($ordersArray) + 1 : 1;

    // add the new order data in $ordersArray
    $ordersArray[] = [
      "id" => $orderId,
      "product_id" => $product_id,
      "quantity" => $quantity,
      "frequency" => $frequency,
      "next_billing_date" => date_format($nextBillingDate, 'Y-m-d')
    ];

    // update orders.json with the new order data
    file_put_contents($orderJsonPath, json_encode($ordersArray));

    // Create an invoice
    (new InvoiceController())->createOrderInvoice($orderId);

    echo "Order ID: " . $orderId . " created successfully." . PHP_EOL;

    return $ordersArray;
  }

  /**
   * Get next billing date based on frequency selected
   *
   * @param String $frequency
   */
  private function getNextBillingDate(String $frequency) {
    $nextBillingDate = new DateTime();

    // calculate next billing date based on frequency
    switch ($frequency) {
      case 'monthly':
        return $nextBillingDate->modify('+1 month');
        break;
      case 'annually':
        return $nextBillingDate->modify('+1 year');
        break;
      default:
        return null;
        break;
    }
  }
}


