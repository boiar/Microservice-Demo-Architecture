# Architecture Updates for Laravel Order Service

With the introduction of the new `springboot-inventory-service`, the **Laravel Order Service** also needs architectural changes. Currently, it is tightly coupled to the product inventory by performing local stock checks and deductions. 

To adopt a proper **Saga Pattern / Event-Driven Architecture**, the Order Service should delegate all stock responsibilities to the Inventory Service.

Here is what should change in the **Laravel Order Service** logic:

## 1. Remove Local Stock Checking & Deductions
Currently, in `OrderService.php` (`createOrder` method), the service checks if `$product->qty < $item->quantity` and then deducts it: `$product->qty -= $item->quantity;`. 
* **Action**: Remove the `IProductRepository` injection and all product quantity logic from `OrderService.php`.
* **Action**: The Order Service should assume stock is available and simply create the order with a status of `pending`, then publish the `order.created` event. It is up to the Inventory Service to validate the stock.

## 2. Listen for Inventory Events (Saga Pattern)
Because the Order Service no longer checks stock synchronously, it needs to know if the order can actually be fulfilled.
* **Action**: Create a RabbitMQ Consumer (e.g., using Laravel queues or a custom worker) in the Laravel Order Service to listen to events published by the Inventory Service.
* **Action**: Listen for `stock.reserved` (or `STOCK_RESERVED`). When received, update the order status from `pending` to `awaiting_payment` (or `confirmed`).
* **Action**: Listen for `out_of_stock` (or `OUT_OF_STOCK`). When received, update the order status from `pending` to `cancelled` (or `failed_inventory`).

## 3. Remove Local Product Tracking from Cart (Optional but Recommended)
In `CartService.php`, there is also logic using `IProductRepository` to validate product availability when adding to the cart. 
* **Action**: You can either transition this to a read-only check (using an API call to the Inventory Service / Product Service) or remove the strict stock check during cart addition, handling the failure during the checkout phase instead.

## Summary of Next Steps
1. Strip all `productRepo->update(...)` and `qty` math from `OrderService.php`.
2. Implement RabbitMQ listeners in Laravel for the Inventory Service's outbox events.
3. Introduce state-machine logic for Orders (`pending` -> `confirmed` -> `payment_failed`, etc.) driven by external events rather than synchronous checks.
