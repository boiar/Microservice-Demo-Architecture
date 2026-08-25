import { Module } from '@nestjs/common';
import { ProductsService } from './products.service';
import { ProductsController } from './products.controller';
import {TypeOrmModule} from "@nestjs/typeorm";
import {Product} from "./product.entity";
import {AuthModule} from "../auth/auth.module";

import { ClientsModule, Transport } from '@nestjs/microservices';

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
    TypeOrmModule.forFeature([Product]),
    AuthModule,
  ],
  controllers: [ProductsController],
  providers: [ProductsService],
  exports: ['ProductRepository'],

})
export class ProductsModule {}