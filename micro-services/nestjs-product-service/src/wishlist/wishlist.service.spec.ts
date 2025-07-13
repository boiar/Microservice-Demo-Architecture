import { Test, TestingModule } from '@nestjs/testing';
import { WishlistService } from './wishlist.service';
import { IWishlistRepositoryInterface } from './interfaces/wishlist-repository.interface';
import { getRepositoryToken } from '@nestjs/typeorm';
import { Product } from '../products/product.entity';
import { Repository } from 'typeorm';
import { NotFoundException } from '@nestjs/common';

describe('WishlistService', () => {
    let service: WishlistService;
    let repo: jest.Mocked<IWishlistRepositoryInterface>;
    let productRepo: jest.Mocked<Repository<Product>>;

    const mockWishlistRepo = {
        find: jest.fn(),
        create: jest.fn(),
        save: jest.fn(),
        delete: jest.fn(),
    };

    const mockProductRepo = {
        findOneBy: jest.fn(),
    };

    beforeEach(async () => {
        const module: TestingModule = await Test.createTestingModule({
            providers: [
                WishlistService,
                { provide: IWishlistRepositoryInterface, useValue: mockWishlistRepo },
                { provide: getRepositoryToken(Product), useValue: mockProductRepo },
            ],
        }).compile();

        service = module.get<WishlistService>(WishlistService);
        repo = module.get(IWishlistRepositoryInterface);
        productRepo = module.get(getRepositoryToken(Product));
    });

    it('should be defined', () => {
        expect(service).toBeDefined();
    });

    describe('getAll', () => {
        it('should return wishlist for user', async () => {
            const mockData = [{ product: { name: 'Mock Product' } }];
            repo.find.mockResolvedValue(mockData as any);
            const result = await service.getAll(1);
            expect(result).toEqual(mockData);
        });
    });

    describe('add', () => {
        it('should throw NotFoundException if product not found', async () => {
            productRepo.findOneBy.mockResolvedValue(null);
            await expect(service.add(1, 100)).rejects.toThrow(NotFoundException);
        });

        it('should add a wishlist item if product exists', async () => {
            const product = { id: 100, name: 'Prod' };
            const created = { user_id: 1, product };
            productRepo.findOneBy.mockResolvedValue(product as any);
            repo.create.mockReturnValue(created as any);
            repo.save.mockResolvedValue(created as any);

            const result = await service.add(1, 100);
            expect(result).toEqual(created);
        });
    });

    describe('remove', () => {
        it('should call delete on repository', async () => {
            const mockDelete = { affected: 1 };
            repo.delete.mockResolvedValue(mockDelete);
            const result = await service.remove(1, 101);
            expect(result).toEqual(mockDelete);
        });
    });
});
