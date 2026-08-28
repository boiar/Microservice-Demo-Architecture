package com.example.gateway.config;

import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.RestController;
import reactor.core.publisher.Mono;

import java.time.Instant;
import java.util.Map;

/**
 * Hit by the CircuitBreaker gateway filter when payment-service or inventory-service
 * are down/slow/timing out, instead of letting the client hang or get a raw 500.
 */

@RestController
public class FallbackController {

    @GetMapping("/fallback/service")
    public Mono<ResponseEntity<Map<String, Object>>> serviceFallback() {
        return Mono.just(
                ResponseEntity
                        .status(HttpStatus.SERVICE_UNAVAILABLE)
                        .body(Map.of(
                                "error", "SERVICE_UNAVAILABLE",
                                "message", "The requested service is temporarily unavailable.",
                                "timestamp", Instant.now().toString()
                        ))
        );
    }

}
