import { Column, Entity, PrimaryGeneratedColumn, Unique } from "typeorm";

@Entity('inbox_events')
@Unique(['eventId'])
export class InboxEvent {
    @PrimaryGeneratedColumn()
    id: number;

    @Column({ name: 'event_id', nullable: false, length: 100 })
    eventId: string;

    @Column({ name: 'event_type', nullable: false, length: 100 })
    eventType: string;

    @Column({ nullable: false, type: 'json' })
    payload: object;

    @Column({ name: 'created_at', default: () => 'CURRENT_TIMESTAMP' })
    createdAt: Date;

    @Column({ name: 'processed_at', nullable: true, type: 'datetime', })
    processedAt: Date | null;
}
