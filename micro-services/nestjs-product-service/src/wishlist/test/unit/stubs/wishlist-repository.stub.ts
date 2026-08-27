import { IWishlistRepositoryInterface } from '../../../repository/wishlist-repository.interface';
import { Wishlist } from '../../../entity/wishlist.entity';
import { Product } from '../../../../products/entity/product.entity';
import { FindManyOptions, FindOptionsWhere, DeleteResult } from 'typeorm';

export class WishlistRepositoryStub implements IWishlistRepositoryInterface {

    private items: Wishlist[] = [
        {
            id: 1,
            user_id: 1,
            product: { id: 101, name: 'Stub Product 1', availableQty: 5, price: 50, description: 'Desc 1' } as Product,
        } as Wishlist,
        {
            id: 2,
            user_id: 2,
            product: { id: 102, name: 'Stub Product 2', availableQty: 10, price: 99, description: 'Desc 2' } as Product,
        } as Wishlist,
    ];

    async find(options: FindManyOptions<Wishlist>): Promise<Wishlist[]> {
        const where = (options?.where ?? {}) as FindOptionsWhere<Wishlist>;
        return this.items.filter(item =>
            Object.entries(where).every(([key, value]) => item[key] === value),
        );
    }

    async findOne(where: FindOptionsWhere<Wishlist>): Promise<Wishlist | null> {
        return this.items.find(item =>
            Object.entries(where).every(([key, value]) => item[key] === value),
        ) ?? null;
    }

    create(data: Partial<Wishlist>): Wishlist {
        return { id: Date.now(), ...data } as Wishlist;
    }

    async save(item: Wishlist): Promise<Wishlist> {
        const index = this.items.findIndex(i => i.id === item.id);
        if (index !== -1) {
            this.items[index] = item;
        } else {
            this.items.push(item);
        }
        return item;
    }

    async delete(where: FindOptionsWhere<Wishlist>): Promise<DeleteResult> {
        const before = this.items.length;
        this.items = this.items.filter(item =>
            !Object.entries(where).every(([key, value]) => {
                if (key === 'product' && typeof value === 'object') {
                    return item.product?.id === (value as any).id;
                }
                return item[key] === value;
            }),
        );
        return { affected: before - this.items.length, raw: [] };
    }
}
