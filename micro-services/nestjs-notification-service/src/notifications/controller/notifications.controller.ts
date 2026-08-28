import { Controller, Logger } from '@nestjs/common';
import {
    Ctx,
    EventPattern,
    Payload,
    RmqContext,
} from '@nestjs/microservices';

import { NotificationsService } from '../service/impl/notifications.service';

import { UserRegisteredEventDto } from '../dto/request/user-registered-event.dto';
import { ProductUpdatedEventDto } from '../dto/request/product-updated-event.dto';
import { OrderCreatedEventDto } from '../dto/request/order-created-event.dto';

@Controller()
export class NotificationsController {
    private readonly logger = new Logger(NotificationsController.name);

    constructor(
      private readonly notificationsService: NotificationsService,
    ) {}

    @EventPattern('user.registered')
    async handleUserRegistered(
      @Payload() data: UserRegisteredEventDto,
      @Ctx() context: RmqContext,
    ): Promise<void> {
        const channel = context.getChannelRef();
        const originalMsg = context.getMessage();

        try {
            await this.notificationsService.handleUserRegisteredEvent(data);

            channel.ack(originalMsg);

            this.logger.log(
              `Successfully processed user.registered`,
            );
        } catch (error: unknown) {
            const message =
              error instanceof Error
                ? error.message
                : 'Unknown error';

            this.logger.error(
              `Error processing user.registered: ${message}`,
            );

            channel.nack(originalMsg, false, false);
        }
    }

    @EventPattern('product.updated')
    async handleProductUpdated(
      @Payload() data: ProductUpdatedEventDto,
      @Ctx() context: RmqContext,
    ): Promise<void> {
        const channel = context.getChannelRef();
        const originalMsg = context.getMessage();

        try {
            await this.notificationsService.handleProductUpdatedEvent(data);

            channel.ack(originalMsg);

            this.logger.log(
              `Successfully processed product.updated`,
            );
        } catch (error: unknown) {
            const message =
              error instanceof Error
                ? error.message
                : 'Unknown error';

            this.logger.error(
              `Error processing product.updated: ${message}`,
            );

            channel.nack(originalMsg, false, false);
        }
    }

    @EventPattern('order.created')
    async handleOrderCreated(
      @Payload() data: OrderCreatedEventDto,
      @Ctx() context: RmqContext,
    ): Promise<void> {
        const channel = context.getChannelRef();
        const originalMsg = context.getMessage();

        try {
            await this.notificationsService.handleOrderCreatedEvent(data);

            channel.ack(originalMsg);

            this.logger.log(
              `Successfully processed order.created`,
            );
        } catch (error: unknown) {
            const message =
              error instanceof Error
                ? error.message
                : 'Unknown error';

            this.logger.error(
              `Error processing order.created: ${message}`,
            );

            channel.nack(originalMsg, false, false);
        }
    }
}