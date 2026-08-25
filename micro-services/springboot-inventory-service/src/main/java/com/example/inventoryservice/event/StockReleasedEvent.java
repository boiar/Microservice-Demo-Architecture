package com.example.inventoryservice.event;

import lombok.*;

import java.time.Instant;

/** Published after a reservation is released (payment failed / order cancelled). */

@Getter
@Setter
@Builder
@NoArgsConstructor
@AllArgsConstructor
public class StockReleasedEvent {

    private Long orderId;
    private Long productId;
    private int quantityReleased;
    private Instant releasedAt;
}
