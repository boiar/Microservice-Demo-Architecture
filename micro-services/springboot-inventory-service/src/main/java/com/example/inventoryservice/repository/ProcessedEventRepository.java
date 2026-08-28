package com.example.inventoryservice.repository;

import com.example.inventoryservice.entity.ProcessedEvent;

import java.util.UUID;

public interface ProcessedEventRepository {

    boolean existsById(UUID eventId);

    void save(ProcessedEvent event);

    void markProcessed(UUID eventId, String eventType);

}
