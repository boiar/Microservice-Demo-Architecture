package com.example.paymentservice.entity;
import com.example.paymentservice.entity.embeddable.Audit;
import com.example.paymentservice.enums.PaymentStatus;
import jakarta.persistence.*;
import lombok.*;

import java.math.BigDecimal;


@Entity
@Table(name = "payments")
@Getter
@Setter
@NoArgsConstructor
@AllArgsConstructor
@Builder
public class Payment {
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Column(name = "id", nullable = false)
    private Long id;

    @Column(name = "order_id", nullable = false, unique = true)
    private String orderId;

    @Column(name = "customer_id", nullable = false)
    private String customerId;

    @Column(name = "amount", nullable = false)
    private BigDecimal amount;

    @Enumerated(EnumType.STRING)
    @Column(name = "status", nullable = false)
    private PaymentStatus status;

    @Column(name = "failure_reason")
    private String failureReason;

    /* Audit Time */
    @Embedded
    private Audit audit = new Audit();

    @PrePersist
    void prePersist() {
        audit.prePersist();
    }

    @PreUpdate
    void preUpdate() {
        audit.preUpdate();
    }

}
