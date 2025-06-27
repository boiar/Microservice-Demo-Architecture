import { Module } from '@nestjs/common';
import { TypeOrmModule } from '@nestjs/typeorm';
import { Wishlist } from './wishlist.entity';
import { Product } from '../products/product.entity';
// @ts-ignore
import { WishlistService } from './wishlist.service';
// @ts-ignore
import { WishlistController } from './wishlist.controller';
import { AuthModule } from '../auth/auth.module'; // if you have one

@Module({
    imports: [TypeOrmModule.forFeature([Wishlist, Product]), AuthModule],
    controllers: [WishlistController],
    providers: [WishlistService],
})
export class WishlistModule {}
