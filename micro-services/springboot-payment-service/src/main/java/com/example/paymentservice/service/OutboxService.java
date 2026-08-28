package com.example.paymentservice.service;

public interface OutboxService {

    /**
     * Store an event to the outbox. MUST be called inside the same transaction
     * as the business change so the two commit atomically.
     *
     * @param topic topic the event will be published to
     * @param key   partition key (typically the aggregate id)
     * @param event the event payload; serialized to JSON
     */
    void storeEvent(String topic, String key, Object event);
}
