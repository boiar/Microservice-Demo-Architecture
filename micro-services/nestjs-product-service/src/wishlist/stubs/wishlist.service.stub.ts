import {IWishlistInterface} from "../iwishlist.interface";
import { Wishlist } from '../wishlist.entity';
import { Product } from '../../products/product.entity'

export class StubWishlistService implements IWishlistInterface {

    private wishlist: Wishlist[] = [
        {
            id: 1,
            user_id: 1,
            product: { id: 101, name: 'Test Product', qty: 1, price: 50 } as Product,
        } as Wishlist,
    ];

    async getAll(userId: number): Promise<Wishlist[]> {
        return this.wishlist.filter(item => item.user_id === userId);
    }

    async add(userId: number, productId: number): Promise<Wishlist> {
        const product = { id: productId, name: 'Stub Product', qty: 1, price: 100 } as Product;
        const item = { id: Date.now(), user_id: userId, product } as Wishlist;
        this.wishlist.push(item);
        return item;
    }

    async remove(userId: number, productId: number): Promise<any> {
        this.wishlist = this.wishlist.filter(item => item.user_id !== userId || item.product.id !== productId);
        return { affected: 1 };
    }
}
