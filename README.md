# Product Billing Service
A simple product billing service that allows users to purchase a product and subscribe to it on a monthly or annual basis.

## Setup
To run this project, please install the following

### Prerequisite
* composer v2 - (https://getcomposer.org/download)
* php 8.1
* CLI

Once you have the above installed locally, run the following:

* `composer install`
* `php -S localhost:8000`

## Running the project
While `localhost:8000` is running, we can now start using the product billing service by calling some curl requests.

#### Request for listing all available products
This endpoint will return an array of products

```curl -X GET  http://localhost:8000/products -H "Content-Type: application/json"```

#### Request for purchasing a product
This endpoint allows you to select a product and select a subscription frequency. Please update the parameter in the curl request for your selected `product_id` from the list of available products, select your desired quantity and frequency from either monthly or annually.

```curl -X POST  http://localhost:8000/orders  -H 'Content-Type: application/json'  -d '{"product_id": 1, "quantity": 2, "frequency": "annually"}'```

#### Request for creating new billing for all expired orders.
This request will find all expired orders and will create a new invoice with the expired orders' details.

```curl -X POST  http://localhost:8000/expired-orders```

There are 2 scenarios for this request: No expired orders and with expired orders

##### Scenario 1: No expired orders
- This assumes that there are no expired orders in `App/mocks/orders.json`
- When this request is used with no expired orders. No new invoices will be created in `App\mocks\invoices.json`.

##### Scenario 2: With expired orders
- This assumes that you have at least 1 expired order in `App/mocks/orders.json`
- To do this, create a couple of orders by following the steps in `Request for purchasing a product` section.
- Manually update the field `next_billing_date` to a past date.
- Once you have some expired orders, run this request again.
- A new invoice should be created for the expired orders inside `App/mocks/invoices.json`. 

## Tests
Test cases are created for each controller file. 

* To run the whole test suite run `./vendor/bin/phpunit App/tests`
* To run only the InvoiceControllerTest run `./vendor/bin/phpunit App/tests --filter InvoiceControllerTest`
* To run only the OrderControllerTest run `./vendor/bin/phpunit App/tests --filter OrderControllerTest`
* To run only the ProductController run `./vendor/bin/phpunit App/tests --filter ProductController`