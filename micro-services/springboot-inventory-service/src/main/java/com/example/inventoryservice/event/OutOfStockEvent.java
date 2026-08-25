package com.example.inventoryservice.event;

import lombok.*;

import java.time.Instant;

/** Published when a reservation attempt cannot be satisfied - lets Order/Notification react. */

@Getter
@Setter
@Builder
@NoArgsConstructor
@AllArgsConstructor
public class OutOfStockEvent {

    private Long orderId;
    private Long productId;
    /** Quantity requested by the order. */
    private int requestedQuantity;
    /** Quantity currently available for reservation. */
    private int availableQuantity;
    private Instant occurredAt;
}
