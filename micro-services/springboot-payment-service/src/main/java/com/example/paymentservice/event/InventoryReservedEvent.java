package com.example.paymentservice.event;

import lombok.*;

import java.math.BigDecimal;
import java.time.Instant;

@Getter
@Setter
@NoArgsConstructor
@AllArgsConstructor
@Builder
public class InventoryReservedEvent {
    private String orderId;
    private String orderSlug;
    private String customerId;
    private BigDecimal totalAmount;
    private Instant timestamp;
}
