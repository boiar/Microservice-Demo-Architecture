package com.example.paymentservice.repository;

import com.example.paymentservice.entity.OutboxEvent;
import com.example.paymentservice.enums.OutboxStatus;

import java.util.List;

public interface OutboxRepository {

    List<OutboxEvent> findPendingEvents();

    void save(OutboxEvent event);

    void markPublished(OutboxEvent event);
}
