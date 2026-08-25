# API Gateway — Manual Test Scenarios

Manual QA scenarios for the Spring Boot API Gateway (`api-gateway-1/2/3`) sitting behind Nginx, covering JWT auth, dynamic routing, rate limiting, and circuit breaking against the downstream microservices (User, Product, Order, Payment, Inventory, Notification).

---

## 1. Core Happy-Path Flow (End-to-End Smoke Test)

The primary scenario — touches almost every service and the full event chain.

| Step | Action | Expected Result |
|---|---|---|
| 1 | `POST` register via gateway → User Service | `201 Created`; user created; `user.registered` published to RabbitMQ; Notification Service logs/sends welcome message |
| 2 | `POST` login with credentials | `200 OK`; JWT access token + refresh token returned |
| 3 | `GET` product details via gateway (JWT in `Authorization` header) | `200 OK`; gateway forwards request to Product Service |
| 4 | `POST` add product to wishlist (optional) | `200/201`; Product Service handles it; gateway routes correctly |
| 5 | `POST` create order (referencing product, JWT attached) | `201 Created`; `order.created` published to RabbitMQ |
| 6 | `POST` pay for order via gateway → Payment Service | `payment.successful` or `payment.failed` published depending on scenario |
| 7 | Confirm Inventory Service reaction | Stock reserved/deducted after consuming `order.created` / `payment.successful` |
| 8 | Check Notification Service | All expected events (user.registered, order.created, payment.successful/failed) were received and processed |

> This flow is the best overall smoke test — if it passes end to end, routing, auth, and async messaging are all working together correctly.

---

## 2. Auth / JWT Tests (Gateway Responsibility)

| Test Case | Steps | Expected Result |
|---|---|---|
| Missing token | Call a protected endpoint with no `Authorization` header | `401 Unauthorized`, rejected at the gateway before reaching the service |
| Expired token | Call with an expired JWT | `401 Unauthorized` |
| Malformed/tampered token | Call with an invalid or tampered JWT | `401 Unauthorized` |
| Refresh token flow | Use refresh token to obtain a new access token | New access token issued; old refresh token invalidated if rotation is enabled |
| Public endpoint access | Call an unauthenticated-allowed endpoint (e.g. product listing) with no token | `200 OK` — confirms gateway isn't blanket-blocking all traffic |

---

## 3. Routing Tests

| Test Case | Steps | Expected Result |
|---|---|---|
| Correct service routing | Hit each service's endpoint through the gateway (`http://localhost` via Nginx) | Response body matches the correct service's shape (e.g. product fields for product routes, not user fields) |
| Unmapped route | Call an intentionally invalid/unmapped route | Clean `404 Not Found` from the gateway, not a raw `500` |

---

## 4. Rate Limiting Tests

| Test Case | Steps | Expected Result |
|---|---|---|
| Per-route limit | Send rapid repeated requests to one route (e.g. login) from the same client/IP | `429 Too Many Requests` once threshold is exceeded |
| Isolation across routes | Continue hitting a *different* route as the same user immediately after being throttled | That route is not also throttled (assuming rate limiting is configured per-route, not global) |

---

## 5. Circuit Breaking Tests

| Test Case | Steps | Expected Result |
|---|---|---|
| Trip the breaker | Stop a downstream service (e.g. `docker compose stop` on Payment Service), then send repeated requests to its route | Breaker trips after N failures; gateway returns a fast fallback/error instead of hanging or timing out |
| Recovery | Restart the stopped service | Breaker eventually closes again; requests succeed normally |

---

## 6. Load Balancing Across Gateway Instances

| Test Case | Steps | Expected Result |
|---|---|---|
| Traffic distribution | Send repeated requests to `http://localhost` (via Nginx) and inspect logs/headers for instance ID | Requests are distributed across `api-gateway-1`, `api-gateway-2`, `api-gateway-3` — not always the same instance |
| Instance failure | Stop one gateway instance (e.g. `api-gateway-2`) | Nginx routes around it; traffic continues flowing through the remaining two instances |

---

## 7. Failure / Edge-Case Flows

| Test Case | Steps | Expected Result |
|---|---|---|
| Order without stock | Create an order for an out-of-stock product | Inventory Service rejects/flags it; no payment attempted, or `payment.failed` if overbooking is allowed |
| Payment failure | Simulate a failed payment | `payment.failed` published; Inventory does **not** deduct stock; Notification sends a failure message |
| Duplicate registration | Register with an email that already exists | Clean `409 Conflict` or validation error, not a `500` |

---

## Notes

- Run these scenarios against `http://localhost` (through Nginx) to exercise the full stack, not just individual services in isolation.
- For circuit breaker and load balancing tests, keep an eye on service logs (`docker compose logs -f <service>`) to confirm behavior, not just HTTP status codes.
- Consider automating scenario 1 (core happy path) as a repeatable smoke test once manual verification passes.