import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository, FindManyOptions, FindOptionsWhere, DeleteResult } from 'typeorm';
import { Wishlist } from '../../entity/wishlist.entity';
import { IWishlistRepositoryInterface } from '../wishlist-repository.interface';

@Injectable()
export class WishlistRepository implements IWishlistRepositoryInterface {

    constructor(
        @InjectRepository(Wishlist)
        private readonly repo: Repository<Wishlist>,
    ) {}

    find(options: FindManyOptions<Wishlist>): Promise<Wishlist[]> {
        return this.repo.find(options);
    }

    findOne(where: FindOptionsWhere<Wishlist>): Promise<Wishlist | null> {
        return this.repo.findOne({ where });
    }

    create(data: Partial<Wishlist>): Wishlist {
        return this.repo.create(data);
    }

    save(item: Wishlist): Promise<Wishlist> {
        return this.repo.save(item);
    }

    delete(where: FindOptionsWhere<Wishlist>): Promise<DeleteResult> {
        return this.repo.delete(where);
    }

}
