import {WishlistController} from "./wishlist.controller";
import {IWishlistInterface} from "./iwishlist.interface";

// @ts-ignore
import {Test, TestingModule} from "@nestjs/testing";
import {WishlistService} from "./wishlist.service";
import {StubWishlistService} from "./stubs/wishlist.service.stub";


describe('WishlistController', () => {
    let controller: WishlistController;
    let service: IWishlistInterface;

    const mockRequest = (userId: number): any => ({
        user: { userId },
    });


    beforeEach(async () => {
        const module: TestingModule = await Test.createTestingModule({
            controllers: [WishlistController],
            providers: [
                {
                    provide: WishlistService,
                    useClass: StubWishlistService,
                },
            ],
        }).compile();

        controller = module.get<WishlistController>(WishlistController);
        service = module.get<IWishlistInterface>(WishlistService);
    });

    it('should be defined', () => {
        expect(controller).toBeDefined();
    });
    
    describe('getAll', () => {
        it('should return wishlist items for a user', async () => {
            
            const result = await controller.getAll(mockRequest(1));
            expect(result).toHaveLength(1);
            expect(result[0].product.name).toEqual('Test Product');
        });
    });


    describe('add', () => {
        it('should add a product to wishlist', async () => {
            const result = await controller.add('202', mockRequest(1));
            expect(result.user_id).toEqual(1);
            expect(result.product.id).toEqual(202);
        });
    });


    describe('remove', () => {
        it('should remove a product from wishlist', async () => {
            const result = await controller.remove('101', mockRequest(1));
            expect(result).toEqual({ affected: 1 });
        });
    });
    
})