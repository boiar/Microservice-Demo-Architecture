export interface INotificationsService {
    onModuleInit(): Promise<void>;
    onModuleDestroy(): Promise<void>;
}