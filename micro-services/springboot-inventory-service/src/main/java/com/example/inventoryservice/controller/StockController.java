package com.example.inventoryservice.controller;

import com.example.inventoryservice.dto.request.StockAdjustRequest;
import com.example.inventoryservice.dto.request.StockCreateRequest;
import com.example.inventoryservice.dto.response.StockResponse;
import com.example.inventoryservice.service.StockService;
import io.micrometer.core.instrument.config.validate.Validated;
import jakarta.validation.Valid;
import lombok.RequiredArgsConstructor;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

@RestController
@RequestMapping("/api/inventory")
@RequiredArgsConstructor
public class StockController {

    private final StockService stockService;

    @PostMapping
    public ResponseEntity<StockResponse> createStock(@Valid @RequestBody StockCreateRequest request) {
        StockResponse created = stockService.createStock(request);
        return ResponseEntity.status(HttpStatus.CREATED).body(created);
    }

    @GetMapping("/{productId}")
    public StockResponse getStock(@PathVariable Long productId) {
        return stockService.getByProductId(productId);
    }

    @PatchMapping("/{productId}/adjust")
    public StockResponse adjustStock(@PathVariable Long productId,
                                        @Valid @RequestBody StockAdjustRequest request) {
        return stockService.adjustQuantity(productId, request.getQuantityChange());
    }
}
