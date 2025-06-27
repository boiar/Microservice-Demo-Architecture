import {Column, Entity, ManyToOne, PrimaryGeneratedColumn, Unique} from "typeorm";
import {Product} from "../products/product.entity";

@Entity()
@Unique(['user_id', 'product'])
export class Wishlist {
    @PrimaryGeneratedColumn()
    id: number;

    @Column()
    user_id: number;

    @ManyToOne(() => Product, { eager: true, onDelete: 'CASCADE' })
    product: Product;

}