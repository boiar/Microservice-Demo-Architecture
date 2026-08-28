import { Notification } from '../entity/notification.entity';

export interface INotificationsRepository {
    saveNotification(notification: Partial<Notification>): Promise<void>;
}