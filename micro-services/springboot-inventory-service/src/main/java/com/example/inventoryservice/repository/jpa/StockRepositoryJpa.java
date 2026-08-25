package com.example.inventoryservice.repository.jpa;


import com.example.inventoryservice.entity.Stock;
import jakarta.persistence.LockModeType;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Lock;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;

import java.util.Optional;

public interface StockRepositoryJpa extends JpaRepository<Stock, Long> {

    Optional<Stock> findByProductId(Long productId);

    /**
     * Pessimistic write lock so concurrent reserve/release calls for the same
     * product serialize at the DB row instead of racing on the optimistic
     * @Version field (used on the hot reservation path under contention).
     */
    @Lock(LockModeType.PESSIMISTIC_WRITE)
    @Query("select s from Stock s where s.productId = :productId")
    Optional<Stock> findByProductIdForUpdate(@Param("productId") Long productId);
}
