<?php

use App\Controller\ProductController;
use PHPUnit\Framework\TestCase;

final class ProductControllerTest extends TestCase
{
  /**
   * @covers ProductController::getAllProducts
   */
  public function testGetAllProducts(): void
  {
    $productsList = json_decode(file_get_contents(__DIR__ . './../mocks/products.json'), true);

    $response = (new ProductController)->getAllProducts();

    $this->assertEquals($productsList, $response);
  }

  /**
   * @covers ProductController::getProductById
   */
  public function testCanGetProductById(): void
  {
    $products = (new ProductController)->getAllProducts();

    $productId = !empty($products) ? $products[0]['id'] : 0;

    $response = (new ProductController)->getProductById($productId);

    if (!empty($products)) {
      $this->assertEquals($products[0], $response);
    } else {
      $this->assertEmpty($response);
    }
  }
}
