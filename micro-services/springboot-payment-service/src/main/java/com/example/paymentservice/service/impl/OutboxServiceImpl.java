package com.example.paymentservice.service.impl;

import com.example.paymentservice.entity.OutboxEvent;
import com.example.paymentservice.enums.OutboxStatus;
import com.example.paymentservice.repository.OutboxRepository;
import com.example.paymentservice.service.OutboxService;
import com.example.paymentservice.service.UlidGenerator;
import lombok.RequiredArgsConstructor;
import lombok.extern.slf4j.Slf4j;
import org.springframework.stereotype.Service;
import com.fasterxml.jackson.core.JsonProcessingException;
import com.fasterxml.jackson.databind.ObjectMapper;

@Slf4j
@Service
@RequiredArgsConstructor
public class OutboxServiceImpl implements OutboxService {

    private final OutboxRepository outboxRepo;
    private final ObjectMapper objectMapper;
    private final UlidGenerator ulidGenerator;

    @Override
    public void storeEvent(String topic, String key, Object event) {
        try {

            String eventId = ulidGenerator.generate();
            OutboxEvent outboxEvent = OutboxEvent.builder()
                    .eventId(eventId)
                    .topic(topic)
                    .messageKey(key)
                    .eventType(event.getClass().getSimpleName())
                    .payload(objectMapper.writeValueAsString(event))
                    .status(OutboxStatus.PENDING)
                    .build();

            outboxRepo.save(outboxEvent);
            log.debug(
                    "Outbox event stored: eventId={}, type={}, topic={}, key={}",
                    eventId,
                    outboxEvent.getEventType(),
                    topic,
                    key
            );

        } catch (JsonProcessingException e) {
            throw new IllegalStateException("Failed to serialize outbox event: " + event.getClass(), e);
        }

    }
}
