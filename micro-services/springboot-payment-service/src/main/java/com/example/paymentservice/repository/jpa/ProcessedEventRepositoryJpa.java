package com.example.paymentservice.repository.jpa;

import com.example.paymentservice.entity.ProcessedEvent;
import org.springframework.data.jpa.repository.JpaRepository;

import java.util.UUID;

public interface ProcessedEventRepositoryJpa extends JpaRepository<ProcessedEvent, UUID> {
}
