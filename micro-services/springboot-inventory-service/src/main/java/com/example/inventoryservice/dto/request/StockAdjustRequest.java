package com.example.inventoryservice.dto.request;

import lombok.AllArgsConstructor;
import lombok.Getter;
import lombok.NoArgsConstructor;
import lombok.Setter;
import jakarta.validation.constraints.NotNull;

@Getter
@Setter
@NoArgsConstructor
@AllArgsConstructor
public class StockAdjustRequest{

    /**
     * Quantity change.
     * Positive values increase stock (replenishment).
     * Negative values decrease stock (damage, correction, shrinkage).
     */
    @NotNull
    private Integer quantityChange;
}
