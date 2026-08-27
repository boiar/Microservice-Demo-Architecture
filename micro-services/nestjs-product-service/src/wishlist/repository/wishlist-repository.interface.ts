import { Wishlist } from '../entity/wishlist.entity';
import { FindManyOptions, FindOptionsWhere, DeleteResult } from 'typeorm';

export interface IWishlistRepositoryInterface {
    find(options: FindManyOptions<Wishlist>): Promise<Wishlist[]>;
    findOne(where: FindOptionsWhere<Wishlist>): Promise<Wishlist | null>;
    create(data: Partial<Wishlist>): Wishlist;
    save(item: Wishlist): Promise<Wishlist>;
    delete(where: FindOptionsWhere<Wishlist>): Promise<DeleteResult>;
}
