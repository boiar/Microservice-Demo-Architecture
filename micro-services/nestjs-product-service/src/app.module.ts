import { Module } from '@nestjs/common';
import { AppController } from './app.controller';
import { AppService } from './app.service';
import { CacheModule } from '@nestjs/cache-manager';
import {TypeOrmModule} from "@nestjs/typeorm";
import { ProductsModule } from './products/products.module';
import {Product} from "./products/product.entity";
import {ConfigModule} from "@nestjs/config";
import {WishlistModule} from "./wishlist/wishlist.module";
import {ProductsService} from "./products/products.service";

@Module({
  imports: [
    ConfigModule.forRoot({ isGlobal: true }),
    CacheModule.register({ isGlobal: true }),
    TypeOrmModule.forRoot({
      type: 'mysql',
      host: process.env.MYSQL_HOST || 'localhost',
      port: parseInt(process.env.MYSQL_PORT) || 3306,
      username: process.env.MYSQL_USER || 'root',
      password: process.env.MYSQL_PASSWORD || 'root',
      database: process.env.MYSQL_DATABASE || 'product_db',
      autoLoadEntities: true,
      synchronize: true,
    }),
    TypeOrmModule.forFeature([Product]),
    ProductsModule,
    WishlistModule,
  ],
  controllers: [AppController],
  providers: [
      AppService,
      ProductsService,
  ],
})
export class AppModule {}