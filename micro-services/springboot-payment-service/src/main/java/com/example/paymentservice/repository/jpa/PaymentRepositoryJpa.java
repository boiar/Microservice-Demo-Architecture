package com.example.paymentservice.repository.jpa;

import com.example.paymentservice.entity.Payment;
import org.springframework.data.jpa.repository.JpaRepository;

import java.util.Optional;

public interface PaymentRepositoryJpa extends JpaRepository<Payment, Long> {

    Optional<Payment> findByOrderId(String orderId);

    boolean existsByOrderId(String orderId);

}
