package com.example.paymentservice.service;

import com.example.paymentservice.dto.response.PaymentResponse;
import com.example.paymentservice.event.InventoryReservedEvent;

import java.util.List;

public interface PaymentService {
    void processPayment(InventoryReservedEvent event);

    List<PaymentResponse> getAll();

    PaymentResponse getByOrderId(String orderId);
}
