import { NestFactory } from '@nestjs/core';
import { AppModule } from './app.module';

async function bootstrap() {
  const app = await NestFactory.create(AppModule);
  const port = process.env.PORT || 3000;
  await app.listen(port, '0.0.0.0'); // LISTEN ON 0.0.0.0 to be accessible from other containers
  console.log(`Notification Service running on: http://localhost:${port}`);
}
bootstrap();
