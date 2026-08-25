import { NestFactory } from '@nestjs/core';
import { AppModule } from './app.module';
import { Transport } from '@nestjs/microservices';

async function bootstrap() {
  const app = await NestFactory.create(AppModule);
  
  app.connectMicroservice({
    transport: Transport.RMQ,
    options: {
      urls: [process.env.RABBITMQ_URL || 'amqp://admin:admin@rabbitmq:5672'],
      queue: 'example_queue',
      queueOptions: {
        durable: true
      },
    },
  });

  await app.startAllMicroservices();
  await app.listen(process.env.PORT || 3000, '0.0.0.0');
}
bootstrap();
