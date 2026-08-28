package com.example.paymentservice.repository.impl;

import com.example.paymentservice.entity.Payment;
import com.example.paymentservice.repository.PaymentRepository;
import com.example.paymentservice.repository.jpa.PaymentRepositoryJpa;
import lombok.RequiredArgsConstructor;
import org.springframework.stereotype.Repository;

import java.util.List;
import java.util.Optional;

@Repository
@RequiredArgsConstructor
public class PaymentRepositoryImpl implements PaymentRepository {

    private final PaymentRepositoryJpa paymentRepositoryJpa;

    @Override
    public List<Payment> findAll() {
        return paymentRepositoryJpa.findAll();
    }

    @Override
    public Optional<Payment> findByOrderId(String orderId) {
        return paymentRepositoryJpa.findByOrderId(orderId);
    }

    @Override
    public boolean existsByOrderId(String orderId) {
        return paymentRepositoryJpa.existsByOrderId(orderId);
    }

    @Override
    public void save(Payment payment) {
        paymentRepositoryJpa.save(payment);
    }
}
