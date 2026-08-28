package com.example.inventoryservice.entity.embeddable;

import jakarta.persistence.Embeddable;
import lombok.*;

@Embeddable
@Setter
@Getter
@Builder
@NoArgsConstructor
@AllArgsConstructor
public class StockLocation {

    private String warehouseCode;
    private String aisle;   /*path*/
    private String bin;
}