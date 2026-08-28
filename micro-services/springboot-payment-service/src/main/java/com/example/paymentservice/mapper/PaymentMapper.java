package com.example.paymentservice.mapper;

import com.example.paymentservice.dto.response.PaymentResponse;
import com.example.paymentservice.entity.Payment;
import org.springframework.stereotype.Component;

@Component
public class PaymentMapper {

    public PaymentResponse toDto(Payment payment) {
        if (payment == null) {
            return null;
        }

        PaymentResponse response = new PaymentResponse();
        response.setId(payment.getId());
        response.setOrderId(payment.getOrderId());
        response.setCustomerId(payment.getCustomerId());
        response.setTotalAmount(payment.getAmount());
        response.setStatus(payment.getStatus());

        return response;
    }

}