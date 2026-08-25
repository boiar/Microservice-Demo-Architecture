package com.example.paymentservice.repository.jpa;

import com.example.paymentservice.entity.OutboxEvent;
import com.example.paymentservice.enums.OutboxStatus;
import org.springframework.data.jpa.repository.JpaRepository;

import java.util.List;

public interface OutboxRepositoryJpa extends JpaRepository<OutboxEvent, Long> {

    List<OutboxEvent> findTop100ByStatusOrderByIdAsc(OutboxStatus status);
}
