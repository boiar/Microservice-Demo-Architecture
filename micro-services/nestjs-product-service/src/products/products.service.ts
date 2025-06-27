import {Inject, Injectable, Logger, OnModuleDestroy, OnModuleInit} from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { CACHE_MANAGER } from '@nestjs/cache-manager';
import type { Cache } from 'cache-manager';
import { Product } from './product.entity';
import Redis from 'ioredis';
import {IProductInterface} from "./iproduct.interface";


@Injectable()
export class ProductsService implements OnModuleInit, OnModuleDestroy, IProductInterface {

    private readonly logger = new Logger(ProductsService.name);
    private readonly redisSubscriber: Redis;
    private readonly redisPublisher: Redis;


    constructor(
        @InjectRepository(Product)
        private productRepository: Repository<Product>,
        @Inject(CACHE_MANAGER) private cacheManager: Cache,
    ) {
        this.redisSubscriber = new Redis({
            host: process.env.REDIS_HOST || 'localhost',
            port: parseInt(process.env.REDIS_PORT || '6379', 10),
            password: process.env.REDIS_PASSWORD || undefined,
        });

        this.redisPublisher = new Redis({
            host: process.env.REDIS_HOST || 'localhost',
            port: parseInt(process.env.REDIS_PORT || '6379', 10),
            password: process.env.REDIS_PASSWORD || undefined,
        });

        this.redisSubscriber.on('error', (err) => {
            this.logger.error('Redis Subscriber Error:', err);
        });
    }



    async onModuleInit() {
        this.logger.log('🔁Subscribing to Redis channel: order-events');
        await this.redisSubscriber.subscribe('order-events');

        this.redisSubscriber.on('message', async (channel, message) => {
            if (channel === 'order-events') {
                try {
                    const payload = JSON.parse(message);
                    if (payload.event === 'order.created') {
                        this.logger.log(`Processing order.created event`);
                        const isAlreadyHandled = await this.cacheManager.get(`order:${payload.order_id}`);
                        if (isAlreadyHandled) {
                            this.logger.warn(`Duplicate event for order ${payload.order_id} ignored`);
                            return;
                        }
                        await this.cacheManager.set(`order:${payload.order_id}`, true, 300); // cache for 5 mins

                        await this.decreaseProductQty(payload.products, payload.order_id, payload.user_id, payload.user_email );

                    } else {
                        this.logger.warn(`No handler defined for event: ${payload.event}`);
                    }
                } catch (err) {
                    this.logger.error(`Failed to handle event: ${err.message}`);
                }
            }
        });
    }


    async onModuleDestroy(): Promise<void> {
        this.logger.log('🛑Unsubscribing Redis and closing connections...');
        await this.redisSubscriber.unsubscribe('order-events');
        await this.redisSubscriber.quit();
    }



    async findAll(): Promise<Product[]> {
        const cacheKey = 'products:all';
        const cached = await this.cacheManager.get<Product[]>(cacheKey);
        if (cached) return cached;

        const products = await this.productRepository.find();
        await this.cacheManager.set(cacheKey, products, 3600); // Cache for 1 hour
        return products;
    }

    // @ts-ignore
    async findOne(id: number): Promise<Product> {
        return this.productRepository.findOneBy({ id });
    }

    async decreaseProductQty(products: { product_id: number; qty: number }[],
                             orderId?: number,
                             userId?: number,
                             userEmail?: string): Promise<void> {
        for (const item of products) {
            this.logger.log(`Decreasing product #${item.product_id} by ${item.qty}`);
            await this.productRepository.decrement({ id: item.product_id }, 'qty', item.qty);

            // Publish product.updated event
            await this.redisPublisher.publish('product-events', JSON.stringify({
                event: 'product.updated',
                product_id: item.product_id,
                qty_changed: item.qty,
                order_id: orderId,
                user_id: userId,
                user_email: userEmail,
                timestamp: new Date().toISOString(),
            }));

        }

        // Optionally clear the cache
        await this.cacheManager.del('products:all');
    }


}
