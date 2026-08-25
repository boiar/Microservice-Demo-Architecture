package com.example.gateway.config;

import org.springframework.cloud.gateway.filter.ratelimit.KeyResolver;
import org.springframework.context.annotation.Bean;
import org.springframework.context.annotation.Configuration;
import reactor.core.publisher.Mono;

@Configuration
public class RateLimiterConfig {

    /**
     * Rate-limits by client IP. Swap for a user-id-based resolver once auth is
     * wired in everywhere, if you want per-user (rather than per-IP) limits.
     */

    @Bean
    public KeyResolver ipKeyResolver(){
        return exchange -> {
            exchange.getRequest().getRemoteAddress();
            String ip = exchange.getRequest().getRemoteAddress().getAddress().getHostAddress();
            return Mono.just(ip);
        };
    }
}
