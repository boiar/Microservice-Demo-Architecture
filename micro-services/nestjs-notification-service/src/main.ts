import { NestFactory } from '@nestjs/core';
import { AppModule } from './app.module';
import mongoose from "mongoose";
import { Transport } from '@nestjs/microservices';

async function bootstrap() {
  const app = await NestFactory.create(AppModule);
  const port = process.env.PORT || 3000;

  mongoose.connection.on('connected', () => {
    console.log('MongoDB connected');
  });

  mongoose.connection.on('error', (err) => {
    console.error('MongoDB connection error:', err);
  });

  app.connectMicroservice({
    transport: Transport.RMQ,
    options: {
      urls: [process.env.RABBITMQ_URL || 'amqp://admin:admin@rabbitmq:5672'],
      queue: 'example_queue',
      noAck: false,
      queueOptions: {
        durable: true
      },
    },
  });

  await app.startAllMicroservices();
  await app.listen(port, '0.0.0.0'); // LISTEN ON 0.0.0.0 to be accessible from other containers
  console.log(`Notification Service running on: http://localhost:${port}`);
}
bootstrap();
