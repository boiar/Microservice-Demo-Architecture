import {
    Inject,
    Injectable,
    Logger,
    OnModuleDestroy,
    OnModuleInit,
} from '@nestjs/common';

import { INotificationsRepository } from '../../repository/notifications-repository.interface';
import { INotificationsService } from '../notifications-service.interface';
import { UserRegisteredEventDto } from '../../dto/request/user-registered-event.dto';
import { UserUpdatedEventDto } from '../../dto/request/user-updated-event.dto';
import { OrderCreatedEventDto } from '../../dto/request/order-created-event.dto';
import { ProductUpdatedEventDto } from '../../dto/request/product-updated-event.dto';

@Injectable()
export class NotificationsService
  implements INotificationsService, OnModuleInit, OnModuleDestroy
{
    private readonly logger = new Logger(NotificationsService.name);

    constructor(
      @Inject('INotificationsRepository')
      private readonly repo: INotificationsRepository,
    ) {}

    async onModuleInit(): Promise<void> {
        this.logger.log('NotificationsService initialized');
    }

    async onModuleDestroy(): Promise<void> {
        this.logger.log('NotificationsService destroyed');
    }

    async handleUserRegisteredEvent(event:UserRegisteredEventDto): Promise<void> {
        const user = event.data;

        await this.repo.saveNotification({
            userId: user.id.toString() || 'unknown',
            userEmail: user.email,
            type: 'welcome_email',
            message: `Welcome to our service, ${user.name || 'user'}!`,
            status: 'sent',
            timestamp: new Date(event.timestamp || Date.now()),
        });
    }

    async handleUserUpdatedEvent(event:UserUpdatedEventDto): Promise<void> {
        const user = event.data;

        await this.repo.saveNotification({
            userId: user.id.toString() || 'unknown',
            userEmail: user.email,
            type: 'profile_updated',
            message: `${user.name} has updated their profile.`,
            status: 'sent',
            timestamp: new Date(event.timestamp || Date.now()),
        });
    }

    async handleOrderCreatedEvent(event: OrderCreatedEventDto): Promise<void> {
        await this.repo.saveNotification({
            userId: event.user_id.toString() || 'unknown',
            userEmail: event.user_email || 'unknown@example.com',
            type: 'order_created',
            message: `Your order #${event.order_id} has been placed successfully.`,
            status: 'sent',
            timestamp: new Date(),
        });
    }

    async handleProductUpdatedEvent(event: ProductUpdatedEventDto): Promise<void> {
        await this.repo.saveNotification({
            userId: event.user_id.toString() || 'unknown',
            userEmail: event.user_email || 'unknown@example.com',
            type: 'product_updated',
            message: `Product #${event.product_id} stock updated.`,
            status: 'sent',
            timestamp: new Date(event.timestamp || Date.now()),
        });
    }
}