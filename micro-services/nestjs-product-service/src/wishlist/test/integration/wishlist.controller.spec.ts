import { Test, TestingModule } from '@nestjs/testing';
import { NotFoundException } from '@nestjs/common';
import { getRepositoryToken } from '@nestjs/typeorm';
import { WishlistController } from '../../wishlist.controller';
import { WishlistService } from '../../service/impl/wishlist.service';
import { IWishlistServiceInterface } from '../../service/iwishlist.service.interface';
import { WishlistRepositoryStub } from '../unit/stubs/wishlist-repository.stub';
import { Product } from '../../../products/entity/product.entity';

describe('WishlistController (integration)', () => {
    let controller: WishlistController;
    let service: IWishlistServiceInterface;

    const mockProductRepo = {
        findOneBy: jest.fn(),
    };

    const mockRequest = (userId: number): any => ({ user: { userId } });

    beforeEach(async () => {
        const module: TestingModule = await Test.createTestingModule({
            controllers: [WishlistController],
            providers: [
                WishlistService,
                {
                    provide: 'IWishlistRepository',
                    useClass: WishlistRepositoryStub,
                },
                {
                    provide: 'IWishlistService',
                    useClass: WishlistService,
                },
                {
                    provide: getRepositoryToken(Product),
                    useValue: mockProductRepo,
                },
            ],
        }).compile();

        controller = module.get<WishlistController>(WishlistController);
        service = module.get<IWishlistServiceInterface>('IWishlistService');
    });

    afterEach(() => {
        jest.clearAllMocks();
    });

    it('should be defined', () => {
        expect(controller).toBeDefined();
    });


    describe('getAll', () => {
        it('should return wishlist items for the authenticated user', async () => {
            const result = await controller.getAll(mockRequest(1));

            expect(result).toHaveLength(1);
            expect(result[0].product.name).toBe('Stub Product 1');
        });

        it('should return empty array for a user with no wishlist', async () => {
            const result = await controller.getAll(mockRequest(999));
            expect(result).toEqual([]);
        });
    });


    describe('add', () => {
        it('should add a product to wishlist and return the new item', async () => {
            const product = { id: 300, name: 'Added Product', qty: 5, price: 49, description: 'Test' } as Product;
            mockProductRepo.findOneBy.mockResolvedValueOnce(product);

            const result = await controller.add('300', mockRequest(1));

            expect(result.user_id).toBe(1);
            expect(result.product.id).toBe(300);
        });

        it('should throw NotFoundException when product does not exist', async () => {
            mockProductRepo.findOneBy.mockResolvedValueOnce(null);
            await expect(controller.add('999', mockRequest(1))).rejects.toThrow(NotFoundException);
        });
    });


    describe('remove', () => {
        it('should remove an existing wishlist item and return affected = 1', async () => {
            const result = await controller.remove('101', mockRequest(1));
            expect(result.affected).toBe(1);
        });

        it('should return affected = 0 when item does not exist', async () => {
            const result = await controller.remove('999', mockRequest(1));
            expect(result.affected).toBe(0);
        });
    });
});
