import { Test, TestingModule } from '@nestjs/testing';
import { NotificationsService } from '../../notifications.service';
import { ConfigService } from '@nestjs/config';
import { NotificationsRepositoryStub } from './stubs/notifications-repository.stub';

describe('NotificationsService', () => {
    let service: NotificationsService;
    let repoStub: NotificationsRepositoryStub;

    beforeEach(async () => {
        repoStub = new NotificationsRepositoryStub();

        const module: TestingModule = await Test.createTestingModule({
            providers: [
                NotificationsService,
                {
                    provide: 'INotificationsRepository',
                    useValue: repoStub,
                },
                {
                    provide: ConfigService,
                    useValue: {
                        get: jest.fn().mockImplementation((key: string) => {
                            const mockConfig = {
                                REDIS_HOST: 'localhost',
                                REDIS_PORT: 6379,
                                REDIS_PASSWORD: '',
                                REDIS_DB: 0,
                            };
                            return mockConfig[key];
                        }),
                    },
                },
            ],
        }).compile();

        service = module.get<NotificationsService>(NotificationsService);
    });

    afterEach(async() => {
        repoStub.clear();
        await service['subscriberClient'].quit();
        await service['redisClient'].quit();
    });

    it('should be defined', () => {
        expect(service).toBeDefined();
    });

    it('should handle user registered event', async () => {
        const event = {
            event: 'user.registered',
            timestamp: Date.now(),
            data: {
                id: 'user-1',
                email: 'test@example.com',
                name: 'Test User',
            },
        };

        await service['handleUserRegisteredEvent'](event);

        const saved = repoStub.getSavedNotifications();
        expect(saved).toHaveLength(1);
        expect(saved[0].userEmail).toBe('test@example.com');
        expect(saved[0].type).toBe('welcome_email');
    });
});