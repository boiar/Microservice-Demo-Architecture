package com.example.inventoryservice.mapper;

import com.example.inventoryservice.dto.request.StockCreateRequest;
import com.example.inventoryservice.dto.response.StockResponse;
import com.example.inventoryservice.entity.Stock;
import com.example.inventoryservice.entity.embeddable.StockLocation;
import org.springframework.stereotype.Component;

@Component
public class StockMapper {

    public StockResponse toResponse(Stock stock) {
        return StockResponse.builder()
                .id(stock.getId())
                .productId(stock.getProductId())
                .quantityAvailable(stock.getQuantityAvailable())
                .quantityReserved(stock.getQuantityReserved())
                .quantityFree(stock.getQuantityFree())
                .status(stock.getStatus())
                .warehouseCode(stock.getLocation() != null ? stock.getLocation().getWarehouseCode() : null)
                .updatedAt(stock.getAudit().getCreatedAt())
                .build();
    }

    public Stock toEntity(StockCreateRequest request) {
        return Stock.builder()
                .productId(request.getProductId())
                .quantityAvailable(request.getQuantityAvailable())
                .quantityReserved(0)
                .location(StockLocation.builder()
                        .warehouseCode(request.getWarehouseCode())
                        .aisle(request.getAisle())
                        .bin(request.getBin())
                        .build())
                .build();
    }

}
