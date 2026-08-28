package com.example.inventoryservice.repository;

import com.example.inventoryservice.entity.OutboxEvent;

import java.util.List;

public interface OutboxRepository {

    List<OutboxEvent> findPendingEvents();

    void save(OutboxEvent event);

    void markPublished(OutboxEvent event);
}
