package com.example.inventoryservice.repository.impl;

import com.example.inventoryservice.entity.Stock;
import com.example.inventoryservice.repository.StockRepository;
import com.example.inventoryservice.repository.jpa.StockRepositoryJpa;
import lombok.RequiredArgsConstructor;
import org.springframework.stereotype.Repository;

import java.util.Optional;

@Repository
@RequiredArgsConstructor
public class StockRepositoryImpl implements StockRepository {

    private final StockRepositoryJpa stockRepositoryJpa;


    @Override
    public Optional<Stock> findByProductId(Long productId) {
        return stockRepositoryJpa.findByProductId(productId);
    }

    @Override
    public Optional<Stock> findByProductIdForUpdate(Long productId) {
        return stockRepositoryJpa.findByProductIdForUpdate(productId);
    }

    @Override
    public Stock save(Stock stock) {
        return stockRepositoryJpa.save(stock);
    }
}
