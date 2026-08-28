package com.example.inventoryservice.repository.impl;

import com.example.inventoryservice.entity.ProcessedEvent;
import com.example.inventoryservice.repository.ProcessedEventRepository;
import com.example.inventoryservice.repository.jpa.ProcessedEventRepositoryJpa;
import lombok.RequiredArgsConstructor;
import org.springframework.stereotype.Repository;

import java.time.Instant;
import java.util.UUID;

@Repository
@RequiredArgsConstructor
public class ProcessedEventRepositoryImpl implements ProcessedEventRepository {

    private final ProcessedEventRepositoryJpa processedEventRepositoryJpa;


    @Override
    public boolean existsById(UUID eventId) {
        return processedEventRepositoryJpa.existsById(eventId);
    }

    @Override
    public void save(ProcessedEvent event) {
        processedEventRepositoryJpa.save(event);
    }

    @Override
    public void markProcessed(UUID eventId, String eventType) {
        processedEventRepositoryJpa.save(ProcessedEvent.builder()
                .eventId(eventId)
                .eventType(eventType)
                .processedAt(Instant.now())
                .build());
    }
}
