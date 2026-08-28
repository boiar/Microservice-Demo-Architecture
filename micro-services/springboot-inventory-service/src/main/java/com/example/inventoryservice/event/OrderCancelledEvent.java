package com.example.inventoryservice.event;

import lombok.AllArgsConstructor;
import lombok.Getter;
import lombok.NoArgsConstructor;
import lombok.Setter;

import java.util.UUID;

/**
 * Inbound event consumed from the payment service's exchange when a payment
 * fails or an order is cancelled - reserved stock must be released back to
 * available.
 */
@Getter
@Setter
@NoArgsConstructor
@AllArgsConstructor
public class OrderCancelledEvent {

    private UUID eventId;
    private Long orderId;
    private Long productId;
    private int quantity;
}
