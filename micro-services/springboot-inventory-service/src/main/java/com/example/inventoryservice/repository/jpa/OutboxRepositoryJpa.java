package com.example.inventoryservice.repository.jpa;

import com.example.inventoryservice.entity.OutboxEvent;
import com.example.inventoryservice.enums.OutboxStatus;
import org.springframework.data.jpa.repository.JpaRepository;

import java.util.List;

public interface OutboxRepositoryJpa extends JpaRepository<OutboxEvent, Long> {

    List<OutboxEvent> findTop100ByStatusOrderByIdAsc(OutboxStatus status);
}
