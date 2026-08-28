import { ProductsService } from '../../service/impl/products.service';
import { CACHE_MANAGER } from '@nestjs/cache-manager';
import { Product } from '../../entity/product.entity';
import { Cache } from 'cache-manager';
import { Test, TestingModule } from '@nestjs/testing';
import { TypeOrmModule } from "@nestjs/typeorm";
import { ProductsRepositoryStub } from "./stubs/products-repository.stub";
import { ProductRepositoryInterface } from "../../repository/product-repository.interface";
import { StockUpdatedEvent } from "../../dto/requests/stock-updated.event";
import { StockUpdatedEventType } from "../../enum/stock-updated-event-type.enum";
import { InboxInterface } from '../../service/inbox.interface';

describe('ProductsService', () => {
    let service: ProductsService;
    let repo: ProductRepositoryInterface;
    let cache: Cache;
    let inboxService: InboxInterface;

    const mockCache = {
        get: jest.fn(),
        set: jest.fn(),
        del: jest.fn(),
    };

    const mockRmqService = {
        emit: jest.fn(),
    };

    const mockInboxService = {
        isProcessed: jest.fn(),
        save: jest.fn(),
    };

    beforeEach(async () => {
        const module: TestingModule = await Test.createTestingModule({
            imports: [],
            providers: [
                ProductsService,
                { provide: 'ProductRepository', useClass: ProductsRepositoryStub },
                { provide: CACHE_MANAGER, useValue: mockCache },
                { provide: 'RMQ_SERVICE', useValue: mockRmqService },
                { provide: 'InboxService', useValue: mockInboxService },
            ],
        }).compile();

        service = module.get<ProductsService>(ProductsService);
        repo = module.get<ProductRepositoryInterface>('ProductRepository');
        cache = module.get<Cache>(CACHE_MANAGER);
        inboxService = module.get<InboxInterface>('InboxService');
    });

    afterEach(() => {
        jest.clearAllMocks();
    });

    describe('findAll', () => {
        it('should return products from cache if available', async () => {
            const cachedProducts = [
                { id: 99, name: 'Cached Product', availableQty: 10, price: 100, description: 'From Cache' }
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
            expect(result.length).toBeGreaterThan(0);
            expect(mockCache.set).toHaveBeenCalledWith('products:all', result, 3600);
        });
    });

    describe('findOne', () => {
        it('should return one product', async () => {
            const product = await repo.findOneBy({});
            expect(product).toBeDefined();

            const result = await service.findOne(product!.id);

            expect(result).toEqual(expect.objectContaining({
                id: product!.id,
                name: product!.name
            }));
        });
    });

    describe('handleStockUpdatedEvent', () => {
        it('should skip processing if event is already processed in inbox', async () => {
            mockInboxService.isProcessed.mockResolvedValueOnce(true);

            const event: StockUpdatedEvent = {
                eventId: 'event-123',
                eventType: StockUpdatedEventType.PRODUCT_OUT_OF_STOCK,
                productId: 1,
                availableQty: 5
            };

            await service.handleStockUpdatedEvent(event);

            expect(mockInboxService.isProcessed).toHaveBeenCalledWith('event-123');
            expect(mockCache.del).not.toHaveBeenCalled();
            expect(mockInboxService.save).not.toHaveBeenCalled();
        });

        it('should update product availableQty, save to inbox, and clear cache', async () => {
            const product = await repo.findOneBy({});
            
            mockInboxService.isProcessed.mockResolvedValueOnce(false);
            mockCache.del.mockResolvedValue(undefined);

            const event: StockUpdatedEvent = {
                eventId: 'event-456',
                eventType: StockUpdatedEventType.PRODUCT_OUT_OF_STOCK,
                productId: product!.id,
                availableQty: 5
            };

            await service.handleStockUpdatedEvent(event);

            expect(mockInboxService.isProcessed).toHaveBeenCalledWith('event-456');

            const updated = await repo.findOneBy({ id: product!.id });
            expect(updated!.availableQty).toBe(5);
            
            expect(mockInboxService.save).toHaveBeenCalledWith('event-456', event.eventType, event);
            
            expect(mockCache.del).toHaveBeenCalledWith('products:all');
            expect(mockCache.del).toHaveBeenCalledWith(`product:${product!.id}`);
        });

        it('should do nothing if product is not found', async () => {
            mockInboxService.isProcessed.mockResolvedValueOnce(false);
            mockCache.del.mockResolvedValue(undefined);

            const event: StockUpdatedEvent = {
                eventId: 'event-789',
                eventType: StockUpdatedEventType.PRODUCT_OUT_OF_STOCK,
                productId: 9999,
                availableQty: 5
            };

            await service.handleStockUpdatedEvent(event);

            expect(mockCache.del).not.toHaveBeenCalled();
            expect(mockInboxService.save).not.toHaveBeenCalled();
        });
    });
});