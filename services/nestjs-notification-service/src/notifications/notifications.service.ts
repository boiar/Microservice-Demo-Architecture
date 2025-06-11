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

        // Dedicated subscriber client
        this.subscriberClient = new Redis({
            host: this.configService.get<string>('REDIS_HOST'),
            port: this.configService.get<number>('REDIS_PORT'),
            password: this.configService.get<string>('REDIS_PASSWORD'),
            db: this.configService.get<number>('REDIS_DB') || 0,
        });

    }
    async onModuleInit() {
        this.logger.log('Connecting to Redis and subscribing to channel...');

        this.subscriberClient.on('connect', () => this.logger.log('Subscriber connected to Redis!'));
        this.subscriberClient.on('error', (err) => this.logger.error('Subscriber Redis Error:', err));

        await this.subscriberClient.subscribe('user.registered', (err, count) => {
            if (err) {
                this.logger.error(`Failed to subscribe: ${err.message}`);
            } else {
                this.logger.log(`Subscribed to ${count} channel(s).`);
            }
        });

        this.subscriberClient.on('message', (channel, message) => {
            this.logger.log(`Received message from ${channel}: ${message}`);
            if (channel === 'user.registered') {
                this.handleUserRegisteredEvent(message);
            }
        });
    }


    async onModuleDestroy() {
        this.logger.log('Unsubscribing and closing Redis connections...');
        await this.subscriberClient.unsubscribe('user.registered');
        await this.subscriberClient.quit();
        await this.redisClient.quit();
    }

    private async handleUserRegisteredEvent(message: string) {
        try {
            const eventData = JSON.parse(message);
            this.logger.log('Processing user registered event:', eventData);

            // 2. Simulate sending welcome email (replace with actual email API call)
            this.logger.log(`Simulating sending welcome email to: ${eventData.user_email}`);
            // In a real app, you'd integrate with SendGrid, Mailgun, etc., here.

            // 3. Store Notification Log/History in MongoDB
            const newNotification = new this.notificationModel({
                userId: eventData.user_id ? eventData.user_id.toString() : 'unknown', // Handle potentially missing user_id
                userEmail: eventData.user_email,
                type: 'welcome_email',
                message: `Welcome to our service, ${eventData.user_name || 'user'}!`,
                status: 'sent', // Or 'failed' if email sending failed
                timestamp: new Date(eventData.timestamp),
            });
            await newNotification.save();
            this.logger.log('Notification log saved to MongoDB:', newNotification);

        } catch (error) {
            this.logger.error('Error processing user registered event:', error.message, error.stack);
        }
    }


}
