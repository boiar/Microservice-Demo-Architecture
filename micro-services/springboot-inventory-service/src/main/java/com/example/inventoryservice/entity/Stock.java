package com.example.inventoryservice.entity;

import com.example.inventoryservice.entity.embeddable.Audit;
import com.example.inventoryservice.entity.embeddable.StockLocation;
import com.example.inventoryservice.enums.InventoryStatus;
import jakarta.persistence.*;
import lombok.*;


@Entity
@Table(name = "stocks", uniqueConstraints = @UniqueConstraint(columnNames = "product_id"))
@Setter
@Getter
@Builder
@NoArgsConstructor
@AllArgsConstructor
public class Stock {
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    @Column(name = "product_id", nullable = false, unique = true)
    private Long productId;

    @Column(nullable = false)
    @Builder.Default
    private int quantityAvailable = 0;

    @Column(nullable = false)
    @Builder.Default
    private int quantityReserved = 0;

    @Embedded
    private StockLocation location;

    @Enumerated(EnumType.STRING)
    @Column(nullable = false)
    private InventoryStatus status;

    /** Optimistic locking to guard against concurrent reserve/release races. */
    @Version
    private Long version;

    /** Quantity that can still be reserved right now. */
    @Transient
    public int getQuantityFree() {
        return quantityAvailable - quantityReserved;
    }

    private void recalculateStatus() {
        int free = getQuantityFree();
        if (free <= 0) {
            this.status = InventoryStatus.OUT_OF_STOCK;
        } else if (free <= 10) {
            this.status = InventoryStatus.LOW_STOCK;
        } else {
            this.status = InventoryStatus.IN_STOCK;
        }
    }


    /** Audit Time **/
    @Embedded
    private Audit audit = new Audit();

    @PrePersist
    void prePersist() {
        audit.prePersist();
    }

    @PreUpdate
    void preUpdate(){audit.preUpdate();}

}
