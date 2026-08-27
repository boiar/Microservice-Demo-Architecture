import { Module } from '@nestjs/common';
import { TypeOrmModule } from '@nestjs/typeorm';
import { Wishlist } from './entity/wishlist.entity';
import { Product } from '../products/entity/product.entity';
import { WishlistController } from './wishlist.controller';
import { WishlistService } from './service/impl/wishlist.service';
import { WishlistRepository } from './repository/impl/wishlist.repository';
import { AuthModule } from '../auth/auth.module';

@Module({
    imports: [TypeOrmModule.forFeature([Wishlist, Product]), AuthModule],
    controllers: [WishlistController],
    providers: [
        {
            provide: 'IWishlistRepository',
            useClass: WishlistRepository,
        },
        {
            provide: 'IWishlistService',
            useClass: WishlistService,
        },
    ],
    exports: [
        'IWishlistRepository',
        'IWishlistService',
    ],
})
export class WishlistModule { }
