package com.example.paymentservice.config;
    
public final class TopicConstants {

    private TopicConstants() {}

    public static final String EXCHANGE = "payment.exchange";

    public static final String INVENTORY_RESERVED_QUEUE = "payment.inventory.reserved";

    public static final String INVENTORY_RESERVED_ROUTING_KEY = "inventory.reserved";
}