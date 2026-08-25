package com.example.paymentservice.dto.response;

import com.example.paymentservice.enums.PaymentStatus;
import lombok.*;

import java.math.BigDecimal;
import java.time.Instant;

@Getter
@Setter
@NoArgsConstructor
@AllArgsConstructor
@Builder
public class PaymentResponse {
    private Long id;
    private String orderId;
    private String customerId;
    private BigDecimal totalAmount;
    private PaymentStatus status;
    private String failureReason;
    private Instant createdAt;
}
