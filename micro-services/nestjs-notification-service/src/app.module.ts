import { Module } from '@nestjs/common';
import { AppController } from './app.controller';
import { AppService } from './app.service';
import { NotificationsModule } from './notifications/notifications.module';
import { ConfigModule, ConfigService } from '@nestjs/config';
import { MongooseModule, MongooseModuleOptions } from '@nestjs/mongoose';

@Module({
    imports: [
        ConfigModule.forRoot({ isGlobal: true, envFilePath: '.env' }),

        MongooseModule.forRootAsync({
            imports: [ConfigModule],
            inject: [ConfigService],
            useFactory: async (config: ConfigService): Promise<MongooseModuleOptions> => {
                const uri = config.get<string>('MONGO_DB_URI');
                console.log('📌 Mongo URI:', uri); // Add this line
                return { uri };
            }
        }),


        NotificationsModule,
    ],
    controllers: [AppController],
    providers: [AppService],
})
export class AppModule {}
