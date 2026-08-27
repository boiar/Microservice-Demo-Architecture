import { Wishlist } from '../entity/wishlist.entity';

export interface IWishlistServiceInterface {
    getAll(userId: number): Promise<Wishlist[]>;
    add(userId: number, productId: number): Promise<Wishlist>;
    remove(userId: number, productId: number): Promise<any>;
}
