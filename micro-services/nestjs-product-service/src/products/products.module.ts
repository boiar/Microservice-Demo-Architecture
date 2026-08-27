import { Module } from '@nestjs/common';
import { ProductsService } from './service/impl/products.service';
import { ProductsController } from './products.controller';
import { TypeOrmModule } from "@nestjs/typeorm";
import { Product } from "./entity/product.entity";
import { AuthModule } from "../auth/auth.module";

import { ClientsModule, Transport } from '@nestjs/microservices';
import { ProductRepository } from './repository/impl/product.repository';
import { InboxEvent } from './entity/inbox-event.entity';
import { InboxService } from './service/impl/inbox.service';

@Module({
  imports: [
    ClientsModule.register([
      {
        name: 'RMQ_SERVICE',
        transport: Transport.RMQ,
        options: {
          urls: [process.env.RABBITMQ_URL || 'amqp://admin:admin@rabbitmq:5672'],
          queue: 'example_queue',
          queueOptions: {
            durable: true
          },
        },
      },
    ]),
    TypeOrmModule.forFeature([
      Product,
      InboxEvent
    ]),
    AuthModule,
  ],
  controllers: [ProductsController],
  providers: [
    {
      provide: 'ProductRepository',
      useClass: ProductRepository,
    },
    {
      provide: 'ProductService',
      useClass: ProductsService,
    },
    {
      provide: 'InboxService',
      useClass: InboxService,
    },
  ],
  exports: [
    'IProductRepository',
    'IProductService',
  ],

})
export class ProductsModule { }