
package com.example.paymentservice.messaging;

import com.example.paymentservice.event.InventoryReservedEvent;
import com.example.paymentservice.service.PaymentService;
import lombok.RequiredArgsConstructor;
import lombok.extern.slf4j.Slf4j;
import org.springframework.amqp.rabbit.annotation.RabbitListener;
import org.springframework.stereotype.Component;

@Slf4j
@Component
@RequiredArgsConstructor
public class PaymentConsumer {

    private final PaymentService paymentService;

    @RabbitListener(queues = "${rabbitmq.queue.payment}")
    public void handleInventoryReserved(InventoryReservedEvent event) {
        log.info("Received InventoryReservedEvent: orderId={}", event.getOrderId());
        paymentService.processPayment(event);
    }
}