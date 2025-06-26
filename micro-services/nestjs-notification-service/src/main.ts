import { NestFactory } from '@nestjs/core';
import { AppModule } from './app.module';
import mongoose from "mongoose";

async function bootstrap() {
  const app = await NestFactory.create(AppModule);
  const port = process.env.PORT || 3000;

  mongoose.connection.on('connected', () => {
    console.log('✅ MongoDB connected');
  });

  mongoose.connection.on('error', (err) => {
    console.error('❌ MongoDB connection error:', err);
  });



  await app.listen(port, '0.0.0.0'); // LISTEN ON 0.0.0.0 to be accessible from other containers
  console.log(`Notification Service running on: http://localhost:${port}`);



}
bootstrap();
