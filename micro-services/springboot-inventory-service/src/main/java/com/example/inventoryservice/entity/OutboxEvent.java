package com.example.inventoryservice.entity;

import com.example.inventoryservice.entity.embeddable.Audit;
import com.example.inventoryservice.enums.OutboxStatus;
import jakarta.persistence.*;
import lombok.*;

import java.time.Instant;

@Entity
@Getter
@Setter
@Table(
        name = "outbox_events",
        indexes = {
                @Index(name = "idx_outbox_events_type", columnList = "event_type"),
                @Index(name = "idx_outbox_events_event_id", columnList = "event_id"),
                @Index(name = "idx_outbox_events_topic", columnList = "topic"),
                @Index(name = "idx_outbox_events_published", columnList = "published")
        }
)
@NoArgsConstructor
@AllArgsConstructor
@Builder
public class OutboxEvent {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Column(name = "id", nullable = false)
    private Long id;

    /** Unique id propagated as the {@code x-event-id} header for consumer idempotency. */
    @Column(name = "event_id", nullable = false, unique = true, length = 26)
    private String eventId;

    @Column(name = "topic", nullable = false, length = 100)
    private String topic;

    @Column(name = "message_key", length = 100)
    private String messageKey;


    @Column(name = "event_type", nullable = false)
    private String eventType;

    @Column(name = "payload", nullable = false, columnDefinition = "TEXT")
    private String payload;

    @Builder.Default
    @Enumerated(EnumType.STRING)
    @Column(nullable = false)
    private OutboxStatus status = OutboxStatus.PENDING;

    @Column(name = "published_at")
    private Instant publishedAt;

    @Embedded
    @Builder.Default
    private Audit audit = new Audit();

    @PrePersist
    void prePersist() {
        audit.prePersist();
    }
}
