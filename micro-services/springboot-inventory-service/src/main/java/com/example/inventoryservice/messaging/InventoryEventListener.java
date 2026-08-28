package com.example.inventoryservice.messaging;

import com.example.inventoryservice.config.RabbitMQConfig;
import com.example.inventoryservice.event.OrderCancelledEvent;
import com.example.inventoryservice.event.OrderCreatedEvent;
import com.example.inventoryservice.repository.ProcessedEventRepository;
import com.example.inventoryservice.service.StockService;
import com.fasterxml.jackson.databind.ObjectMapper;
import lombok.RequiredArgsConstructor;
import lombok.extern.slf4j.Slf4j;
import org.springframework.amqp.rabbit.annotation.RabbitListener;
import org.springframework.stereotype.Component;
import org.springframework.transaction.annotation.Transactional;

@Component
@Slf4j
@RequiredArgsConstructor
public class InventoryEventListener {

    private final StockService stockService;
    private final ProcessedEventRepository processedEventRepo;
    private final ObjectMapper objectMapper;

    @RabbitListener(queues = RabbitMQConfig.ORDER_CREATED_QUEUE)
    @Transactional
    public void onOrderCreated(String payload) throws Exception {

        OrderCreatedEvent event = objectMapper.readValue(payload, OrderCreatedEvent.class);

        if (processedEventRepo.existsById(event.getEventId())) {
            log.info("Duplicate OrderCreatedEvent {} ignored", event.getEventId());
            return;
        }

        for (OrderCreatedEvent.OrderItem item : event.getItems()) {
            stockService.reserveStock(event.getOrderId(), item.getProductId(), item.getQuantity());
        }

        processedEventRepo.markProcessed(event.getEventId(), "ORDER_CREATED");
    }


    @RabbitListener(queues = RabbitMQConfig.ORDER_CANCELED_QUEUE)
    @Transactional
    public void onOrderCanceled(String payload) throws Exception {

        OrderCancelledEvent event = objectMapper.readValue(payload, OrderCancelledEvent.class);
        if (processedEventRepo.existsById(event.getEventId())) {
            log.info("Duplicate OrderCancelledEvent {} ignored", event.getEventId());
            return;
        }

        stockService.releaseStock(event.getOrderId(), event.getProductId(), event.getQuantity());

        processedEventRepo.markProcessed(event.getEventId(), "ORDER_CANCELLED");
    }
}
