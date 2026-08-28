import { Entity, Column, PrimaryGeneratedColumn } from 'typeorm';

@Entity()
export class Product {
    @PrimaryGeneratedColumn()
    id: number;

    @Column()
    name: string;

    @Column('decimal', { precision: 10, scale: 2 })
    price: number;


    @Column()
    description: string;

    /**
     * Read-model projection from Inventory Service.
     * Inventory Service is the source of truth.
     */
    @Column({ name: 'available_qty', type: 'int', default: 0 })
    availableQty: number;
}
