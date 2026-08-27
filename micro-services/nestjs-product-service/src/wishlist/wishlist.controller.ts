import { Controller, Delete, Get, Inject, Param, Post, Req, UseGuards } from '@nestjs/common';
import { AuthGuard } from '@nestjs/passport';
import { IWishlistServiceInterface } from './service/iwishlist.service.interface';

@UseGuards(AuthGuard('jwt'))
@Controller('wishlist')
export class WishlistController {

    constructor(
        @Inject('IWishlistService')
        private readonly wishlistService: IWishlistServiceInterface,
    ) {}


    @Get()
    getAll(@Req() req: Request) {
        const user = (req as any).user;
        return this.wishlistService.getAll(user.userId);
    }


    @Post(':productId')
    add(@Param('productId') productId: string, @Req() req: Request) {
        const user = (req as any).user;
        return this.wishlistService.add(user.userId, Number(productId));
    }


    @Delete(':productId')
    remove(@Param('productId') productId: string, @Req() req: Request) {
        const user = (req as any).user;
        return this.wishlistService.remove(user.userId, Number(productId));
    }
}