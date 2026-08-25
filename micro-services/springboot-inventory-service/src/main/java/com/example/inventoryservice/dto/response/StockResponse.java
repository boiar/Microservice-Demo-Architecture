package com.example.inventoryservice.dto.response;

import com.example.inventoryservice.enums.InventoryStatus;
import lombok.*;

import java.time.Instant;

@Getter
@Setter
@NoArgsConstructor
@AllArgsConstructor
@Builder
public class StockResponse {
    private Long id;
    private Long productId;
    private int quantityAvailable;
    private int quantityReserved;
    private int quantityFree;
    private InventoryStatus status;
    private String warehouseCode;
    private Instant updatedAt;
}
