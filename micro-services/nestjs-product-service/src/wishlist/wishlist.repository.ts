// src/wishlist/wishlist.repository.ts

import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository, FindManyOptions, FindOptionsWhere } from 'typeorm';
import { Wishlist } from './wishlist.entity';
import { IWishlistRepositoryInterface } from './interfaces/wishlist-repository.interface';

@Injectable()
export class WishlistRepository implements IWishlistRepositoryInterface {
    constructor(
        @InjectRepository(Wishlist)
        private readonly repo: Repository<Wishlist>,
    ) {}

    find(options: FindManyOptions<Wishlist>) {
        return this.repo.find(options);
    }

    findOne(where: FindOptionsWhere<Wishlist>) {
        return this.repo.findOne({ where });
    }

    create(data: Partial<Wishlist>): Wishlist {
        return this.repo.create(data);
    }

    save(item: Wishlist): Promise<Wishlist> {
        return this.repo.save(item);
    }

    delete(where: FindOptionsWhere<Wishlist>) {
        return this.repo.delete(where);
    }
}
