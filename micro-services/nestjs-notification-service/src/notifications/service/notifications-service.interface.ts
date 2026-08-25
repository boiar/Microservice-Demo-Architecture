import { UserRegisteredEventDto } from '../dto/request/user-registered-event.dto';
import { UserUpdatedEventDto } from '../dto/request/user-updated-event.dto';
import { OrderCreatedEventDto } from '../dto/request/order-created-event.dto';
import { ProductUpdatedEventDto } from '../dto/request/product-updated-event.dto';

export interface INotificationsService {
    handleUserRegisteredEvent(
      event: UserRegisteredEventDto,
    ): Promise<void>;

    handleUserUpdatedEvent(
      event: UserUpdatedEventDto,
    ): Promise<void>;

    handleOrderCreatedEvent(
      event: OrderCreatedEventDto,
    ): Promise<void>;

    handleProductUpdatedEvent(
      event: ProductUpdatedEventDto,
    ): Promise<void>;
}