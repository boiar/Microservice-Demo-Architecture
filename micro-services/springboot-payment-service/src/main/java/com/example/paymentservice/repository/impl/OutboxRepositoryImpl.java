package com.example.paymentservice.repository.impl;

import com.example.paymentservice.entity.OutboxEvent;
import com.example.paymentservice.enums.OutboxStatus;
import com.example.paymentservice.repository.OutboxRepository;
import com.example.paymentservice.repository.jpa.OutboxRepositoryJpa;
import lombok.RequiredArgsConstructor;
import org.springframework.stereotype.Repository;

import java.time.Instant;
import java.util.List;

@Repository
@RequiredArgsConstructor
public class OutboxRepositoryImpl implements OutboxRepository {

    private final OutboxRepositoryJpa outboxRepositoryJpa;

    @Override
    public List<OutboxEvent> findPendingEvents() {
        return outboxRepositoryJpa.findTop100ByStatusOrderByIdAsc(OutboxStatus.PENDING);
    }

    @Override
    public void save(OutboxEvent event) {
        outboxRepositoryJpa.save(event);
    }

    @Override
    public void markPublished(OutboxEvent event) {
        event.setStatus(OutboxStatus.PUBLISHED);
        event.setPublishedAt(Instant.now());
        outboxRepositoryJpa.save(event);
    }
}
