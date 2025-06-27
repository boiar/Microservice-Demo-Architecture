import {Controller, Delete, Get, Param, Post, Req, UseGuards} from "@nestjs/common";
import {AuthGuard} from "@nestjs/passport";
import {WishlistService} from "./wishlist.service";

@UseGuards(AuthGuard('jwt'))
@Controller('wishlist')
export class WishlistController {

    constructor(private readonly wishlistService: WishlistService) {}


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