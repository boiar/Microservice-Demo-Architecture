import { Notification } from '../schemas/notification.schema';

export interface INotificationsRepository {
    saveNotification(notification: Partial<Notification>): Promise<void>;
}