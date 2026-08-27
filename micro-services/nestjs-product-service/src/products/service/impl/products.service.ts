import { Inject, Injectable, Logger } from '@nestjs/common';
import { ClientProxy } from '@nestjs/microservices';
import { CACHE_MANAGER } from '@nestjs/cache-manager';
import type { Cache } from 'cache-manager';
import { Product } from '../../entity/product.entity';
import { IProductInterface } from "../product.interface";
import { ProductRepositoryInterface } from "../../repository/product-repository.interface";
import { StockUpdatedEvent } from 'src/products/dto/requests/stock-updated.event';
import { InboxInterface } from '../inbox.interface';


@Injectable()
export class ProductsService implements IProductInterface {

    private readonly logger = new Logger(ProductsService.name);

    constructor(
        @Inject('ProductRepository')
        private readonly productRepository: ProductRepositoryInterface,
        
        @Inject('InboxService')
        private readonly inboxService: InboxInterface,

        @Inject(CACHE_MANAGER)
        private cacheManager: Cache,

        @Inject('RMQ_SERVICE')
        private readonly rmqService: ClientProxy
    ) {
    }


    async findAll(): Promise<Product[]> {
        const cacheKey = 'products:all';
        const cached = await this.cacheManager.get<Product[]>(cacheKey);
        if (cached) {
            return cached;
        }

        const products = await this.productRepository.findAll();
        await this.cacheManager.set(
            cacheKey,
            products,
            3600
        ); // Cache for 1 hour
        return products;
    }

    async findOne(id: number): Promise<Product> {
        return this.productRepository.findOneBy({ id });
    }

    async handleStockUpdatedEvent(event: StockUpdatedEvent): Promise<void> {
        
        // Inbox Pattern: Idempotency check
        const isProcessed = await this.inboxService.isProcessed(event.eventId);
        if (isProcessed) {
            this.logger.log(`Event ${event.eventId} already processed, skipping`);
            return;
        }

        this.logger.log(
            `Stock updated: product=${event.productId}, availableQty=${event.availableQty}`,
        );

        const product = await this.productRepository.findById(event.productId);

        if (!product) {
            this.logger.warn(
                `Product ${event.productId} not found while processing stock event`,
            );

            return;
        }

        await this.productRepository.update(event.productId, { availableQty: event.availableQty });

        // Save event to inbox to prevent duplicate processing
        await this.inboxService.save(event.eventId, event.eventType, event);

        // remove products cache 
        await this.cacheManager.del('products:all');
        await this.cacheManager.del(
            `product:${event.productId}`,
        );

    }

}
