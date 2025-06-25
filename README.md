# Microservice Demo Architecture

![Microservice Architecture](./MongoDB.png)

A containerized, event-driven microservices architecture that decouples user, product, order, and notification domains. Built with Laravel and NestJS, designed for scalability, modularity, and ease of testing.

---

##  Project Overview

This project demonstrates a modular microservices system where each service handles a distinct business domain:

- **User Service** (Laravel)
- **Product Service** (NestJS)
- **Order Service** (Laravel)
- **Notification Service** (NestJS)

Services communicate asynchronously using Redis Pub/Sub. The architecture is fully containerized via Docker and optimized for CI/CD and testing environments.

---

## Business Logic by Service

### 1. **Laravel User Service**
- Handles user registration and login
- Manages JWT access and refresh tokens
- Publishes `user.registered` event to Redis

### 2. **Nest Product Service**
- Provides product details
- Manages product wishlists
- Publishes `product.updated` event to Redis

### 3. **Laravel Order Service**
- Manages user orders
- Publishes `order.created` event to Redis

### 4. **Nest Notification Service**
- Subscribes to:
  - `user.registered`
  - `product.updated`
  - `order.created`
- Sends notifications or logs events accordingly

---

## Key Features

- **Microservices Architecture**
  Each domain is independently deployed, maintained, and scaled.

- **Event-Driven Communication**
  Uses Redis Pub/Sub channels to propagate events between services.

- **JWT Authentication**
  Stateless authentication with access and refresh token handling.

- **Test-Ready Design**
  DTOs, service interfaces, and stubs support unit isolation and service mocking.

- **Dockerized Environment**
  Entire system is containerized with `docker-compose` for local and CI use.

- **Modular & Scalable**
  Add new services with minimal impact on existing functionality.

- **Isolated Testing Architecture**
  Database containers and service mocks allow robust and isolated test runs.

---

## Tools & Technologies

- **Laravel**
- **NestJS**
- **MySQL** – Relational DB for User, Product, and Order services
- **MongoDB** – NoSQL DB for Notification service
- **Redis Pub/Sub** – Event-driven service communication
- **JWT**
- **Docker & Docker Compose**
- **PHPUnit** – Unit testing for Laravel
- **Jest** – Unit testing for NestJS services
- **Postman** – API exploration and testing

