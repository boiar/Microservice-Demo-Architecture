import { Module } from '@nestjs/common';
import { NotificationsService } from './notifications.service';
import { MongooseModule } from '@nestjs/mongoose';
import { Notification, NotificationSchema } from './schemas/notification.schema';
import { ConfigModule } from '@nestjs/config';
import {NotificationsRepository} from "./notifications.repository";

@Module({
  imports: [
    ConfigModule,
    MongooseModule.forFeature([{ name: Notification.name, schema: NotificationSchema }]),
  ],
  providers: [
    NotificationsService,
    {
      provide: 'INotificationsRepository',
      useClass: NotificationsRepository,
    },
  ],
  exports: [NotificationsService],
})
export class NotificationsModule {}