package com.example.inventoryservice.service.impl;

import com.example.inventoryservice.dto.request.StockCreateRequest;
import com.example.inventoryservice.dto.response.StockResponse;
import com.example.inventoryservice.entity.OutboxEvent;
import com.example.inventoryservice.entity.Stock;
import com.example.inventoryservice.enums.EventTopic;
import com.example.inventoryservice.enums.EventType;
import com.example.inventoryservice.event.OutOfStockEvent;
import com.example.inventoryservice.event.StockReleasedEvent;
import com.example.inventoryservice.event.StockReservedEvent;
import com.example.inventoryservice.exception.InsufficientStockException;
import com.example.inventoryservice.exception.ProductNotFoundException;
import com.example.inventoryservice.exception.StockAlreadyExistsException;
import com.example.inventoryservice.mapper.StockMapper;
import com.example.inventoryservice.repository.OutboxRepository;
import com.example.inventoryservice.repository.StockRepository;
import com.example.inventoryservice.service.StockService;
import com.example.inventoryservice.service.UlidGenerator;
import lombok.RequiredArgsConstructor;
import lombok.SneakyThrows;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;
import com.fasterxml.jackson.databind.ObjectMapper;

import java.time.Instant;


@Service
@RequiredArgsConstructor
public class StockServiceImpl implements StockService {

    private final StockRepository stockRepo;
    private final OutboxRepository outboxRepo;
    private final StockMapper stockMapper;
    private final ObjectMapper objectMapper;
    private final UlidGenerator ulidGenerator;


    @Override
    @Transactional
    public StockResponse createStock(StockCreateRequest request) {
        stockRepo.findByProductId(request.getProductId()).ifPresent( s-> {
            throw new StockAlreadyExistsException(request.getProductId());
        });

        Stock stock = stockMapper.toEntity(request);

        return stockMapper.toResponse(stockRepo.save(stock));
    }

    @Override
    @Transactional(readOnly = true)
    public StockResponse getByProductId(Long productId) {
        Stock stock = stockRepo.findByProductId(productId)
                .orElseThrow(() -> new ProductNotFoundException(productId));
        return stockMapper.toResponse(stock);
    }

    @Override
    @Transactional
    public StockResponse adjustQuantity(Long productId, int quantityChange) {
        Stock stock = stockRepo.findByProductIdForUpdate(productId)
                .orElseThrow(() -> new ProductNotFoundException(productId));

        int newQuantity = stock.getQuantityAvailable() + quantityChange;
        if (newQuantity < 0) {
           throw new InsufficientStockException(
                   productId,
                   -quantityChange,
                   stock.getQuantityAvailable()
           );
        }

        stock.setQuantityAvailable(newQuantity);
        Stock savedStock = stockRepo.save(stock);

       return stockMapper.toResponse(savedStock);

    }

    @Override
    @Transactional
    public void reserveStock(Long orderId, Long productId, int quantity) {
        Stock stock = stockRepo.findByProductIdForUpdate(productId)
                .orElseThrow(() -> new ProductNotFoundException(productId));

        if (stock.getQuantityFree() < quantity) {
            storeOutboxEvent(stock, EventType.OUT_OF_STOCK, OutOfStockEvent.builder()
                    .orderId(orderId)
                    .productId(productId)
                    .requestedQuantity(quantity)
                    .availableQuantity(stock.getQuantityFree())
                    .occurredAt(Instant.now())
                    .build());
            throw new InsufficientStockException(productId, quantity, stock.getQuantityFree());
        }

        stock.setQuantityReserved(stock.getQuantityReserved() + quantity);
        stockRepo.save(stock);

        storeOutboxEvent(stock, EventType.STOCK_RESERVED, StockReservedEvent.builder()
                .orderId(orderId)
                .productId(productId)
                .quantityReserved(quantity)
                .reservedAt(Instant.now())
                .build());

    }


    @Override
    @Transactional
    public void releaseStock(Long orderId, Long productId, int quantity) {
        Stock stock = stockRepo.findByProductIdForUpdate(productId)
                .orElseThrow(() -> new ProductNotFoundException(productId));

        int newReserved = stock.getQuantityReserved() - quantity;

        stock.setQuantityReserved(newReserved);
        stockRepo.save(stock);

        storeOutboxEvent(stock, EventType.STOCK_RELEASED, StockReleasedEvent.builder()
                .orderId(orderId)
                .productId(productId)
                .quantityReleased(quantity)
                .releasedAt(Instant.now())
                .build());
    }

    /**
     * Saves the event to the outbox in the same transaction as the stock update.
     * A separate relay publishes it to MessageQueue later.
     */
    @SneakyThrows
    private void storeOutboxEvent (Stock stock, EventType eventType, Object payload) {
        OutboxEvent event = OutboxEvent.builder()
                .eventId(ulidGenerator.generate())
                .topic(EventTopic.INVENTORY_STOCK.value())
                .messageKey(String.valueOf(stock.getProductId()))
                .eventType(eventType.name())
                .payload(objectMapper.writeValueAsString(payload))
                .build();

    }
}
