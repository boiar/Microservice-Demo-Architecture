package com.example.inventoryservice.service;

import java.util.UUID;

public interface IdempotencyService {

    /** @return true if this event id has already been handled (or is null). */
    boolean checkPaymentProcessed(UUID eventId);

    /** Record the event id as handled. No-op when eventId is null. */
    void markProcessed(UUID eventId, String eventType);
}
