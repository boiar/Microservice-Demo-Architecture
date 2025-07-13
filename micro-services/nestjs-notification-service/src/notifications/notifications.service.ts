import {
    Inject,
    Injectable,
    Logger,
    OnModuleDestroy,
    OnModuleInit,
} from '@nestjs/common';
import Redis from 'ioredis';
import { ConfigService } from '@nestjs/config';
import { INotificationsRepository } from './interfaces/notifications-repository.interface';
import { INotificationsService } from './interfaces/notifications-service.interface';

@Injectable()
export class NotificationsService
    implements OnModuleInit, OnModuleDestroy, INotificationsService
{
    private readonly logger = new Logger(NotificationsService.name);
    private redisClient: Redis;
    private subscriberClient: Redis;

    constructor(
        private readonly configService: ConfigService,
        @Inject('INotificationsRepository')
        private readonly repo: INotificationsRepository
    ) {
        this.redisClient = new Redis({
            host: this.configService.get<string>('REDIS_HOST'),
            port: this.configService.get<number>('REDIS_PORT'),
            password: this.configService.get<string>('REDIS_PASSWORD'),
            db: this.configService.get<number>('REDIS_DB') || 0,
        });

        this.subscriberClient = new Redis({
            host: this.configService.get<string>('REDIS_HOST'),
            port: this.configService.get<number>('REDIS_PORT'),
            password: this.configService.get<string>('REDIS_PASSWORD'),
            db: this.configService.get<number>('REDIS_DB') || 0,
        });
    }

    private readonly eventHandlers: Record<string, (data: any) => Promise<void>> =
        {
            'user.registered': this.handleUserRegisteredEvent.bind(this),
            'user.updated': this.handleUserUpdatedEvent.bind(this),
            'order.created': this.handleOrderCreatedEvent.bind(this),
            'product.updated': this.handleProductUpdatedEvent.bind(this),
        };

    async onModuleInit() {
        this.logger.log('Connecting to Redis and subscribing to channel...');

        const channels = ['user-events', 'product-events', 'order-events'];
        for (const channel of channels) {
            await this.subscriberClient.subscribe(channel);
            this.logger.log(`Subscribed to channel: ${channel}`);
        }

        this.subscriberClient.on('message', async (channel, message) => {
            try {
                const eventData = JSON.parse(message);
                const handler = this.eventHandlers[eventData.event];
                if (handler) {
                    await handler(eventData);
                } else {
                    this.logger.warn(`No handler defined for event: ${eventData.event}`);
                }
            } catch (err) {
                this.logger.error('Invalid message or handler error', err);
            }
        });
    }

    async onModuleDestroy() {
        this.logger.log('Unsubscribing and closing Redis connections...');
        await this.subscriberClient.unsubscribe();
        await this.subscriberClient.quit();
        await this.redisClient.quit();
    }

    private async handleUserRegisteredEvent(eventData: any) {
        const user = eventData.data;
        await this.repo.saveNotification({
            userId: user.id?.toString() || 'unknown',
            userEmail: user.email,
            type: 'welcome_email',
            message: `Welcome to our service, ${user.name || 'user'}!`,
            status: 'sent',
            timestamp: new Date(eventData.timestamp),
        });
    }

    private async handleUserUpdatedEvent(eventData: any) {
        const user = eventData.data;
        await this.repo.saveNotification({
            userId: user.id?.toString() || 'unknown',
            userEmail: user.email,
            type: 'profile_updated',
            message: `${user.name} has updated their profile.`,
            status: 'sent',
            timestamp: new Date(eventData.timestamp),
        });
    }

    private async handleOrderCreatedEvent(eventData: any) {
        await this.repo.saveNotification({
            userId: eventData.user_id?.toString() || 'unknown',
            userEmail: eventData.user_email || 'unknown@example.com',
            type: 'order_created',
            message: `Your order #${eventData.order_id} has been placed successfully.`,
            status: 'sent',
            timestamp: new Date(),
        });
    }

    private async handleProductUpdatedEvent(eventData: any) {
        await this.repo.saveNotification({
            userId: eventData.user_id?.toString() || 'unknown',
            userEmail: eventData.user_email || 'unknown@example.com',
            type: 'product_updated',
            message: `Product #${eventData.product_id} stock updated.`,
            status: 'sent',
            timestamp: new Date(eventData.timestamp),
        });
    }
}
