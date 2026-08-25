package com.example.paymentservice.repository;

import com.example.paymentservice.entity.ProcessedEvent;

import java.util.UUID;

public interface ProcessedEventRepository {

    boolean existsById(UUID eventId);

    void save(ProcessedEvent event);

}
