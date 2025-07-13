import { Injectable } from '@nestjs/common';
import { InjectModel } from '@nestjs/mongoose';
import { Notification } from './schemas/notification.schema';
import { Model } from 'mongoose';
import { INotificationsRepository } from './interfaces/notifications-repository.interface';

@Injectable()
export class NotificationsRepository implements INotificationsRepository {
    constructor(
        @InjectModel(Notification.name)
        private readonly notificationModel: Model<Notification>
    ) {}

    async saveNotification(notification: Partial<Notification>): Promise<void> {
        const doc = new this.notificationModel(notification);
        await doc.save();
    }
}