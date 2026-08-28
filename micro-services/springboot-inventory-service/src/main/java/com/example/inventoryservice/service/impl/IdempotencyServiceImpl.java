package com.example.inventoryservice.service.impl;

import com.example.inventoryservice.entity.ProcessedEvent;
import com.example.inventoryservice.repository.ProcessedEventRepository;
import com.example.inventoryservice.service.IdempotencyService;
import lombok.RequiredArgsConstructor;
import lombok.extern.slf4j.Slf4j;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.time.Instant;
import java.util.UUID;

@Slf4j
@Service
@RequiredArgsConstructor
public class IdempotencyServiceImpl implements IdempotencyService {
    private final ProcessedEventRepository processedEventRepo;

    @Override
    public boolean checkPaymentProcessed(UUID eventId) {
        if (eventId == null) {
            log.warn("Received event without an x-event-id header; processing without dedup");
            return false;
        }
        return processedEventRepo.existsById(eventId);
    }

    @Override
    @Transactional
    public void markProcessed(UUID eventId, String eventType) {
        if (eventId == null) {
            log.warn("Received event without an x-event-id header; processing without dedup");
        }

        processedEventRepo.save(ProcessedEvent.builder()
                .eventId(eventId)
                .eventType(eventType)
                .processedAt(Instant.now())
                .build());

    }
}
