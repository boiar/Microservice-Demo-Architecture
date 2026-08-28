package com.example.inventoryservice.exception;

public class ProductNotFoundException extends RuntimeException {

    public ProductNotFoundException(Long productId) {
        super("No stock record found for product " + productId);
    }
}
