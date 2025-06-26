// microservices-registration-project/services/nestjs-notification-service/src/notifications/notifications.module.ts
import { Module } from '@nestjs/common';
import { NotificationsService } from './notifications.service';
import { MongooseModule } from '@nestjs/mongoose';
import { Notification, NotificationSchema } from './schemas/notification.schema';
import { ConfigModule } from '@nestjs/config'; // Make sure this is imported

@Module({
  imports: [
    ConfigModule, // Import ConfigModule here if not global in app.module
    MongooseModule.forFeature([{ name: Notification.name, schema: NotificationSchema }]),
  ],
  providers: [NotificationsService],
  exports: [NotificationsService]
})
export class NotificationsModule {}