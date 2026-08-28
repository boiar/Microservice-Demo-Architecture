import { Injectable, NotFoundException, Inject } from '@nestjs/common';
import { Wishlist } from '../../entity/wishlist.entity';
import { IWishlistServiceInterface } from '../iwishlist.service.interface';
import { IWishlistRepositoryInterface } from '../../repository/wishlist-repository.interface';
import { Repository } from 'typeorm';
import { InjectRepository } from '@nestjs/typeorm';
import { Product } from '../../../products/entity/product.entity';

@Injectable()
export class WishlistService implements IWishlistServiceInterface {

    constructor(
        @Inject('IWishlistRepository')
        private readonly wishlistRepository: IWishlistRepositoryInterface,
        @InjectRepository(Product)
        private readonly productRepo: Repository<Product>,
    ) { }


    async getAll(userId: number): Promise<Wishlist[]> {
        return this.wishlistRepository.find({
            where: { user_id: userId },
            relations: ['product'],
        });
    }


    async add(userId: number, productId: number): Promise<Wishlist> {
        const product = await this.productRepo.findOneBy({ id: productId });
        if (!product) throw new NotFoundException('Product not found');

        const item = this.wishlistRepository.create({ user_id: userId, product });
        return this.wishlistRepository.save(item);
    }


    async remove(userId: number, productId: number): Promise<any> {
        return this.wishlistRepository.delete({ user_id: userId, product: { id: productId } });
    }

}
