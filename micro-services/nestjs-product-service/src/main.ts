import { NestFactory } from '@nestjs/core';
import { AppModule } from './app.module';
import { Transport, MicroserviceOptions } from '@nestjs/microservices';
import { join } from 'path';

async function bootstrap() {
  const app = await NestFactory.create(AppModule);
  
  app.connectMicroservice<MicroserviceOptions>({
    transport: Transport.RMQ,
    options: {
      urls: [process.env.RABBITMQ_URL || 'amqp://admin:admin@rabbitmq:5672'],
      queue: 'example_queue',
      queueOptions: {
        durable: true,
      },
      // Bind to the exchange that the Laravel order service publishes to
      exchange: 'example_exchange',
      exchangeType: 'direct',
      routingKey: 'example_routing_key',
      noAck: false,
    },
  });

  // gRPC server
  app.connectMicroservice<MicroserviceOptions>({
    transport: Transport.GRPC,
    options: {
      package: 'products',
      protoPath: join(__dirname, '../src/products/proto/product.proto'),
      url: `0.0.0.0:${process.env.GRPC_PORT || 50051}`,
    },
  });

  await app.startAllMicroservices();
  await app.listen(process.env.PORT || 3000, '0.0.0.0');
}
bootstrap();
