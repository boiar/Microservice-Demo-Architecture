package com.example.paymentservice.messaging;

import com.example.paymentservice.entity.OutboxEvent;
import com.example.paymentservice.enums.OutboxStatus;
import com.example.paymentservice.repository.OutboxRepository;
import com.example.paymentservice.repository.jpa.OutboxRepositoryJpa;
import lombok.RequiredArgsConstructor;
import lombok.extern.slf4j.Slf4j;
import org.springframework.amqp.rabbit.core.RabbitTemplate;
import org.springframework.scheduling.annotation.Scheduled;
import org.springframework.stereotype.Component;

import java.time.Instant;
import java.util.List;

/**
 * Outbox Relay — reads PENDING outbox events from the DB every 5 seconds
 * and publishes them to RabbitMQ (Transactional Outbox Pattern).
 *
 * Flow:
 *  1. OutboxServiceImpl writes the event to outbox_events (PENDING) atomically
 *     inside the same DB transaction as the Payment record.
 *  2. This relay picks up PENDING events, publishes to RabbitMQ, then marks PUBLISHED.
 *  3. On failure, the event is marked FAILED so it can be retried or inspected.
 */
@Slf4j
@Component
@RequiredArgsConstructor
public class OutboxRelay {

    private final OutboxRepository outboxRepository;
    private final OutboxRepositoryJpa outboxRepositoryJpa;
    private final RabbitTemplate rabbitTemplate;

    @Scheduled(fixedDelay = 5000) // runs every 5 seconds
    public void relay() {
        List<OutboxEvent> pendingEvents = outboxRepository.findPendingEvents();

        if (pendingEvents.isEmpty()) {
            return;
        }

        log.debug("OutboxRelay: found {} pending event(s) to publish", pendingEvents.size());

        for (OutboxEvent event : pendingEvents) {
            try {
                rabbitTemplate.convertAndSend(
                        event.getTopic(),          // routing key (e.g. "payment.completed")
                        event.getMessageKey(),     // message body (serialized payload)
                        message -> {
                            message.getMessageProperties().setHeader("x-event-id", event.getEventId());
                            message.getMessageProperties().setHeader("x-event-type", event.getEventType());
                            return message;
                        }
                );

                event.setStatus(OutboxStatus.PUBLISHED);
                event.setPublishedAt(Instant.now());
                outboxRepositoryJpa.save(event);

                log.info("OutboxRelay: published eventId={}, type={}, topic={}",
                        event.getEventId(), event.getEventType(), event.getTopic());

            } catch (Exception ex) {
                event.setStatus(OutboxStatus.FAILED);
                outboxRepositoryJpa.save(event);
                log.error("OutboxRelay: failed to publish eventId={}, reason={}",
                        event.getEventId(), ex.getMessage(), ex);
            }
        }
    }
}
