package com.example.inventoryservice.exception;

public class StockAlreadyExistsException extends RuntimeException {

    public StockAlreadyExistsException(Long productId) {
        super("Stock record already exists for product " + productId);
    }
}
