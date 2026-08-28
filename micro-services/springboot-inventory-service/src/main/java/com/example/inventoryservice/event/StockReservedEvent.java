package com.example.inventoryservice.event;

import lombok.*;

import java.time.Instant;

/** Published after a reservation succeeds, so Payment/Order can proceed. */

@Getter
@Setter
@Builder
@NoArgsConstructor
@AllArgsConstructor
public class StockReservedEvent {
    private Long orderId;
    private Long productId;
    private int quantityReserved;
    private Instant reservedAt;
}
