<?php namespace App\Controller;

use DateTime;

class InvoiceController
{
  /**
   * Function to get all invoices
   *
   * @return array
   */
  public function getAllInvoices()
  {
    return json_decode(file_get_contents(__DIR__ . './../mocks/invoices.json'), true);
  }

  /**
   * Function to create order invoice
   *
   * @param integer $orderId
   * @return array
   */
  public function createOrderInvoice(int $orderId)
  {
    // Get the order data
    $order = (new OrderController())->getOrderById($orderId);

    if (empty($order)) {
      echo "Order not found. Invoice not created." . PHP_EOL;

      return [
        'error' => true,
        'message' => 'Order not found'
      ];
    } else {
      echo 'Order ID ' . $order['id'] . ' found. Creating invoice...' . PHP_EOL;

      return $this->createInvoice($order);
    }
  }
  
  /**
   * Function to create invoice for expired orders
   *
   * @return array
   */
  public function createInvoiceForExpiredOrders()
  {
    // get all expired orders
    $expiredOrders = (new OrderController())->getExpiredOrders();

    $createdInvoices = [];

    if (!empty($expiredOrders)) {
      foreach($expiredOrders as $expiredOrder) {
        $createdInvoices[] = $this->createInvoice($expiredOrder);
      }

      echo "Invoices created for expired orders" . PHP_EOL;

      return $createdInvoices;
    } else {
      echo "No expired orders found" . PHP_EOL;
    }
  }

  /**
   * Function to create invoice
   *
   * @param $order
   * 
   * @return array
   */
  private function createInvoice($order)
  {
    $invoiceJsonPath = __DIR__ . './../mocks/invoices.json';

    // read file
    $invoicesArray = json_decode(file_get_contents($invoiceJsonPath), true);

    $product = (new ProductController())->getProductById($order['product_id']);

    if (empty($product)) {
      echo "Product not found. Invoice not created." . PHP_EOL;

      return [
        'error' => true,
        'message' => 'Product not found'
      ];
    } 

    $invoiceDate = new DateTime();
    $invoiceId = $invoicesArray ? count($invoicesArray) + 1 : 1;     

    // add the new order data in $ordersArray
    $invoicesArray[] = [
      "id" => $invoiceId,
      "order_id" => $order['id'],
      "amount" => $product['price'],
      "invoice_date" => date_format($invoiceDate, 'Y-m-d H:i:s'),
      "invoice_due_date" => $invoiceDate->modify('+1 day')->format('Y-m-d H:i:s'),
    ];

    // update orders.json with the new order data
    file_put_contents($invoiceJsonPath, json_encode($invoicesArray));

    echo "Successfully created an invoice for Order ID: " . $order['id'] . PHP_EOL;

    return $invoicesArray;
  }
}