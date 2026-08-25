package com.example.inventoryservice.messaging;


import com.example.inventoryservice.config.RabbitMQConfig;
import com.example.inventoryservice.entity.OutboxEvent;
import lombok.RequiredArgsConstructor;
import lombok.extern.slf4j.Slf4j;
import org.springframework.amqp.rabbit.core.RabbitTemplate;
import org.springframework.stereotype.Component;

@Component
@RequiredArgsConstructor
public class InventoryEventPublisher {

    private final RabbitTemplate rabbitTemplate;

    public void publish(OutboxEvent event) {
        String routingKey = "inventory." + event.getEventType().toLowerCase();

        rabbitTemplate.convertAndSend(
                RabbitMQConfig.INVENTORY_EXCHANGE,
                routingKey,
                event.getPayload(),
                message -> {
                    message.getMessageProperties().setHeader("eventId", event.getId().toString());
                    message.getMessageProperties().setHeader("eventType", event.getEventType());
                    return message;
                });
    }

}
