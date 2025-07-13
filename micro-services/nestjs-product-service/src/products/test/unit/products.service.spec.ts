import { ProductsService } from '../../products.service';
import { CACHE_MANAGER } from '@nestjs/cache-manager';
import { Product } from '../../product.entity';
import { Cache } from 'cache-manager';
import { Test, TestingModule } from '@nestjs/testing';
import {TypeOrmModule} from "@nestjs/typeorm";
import {ProductsRepositoryStub} from "./stubs/products-repository.stub";
import {IProductRepositoryInterface} from "../../interfaces/product-repository.interface";



describe('ProductsService', () => {
    let service: ProductsService;
    let repo: IProductRepositoryInterface;
    let cache: Cache;



    const mockCache = {
        get: jest.fn(),
        set: jest.fn(),
        del: jest.fn(),
    };

    beforeEach(async () => {
        const module: TestingModule = await Test.createTestingModule({
            imports: [],
            providers: [
                ProductsService,
                { provide: 'ProductRepository', useClass: ProductsRepositoryStub },
                { provide: CACHE_MANAGER, useValue: mockCache },
            ],
        }).compile();

        service = module.get<ProductsService>(ProductsService);
        repo = module.get<IProductRepositoryInterface>('ProductRepository');
        cache = module.get<Cache>(CACHE_MANAGER);

    });



    afterEach(() => {
        jest.clearAllMocks();
    });

    describe('findAll', () => {

        it('should return products from cache if available', async () => {
            const cachedProducts = [
                { id: 99, name: 'Cached Product', qty: 10, price: 100, description: 'From Cache' }
            ];
            mockCache.get.mockResolvedValueOnce(cachedProducts);

            const result = await service.findAll();

            expect(result).toEqual(cachedProducts);
            expect(mockCache.get).toHaveBeenCalledWith('products:all');
        });

        it('should fetch from repo and cache if not cached', async () => {
            mockCache.get.mockResolvedValueOnce(null);

            const result = await service.findAll();

            expect(Array.isArray(result)).toBe(true);
            expect(result.length).toBeGreaterThan(0); // نتوقع وجود بيانات بالفعل
            expect(mockCache.set).toHaveBeenCalledWith('products:all', result, 3600);
        });
    });

    describe('findOne', () => {
        it('should return one product', async () => {
            const product = await repo.findOneBy({}); // أي منتج موجود
            expect(product).toBeDefined();

            const result = await service.findOne(product!.id);

            expect(result).toEqual(expect.objectContaining({
                id: product!.id,
                name: product!.name
            }));
        });
    });

    describe('decreaseProductQty', () => {
        it('should decrease product qty and clear cache', async () => {
            const product = await repo.findOneBy({});
            const originalQty = product!.qty;

            mockCache.del.mockResolvedValue(undefined);

            await service.decreaseProductQty([{ product_id: product!.id, qty: 1 }]);

            const updated = await repo.findOneBy({ id: product!.id });
            expect(updated!.qty).toBe(originalQty - 1);
            expect(mockCache.del).toHaveBeenCalledWith('products:all');
        });
    });


})