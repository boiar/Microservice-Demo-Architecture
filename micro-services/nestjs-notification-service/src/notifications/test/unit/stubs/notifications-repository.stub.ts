import { INotificationsRepository } from '../../../repository/notifications-repository.interface';
import { Notification } from '../../../entity/notification.entity';

export class NotificationsRepositoryStub implements INotificationsRepository {
    private notifications: Partial<Notification>[] = [];

    async saveNotification(notification: Partial<Notification>): Promise<void> {
        this.notifications.push({
            ...notification,
            id: `${this.notifications.length + 1}`,
            status: notification.status ?? 'sent',
            timestamp: notification.timestamp ?? new Date(),
        });
        // Simulate async behavior
        return Promise.resolve();
    }

    getSavedNotifications(): Partial<Notification>[] {
        return this.notifications;
    }

    clear(): void {
        this.notifications = [];
    }
}