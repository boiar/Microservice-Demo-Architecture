import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository, FindOneOptions, FindManyOptions } from 'typeorm';
import { FindOptionsWhere } from 'typeorm/find-options/FindOptionsWhere';
import { Product } from './product.entity';
import { IProductRepositoryInterface } from './interfaces/product-repository.interface';

@Injectable()
export class ProductRepository implements IProductRepositoryInterface {
    constructor(
        @InjectRepository(Product)
        private readonly repo: Repository<Product>,
    ) {}

    findAll(options?: FindManyOptions<Product>): Promise<Product[]> {
        return this.repo.find(options);
    }

    findById(id: number): Promise<Product | null> {
        return this.repo.findOne({ where: { id } });
    }

    findOne(options: FindOneOptions<Product>): Promise<Product | null> {
        return this.repo.findOne(options);
    }

    findOneBy(
        where: FindOptionsWhere<Product> | FindOptionsWhere<Product>[],
    ): Promise<Product | null> {
        return this.repo.findOneBy(where);
    }

    async create(data: Partial<Product>): Promise<Product> {
        return this.repo.save(data);
    }

    async delete(id: number): Promise<void> {
        await this.repo.delete(id);
    }

    async update(id: number, product: Partial<Product>): Promise<Product> {
        await this.repo.update({ id }, product);
        return this.repo.findOne({ where: { id } });
    }

    save(data: Product): Promise<Product> {
        return this.repo.save(data);
    }

    async decrementQty(p: { id: number }, qty: number): Promise<void> {
        await this.repo.decrement({ id: p.id }, 'qty', qty);
    }



}
