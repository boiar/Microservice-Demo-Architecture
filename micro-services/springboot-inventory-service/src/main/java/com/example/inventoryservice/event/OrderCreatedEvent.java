package com.example.inventoryservice.event;

import lombok.AllArgsConstructor;
import lombok.Getter;
import lombok.NoArgsConstructor;
import lombok.Setter;

import java.util.List;
import java.util.UUID;

/**
 * Inbound event consumed from the order service's exchange.
 * Triggers a stock reservation attempt for each line item.
 */
@Getter
@Setter
@NoArgsConstructor
@AllArgsConstructor
public class OrderCreatedEvent {

    private UUID eventId;
    private Long orderId;
    private List<OrderItem> items;

    @Getter
    @Setter
    @NoArgsConstructor
    @AllArgsConstructor
    public static class OrderItem {
        private Long productId;
        private int quantity;
    }
}
