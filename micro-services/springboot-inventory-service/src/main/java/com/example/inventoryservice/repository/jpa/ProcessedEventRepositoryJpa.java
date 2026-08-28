package com.example.inventoryservice.repository.jpa;

import com.example.inventoryservice.entity.ProcessedEvent;
import org.springframework.data.jpa.repository.JpaRepository;

import java.util.UUID;

public interface ProcessedEventRepositoryJpa extends JpaRepository<ProcessedEvent, UUID> {
}
