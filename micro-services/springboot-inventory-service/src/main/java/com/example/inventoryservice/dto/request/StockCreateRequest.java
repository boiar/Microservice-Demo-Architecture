package com.example.inventoryservice.dto.request;

import jakarta.validation.constraints.Min;
import lombok.AllArgsConstructor;
import lombok.Getter;
import lombok.NoArgsConstructor;
import lombok.Setter;
import jakarta.validation.constraints.NotNull;

@Getter
@Setter
@NoArgsConstructor
@AllArgsConstructor
public class StockCreateRequest {
    @NotNull
    private Long productId;

    @NotNull
    @Min(0)
    private Integer quantityAvailable;

    private String warehouseCode;
    private String aisle;
    private String bin;
}
