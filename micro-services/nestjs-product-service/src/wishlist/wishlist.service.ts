import {Injectable, NotFoundException} from "@nestjs/common";
import {InjectRepository} from "@nestjs/typeorm";
import {Wishlist} from "./wishlist.entity";
import {Repository} from "typeorm";
import {Product} from "../products/product.entity";
import {IWishlistInterface} from "./iwishlist.interface";

@Injectable()
export class WishlistService implements IWishlistInterface {
    constructor(
        @InjectRepository(Wishlist)
        private readonly wishlistRepo: Repository<Wishlist>,
        @InjectRepository(Product)
        private readonly productRepo: Repository<Product>,
    ) {}


    async getAll(userId: number) {
        return this.wishlistRepo.find({
            where: { user_id : userId },
            relations: ['product'],
        });
    }


    async add(userId: number, productId: number) {
        const product = await this.productRepo.findOneBy({ id: productId });
        if (!product) throw new NotFoundException('Product not found');

        const item = this.wishlistRepo.create({ user_id: userId, product });
        return this.wishlistRepo.save(item);
    }


    async remove(userId: number, productId: number) {
        return this.wishlistRepo.delete({ user_id: userId, product: { id: productId } });
    }


}