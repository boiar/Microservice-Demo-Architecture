package com.example.paymentservice.service.impl;

import com.example.paymentservice.PaymentServiceApplication;
import com.example.paymentservice.dto.response.PaymentResponse;
import com.example.paymentservice.entity.Payment;
import com.example.paymentservice.enums.PaymentStatus;
import com.example.paymentservice.event.InventoryReservedEvent;
import com.example.paymentservice.exception.PaymentNotFoundException;
import com.example.paymentservice.mapper.PaymentMapper;
import com.example.paymentservice.repository.PaymentRepository;
import com.example.paymentservice.service.OutboxService;
import com.example.paymentservice.service.PaymentService;
import lombok.RequiredArgsConstructor;
import lombok.extern.slf4j.Slf4j;
import org.springframework.beans.factory.annotation.Value;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;
import org.springframework.transaction.support.TransactionTemplate;

import java.math.BigDecimal;
import java.util.List;
import java.util.stream.Collectors;


@Slf4j
@Service
@RequiredArgsConstructor
public class PaymentServiceImpl implements PaymentService {

    @Value("${payment.processing.max-amount:500.00}")
    private BigDecimal maxAllowedAmount;

    private final PaymentRepository paymentRepo;
    private final PaymentMapper mapper;
    private final TransactionTemplate transactionTemplate;


    @Override
    public void processPayment(InventoryReservedEvent event) {

        if (paymentRepo.existsByOrderId(event.getOrderId())){
            log.warn("Duplicate event for orderId={}, skipping", event.getOrderId());
            return;
        }

        log.info("Processing payment for orderId={}, amount={}", event.getOrderId(), event.getTotalAmount());

        // Payment row and the resulting event are written to the outbox in one
        // transaction; OutboxRelay publishes it afterwards.

        transactionTemplate.executeWithoutResult(status -> {
            Payment payment = Payment.builder()
                    .orderId(event.getOrderId())
                    .customerId(event.getCustomerId())
                    .amount(event.getTotalAmount())
                    .status(PaymentStatus.PENDING)
                    .build();

            // check if customer allow max amount or not

            if (event.getTotalAmount().compareTo(maxAllowedAmount) > 0) {

                String reason = "Amount %.2f exceeds the allowed limit of %.2f"
                        .formatted(event.getTotalAmount(), maxAllowedAmount);

                payment.setStatus(PaymentStatus.FAILED);
                payment.setFailureReason(reason);

                log.warn("Payment failed for orderId={}: {}", event.getOrderId(), reason);

            } else {
                payment.setStatus(PaymentStatus.COMPLETED);

                log.info("Payment completed for orderId={}", event.getOrderId());
            }
            paymentRepo.save(payment);

        });
    }

    @Override
    @Transactional(readOnly = true)
    public List<PaymentResponse> getAll() {
        return paymentRepo.findAll()
                .stream()
                .map(mapper::toDto)
                .collect(Collectors.toList());
    }

    @Override
    @Transactional(readOnly = true)
    public PaymentResponse getByOrderId(String orderId) {
        return paymentRepo.findByOrderId(orderId)
                .map(mapper::toDto)
                .orElseThrow(() ->
                        new PaymentNotFoundException(
                                "Payment not found for orderId: " + orderId));
    }
}
