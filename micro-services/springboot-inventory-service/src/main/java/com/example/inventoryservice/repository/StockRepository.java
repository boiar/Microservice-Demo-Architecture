package com.example.inventoryservice.repository;


import com.example.inventoryservice.entity.Stock;

import java.util.Optional;

public interface StockRepository {
    Optional<Stock> findByProductId(Long productId);

    Optional<Stock> findByProductIdForUpdate(Long productId);

    Stock save(Stock stock);
}
