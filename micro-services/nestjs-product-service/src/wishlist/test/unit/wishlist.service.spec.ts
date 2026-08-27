import { Test, TestingModule } from '@nestjs/testing';
import { WishlistService } from '../../service/impl/wishlist.service';
import { IWishlistRepositoryInterface } from '../../repository/wishlist-repository.interface';
import { getRepositoryToken } from '@nestjs/typeorm';
import { Product } from '../../../products/entity/product.entity';
import { NotFoundException } from '@nestjs/common';
import { WishlistRepositoryStub } from './stubs/wishlist-repository.stub';

describe('WishlistService', () => {
    let service: WishlistService;
    let repo: IWishlistRepositoryInterface;

    const mockProductRepo = {
        findOneBy: jest.fn(),
    };

    beforeEach(async () => {
        const module: TestingModule = await Test.createTestingModule({
            providers: [
                WishlistService,
                { provide: 'IWishlistRepository', useClass: WishlistRepositoryStub },
                { provide: getRepositoryToken(Product), useValue: mockProductRepo },
            ],
        }).compile();

        service = module.get<WishlistService>(WishlistService);
        repo = module.get<IWishlistRepositoryInterface>('IWishlistRepository');
    });

    afterEach(() => {
        jest.clearAllMocks();
    });

    it('should be defined', () => {
        expect(service).toBeDefined();
    });


    describe('getAll', () => {
        it('should return wishlist items belonging to the given user', async () => {
            const result = await service.getAll(1);

            expect(result).toHaveLength(1);
            expect(result[0].user_id).toBe(1);
        });

        it('should return an empty array when user has no wishlist items', async () => {
            const result = await service.getAll(999);
            expect(result).toEqual([]);
        });
    });


    describe('add', () => {
        it('should throw NotFoundException if product does not exist', async () => {
            mockProductRepo.findOneBy.mockResolvedValueOnce(null);
            await expect(service.add(1, 999)).rejects.toThrow(NotFoundException);
        });

        it('should add and return a new wishlist item when product exists', async () => {
            const product = { id: 200, name: 'New Product', qty: 3, price: 75, description: 'New' } as Product;
            mockProductRepo.findOneBy.mockResolvedValueOnce(product);

            const result = await service.add(1, 200);

            expect(result.user_id).toBe(1);
            expect(result.product.id).toBe(200);
        });
    });


    describe('remove', () => {
        it('should remove a wishlist item and return affected count', async () => {
            const result = await service.remove(1, 101);
            expect(result.affected).toBe(1);
        });

        it('should return 0 affected when item does not exist', async () => {
            const result = await service.remove(1, 999);
            expect(result.affected).toBe(0);
        });
    });
});
