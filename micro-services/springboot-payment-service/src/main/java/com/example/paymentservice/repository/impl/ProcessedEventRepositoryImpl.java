package com.example.paymentservice.repository.impl;

import com.example.paymentservice.entity.Payment;
import com.example.paymentservice.entity.ProcessedEvent;
import com.example.paymentservice.repository.PaymentRepository;
import com.example.paymentservice.repository.ProcessedEventRepository;
import com.example.paymentservice.repository.jpa.PaymentRepositoryJpa;
import com.example.paymentservice.repository.jpa.ProcessedEventRepositoryJpa;
import lombok.RequiredArgsConstructor;
import org.springframework.stereotype.Repository;

import java.util.Optional;
import java.util.UUID;

@Repository
@RequiredArgsConstructor
public class ProcessedEventRepositoryImpl implements ProcessedEventRepository {

    private final ProcessedEventRepositoryJpa processedEventRepositoryJpa;


    @Override
    public boolean existsById(UUID eventId) {
        return processedEventRepositoryJpa.existsById(eventId);
    }

    @Override
    public void save(ProcessedEvent event) {
        processedEventRepositoryJpa.save(event);
    }
}
