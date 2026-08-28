package com.example.inventoryservice.entity;


import jakarta.persistence.*;
import lombok.*;

import java.time.Instant;
import java.util.UUID;

@Entity
@Getter
@Setter
@Table(
        name = "processed_events",
        indexes = {
                @Index(name = "idx_processed_events_type", columnList = "event_type"),
                @Index(name = "idx_processed_events_processed_at", columnList = "processed_at")
        }
)
@NoArgsConstructor
@AllArgsConstructor
@Builder
public class ProcessedEvent {

    @Id
    @Column(name = "event_id", nullable = false, updatable = false)
    private UUID eventId;

    @Column(name = "event_type")
    private String eventType;

    @Column(name = "metadata", nullable = true)
    private String metadata;


    @Column(name = "processed_at", nullable = false)
    private Instant processedAt;

    @PrePersist
    void prePersist() {
        if (processedAt == null) {
            processedAt = Instant.now();
        }
    }

}
