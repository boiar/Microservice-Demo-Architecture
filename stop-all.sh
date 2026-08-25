#!/usr/bin/env bash
set +e  # keep going even if one stack is already down, so everything else still stops

echo "==> 1. Stopping Nginx"
cd nginx
docker compose -f nginx-stack.yml down
cd ..

echo "==> 2. Stopping API Gateway (3 instances)"
cd micro-services/springboot-api-gateway
docker compose -f gateway-stack.yml down
cd ../..

echo "==> 3. Stopping Notification Service"
cd micro-services/nestjs-notification-service
docker compose -f notification-stack.yml down
cd ../..

echo "==> 4. Stopping Inventory Service"
cd micro-services/springboot-inventory-service
docker compose -f inventory-stack.yml down
cd ../..

echo "==> 5. Stopping Payment Service"
cd micro-services/springboot-payment-service
docker compose -f payment-stack.yml down
cd ../..

echo "==> 6. Stopping Order Service"
cd micro-services/laravel-order-service
docker compose -f order-stack.yml down
cd ../..

echo "==> 7. Stopping Product Service"
cd micro-services/nestjs-product-service
docker compose -f product-stack.yml down
cd ../..

echo "==> 8. Stopping User Service"
cd micro-services/laravel-user-service
docker compose -f user-stack.yml down
cd ../..

echo "==> 9. Stopping shared services (RabbitMQ, Redis)"
cd micro-services/shared
docker compose -f rabbitmq-stack.yml down
docker compose -f redis-stack.yml down
cd ../..

echo "==> All services stopped."
echo "    (network 'app-network' left in place — remove manually with:"
echo "     docker network rm app-network   — if you want a full clean slate)"