package com.example.inventoryservice.service;

import com.example.inventoryservice.dto.request.StockCreateRequest;
import com.example.inventoryservice.dto.response.StockResponse;

public interface StockService {

    StockResponse createStock(StockCreateRequest request);

    StockResponse getByProductId(Long productId);

    StockResponse adjustQuantity(Long productId, int quantityChange);

    /** Reserves quantity for an order line; raises the corresponding outbox event. */
    void reserveStock(Long orderId, Long productId, int quantity);

    /** Releases previously reserved quantity back to available. */
    void releaseStock(Long orderId, Long productId, int quantity);

}