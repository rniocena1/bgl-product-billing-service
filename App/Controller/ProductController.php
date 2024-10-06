<?php namespace App\Controller;

class ProductController
{
  /**
   * Function to get all products
   *
   * @return array
   */
  public function getAllProducts()
  {
    return json_decode(file_get_contents(__DIR__ . './../mocks/products.json'), true);
  }

  /**
   * Function to get product by id
   *
   * @param int $id
   * @return array
   */
  public function getProductById(int $id)
  {
    $products = $this->getAllProducts();
    
    // find object in array by id
    return current(array_filter($products, function ($product) use ($id) {
      return $product['id'] == $id;
    }));
  }
}