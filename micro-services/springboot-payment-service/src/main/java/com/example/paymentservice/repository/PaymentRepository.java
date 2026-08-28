package com.example.paymentservice.repository;

import com.example.paymentservice.entity.Payment;

import java.util.List;
import java.util.Optional;

public interface PaymentRepository {

    List<Payment> findAll();

    Optional<Payment> findByOrderId(String orderId);

    boolean existsByOrderId(String orderId);

    void save(Payment payment);

}
