import { Module } from '@nestjs/common';
import { NotificationsService } from './service/impl/notifications.service';
import { NotificationsController } from './controller/notifications.controller';
import { MongooseModule } from '@nestjs/mongoose';
import { Notification, NotificationEntity } from './entity/notification.entity';
import { ConfigModule } from '@nestjs/config';
import {NotificationsRepository} from "./repository/impl/notifications.repository";

@Module({
  imports: [
    ConfigModule,
    MongooseModule.forFeature([{ name: Notification.name, schema: NotificationEntity }]),
  ],
  controllers: [NotificationsController],
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