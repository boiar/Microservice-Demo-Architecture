#!/usr/bin/env bash
set -e  # stop immediately if any step fails

echo "==> 1. Creating external network (skips if it already exists)"
docker network inspect app-network >/dev/null 2>&1 || docker network create app-network

echo "==> 2. Starting shared services (Redis, RabbitMQ)"
cd micro-services/shared
docker compose -f redis-stack.yml --env-file docker.env up -d --build
docker compose -f rabbitmq-stack.yml --env-file docker.env up -d --build
cd ../..

echo "==> 3. Starting User Service"
cd micro-services/laravel-user-service
docker compose -f user-stack.yml --env-file docker.env up -d --build
cd ../..

echo "==> 4. Starting Product Service"
cd micro-services/nestjs-product-service
docker compose -f product-stack.yml --env-file docker.env up -d --build
cd ../..

echo "==> 5. Starting Order Service"
cd micro-services/laravel-order-service
docker compose -f order-stack.yml --env-file docker.env up -d --build
cd ../..

echo "==> 6. Starting Payment Service"
cd micro-services/springboot-payment-service
docker compose -f payment-stack.yml --env-file .env up -d --build
cd ../..

echo "==> 7. Starting Inventory Service"
cd micro-services/springboot-inventory-service
docker compose -f inventory-stack.yml --env-file .env up -d --build
cd ../..

echo "==> 8. Starting Notification Service"
cd micro-services/nestjs-notification-service
docker compose -f notification-stack.yml --env-file docker.env up -d --build
cd ../..

echo "==> 9. Starting API Gateway (3 instances)"
cd micro-services/springboot-api-gateway
docker compose -f gateway-stack.yml --env-file docker.env up -d --build
cd ../..

echo "==> 10. Starting Nginx load balancer"
cd nginx
docker compose -f nginx-stack.yml up -d --build
cd ..

echo "==> All services are up. Entry point: http://localhost"