import { Product } from '../product.entity';
import {
    FindManyOptions,
    FindOneOptions,
    FindOptionsWhere,
} from 'typeorm';

export interface IProductRepositoryInterface {
    findAll(options?: FindManyOptions<Product>): Promise<Product[]>;
    findById(id: number): Promise<Product | null>;
    findOne(options: FindOneOptions<Product>): Promise<Product | null>;
    findOneBy(where: FindOptionsWhere<Product> | FindOptionsWhere<Product>[]): Promise<Product | null>;
    create(data: Partial<Product>): Promise<Product>;
    delete(id: number): Promise<void>;
    update(id: number, product: Partial<Product>): Promise<Product>;
    save(data: Product): Promise<Product>;
    save(data: Product): Promise<Product>;
    decrementQty(p: { id: number }, qty: number): Promise<void>;

}
