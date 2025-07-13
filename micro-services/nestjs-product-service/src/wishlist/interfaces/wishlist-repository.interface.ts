// src/wishlist/interfaces/wishlist-repository.interface.ts
export const IWishlistRepositoryInterface = Symbol('IWishlistRepositoryInterface');

export interface IWishlistRepositoryInterface {
    find(...args: any[]): any;
    findOne(...args: any[]): any;
    create(...args: any[]): any;
    save(...args: any[]): any;
    delete(...args: any[]): any;
}
