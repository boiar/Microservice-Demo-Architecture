import {Injectable, Logger, OnModuleDestroy, OnModuleInit} from '@nestjs/common';
import Redis from "ioredis";
import {InjectModel} from "@nestjs/mongoose";
import {Model} from "mongoose";
import {Notification} from "./schemas/notification.schema";
import {ConfigService} from "@nestjs/config";

@Injectable()
export class NotificationsService implements OnModuleInit, OnModuleDestroy{
    private readonly logger = new Logger(NotificationsService.name);
    private redisClient: Redis;
    private subscriberClient: Redis;

    constructor(
        @InjectModel(Notification.name)
        private notificationModel: Model<Notification>,
        private configService: ConfigService,
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


    // open-close principle
    private readonly eventHandlers: Record<string, (data: any) => Promise<void>> = {
        'user.registered': this.handleUserRegisteredEvent.bind(this),
        'user.updated': this.handleUserUpdatedEvent.bind(this),
        'order.created': this.handleOrderCreatedEvent.bind(this),
        'product.updated': this.handleProductUpdatedEvent.bind(this),
    };



    async onModuleInit() {
        this.logger.log('Connecting to Redis and subscribing to channel...');

        this.subscriberClient.on('connect', () => this.logger.log('Subscriber connected to Redis!'));
        this.subscriberClient.on('error', (err) => this.logger.error('Subscriber Redis Error:', err));

        const channels = ['user-events', 'product-events', 'order-events'];

        for (const channel of channels) {
            await this.subscriberClient.subscribe(channel, (err, count) => {
                if (err) {
                    this.logger.error(`Failed to subscribe to ${channel}: ${err.message}`);
                } else {
                    this.logger.log(`Subscribed to channel: ${channel}`);
                }
            });
        }

        this.subscriberClient.on('message', (channel, message) => {
            this.logger.log(`Received message from ${channel}: ${message}`);
            try {
                const eventData = JSON.parse(message);
                const handler = this.eventHandlers[eventData.event];

                if (handler) {
                    handler(eventData);
                } else {
                    this.logger.warn(`No handler defined for event: ${eventData.event}`);
                }
            } catch (err) {
                this.logger.error('Invalid message format or handler error', err);
            }
        });


    }


    async onModuleDestroy() {
        this.logger.log('Unsubscribing and closing Redis connections...');
        await this.subscriberClient.unsubscribe('user.registered');
        await this.subscriberClient.unsubscribe('product-events');
        await this.subscriberClient.quit();
        await this.redisClient.quit();
    }

    private async handleUserRegisteredEvent(eventData: any) {
        try {
            this.logger.log('Processing user registered event:', eventData);

            const user = eventData.data;

            this.logger.log(`Simulating sending welcome email to: ${user.email}`);

            const newNotification = new this.notificationModel({
                userId: user.id ? user.id.toString() : 'unknown',
                userEmail: user.email,
                type: 'welcome_email',
                message: `Welcome to our service, ${user.name || 'user'}!`,
                status: 'sent',
                timestamp: new Date(eventData.timestamp),
            });

            await newNotification.save();

        } catch (error) {
            this.logger.error('Error processing user registered event:', error.message, error.stack);
        }
    }



    private async handleUserUpdatedEvent(eventData: any) {
        this.logger.log(`User updated profile: ${eventData.data.email}`);

        const newNotification = new this.notificationModel({
            userId: eventData.data.id?.toString() ?? 'unknown',
            userEmail: eventData.data.email,
            type: 'profile_updated',
            message: `${eventData.data.name} has updated their profile.`,
            status: 'sent',
            timestamp: new Date(eventData.timestamp),
        });

        await newNotification.save();
    }


    private async handleOrderCreatedEvent(eventData: any) {
        try {
            this.logger.log('Processing order.created event:', eventData);

            const orderId = eventData.order_id;
            const userId = eventData.user_id;

            const newNotification = new this.notificationModel({
                userId: userId.toString(),
                userEmail: eventData.user_email || 'unknown@example.com',
                type: 'order_created',
                message: `Your order #${orderId} has been placed successfully.`,
                status: 'sent',
                timestamp: new Date(),
            });

            await newNotification.save();
        } catch (error) {
            this.logger.error('Error processing order.created event:', error.message, error.stack);
        }
    }


    private async handleProductUpdatedEvent(eventData: any) {
        try {
            this.logger.log(`Product updated: ID ${eventData.product_id} | Qty changed decreased : ${eventData.qty_changed}`);

            const newNotification = new this.notificationModel({
                userId: eventData.user_id?.toString() || 'unknown',
                userEmail: eventData.user_email || 'unknown@example.com',
                type: 'product_updated',
                message: `Product #${eventData.product_id} stock updated.`,
                status: 'sent',
                timestamp: new Date(eventData.timestamp),
            });

            await newNotification.save();
        } catch (error) {
            this.logger.error('Error handling product.updated event:', error.message, error.stack);
        }
    }



}
