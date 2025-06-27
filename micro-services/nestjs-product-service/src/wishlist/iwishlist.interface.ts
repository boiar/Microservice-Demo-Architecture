import { Wishlist } from './wishlist.entity';

export interface IWishlistInterface {
    getAll(userId: number): Promise<Wishlist[]>;
    add(userId: number, productId: number): Promise<Wishlist>;
    remove(userId: number, productId: number): Promise<any>;
}
