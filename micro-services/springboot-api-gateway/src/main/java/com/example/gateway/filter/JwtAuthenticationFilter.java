package com.example.gateway.filter;

import com.example.gateway.config.JwtProperties;
import com.example.gateway.dto.ApiError;
import com.example.gateway.dto.ApiResponse;
import com.fasterxml.jackson.databind.ObjectMapper;
import io.jsonwebtoken.Claims;
import io.jsonwebtoken.JwtException;
import io.jsonwebtoken.Jwts;
import io.jsonwebtoken.security.Keys;
import org.springframework.core.Ordered;
import org.springframework.core.io.buffer.DataBuffer;
import org.springframework.http.HttpHeaders;
import org.springframework.http.HttpStatus;
import org.springframework.http.MediaType;
import org.springframework.http.server.reactive.ServerHttpRequest;
import org.springframework.http.server.reactive.ServerHttpResponse;
import org.springframework.stereotype.Component;
import org.springframework.util.AntPathMatcher;
import org.springframework.web.server.ServerWebExchange;
import org.springframework.web.server.WebFilter;
import org.springframework.web.server.WebFilterChain;
import reactor.core.publisher.Mono;

import javax.crypto.SecretKey;
import java.nio.charset.StandardCharsets;

@Component
public class JwtAuthenticationFilter implements WebFilter, Ordered {

    private final JwtProperties jwtProperties;
    private final AntPathMatcher pathMatcher;
    private final SecretKey signingKey;
    private final ObjectMapper objectMapper = new ObjectMapper();


    public JwtAuthenticationFilter(JwtProperties jwtProperties, AntPathMatcher pathMatcher){
        this.jwtProperties = jwtProperties;
        this.pathMatcher = pathMatcher;

        // HS256 shared-secret key. Must match the key laravel-user-service signs with.
        this.signingKey = Keys.hmacShaKeyFor(jwtProperties.getSecret().getBytes(StandardCharsets.UTF_8));
    }

    @Override
    public int getOrder() {
        return -1; // run before routing filters
    }

    @Override
    public Mono<Void> filter(ServerWebExchange exchange, WebFilterChain chain) {
        String path = exchange.getRequest().getURI().getPath();

        // skip filter
        if (isPublicPath(path)) {
            return chain.filter(exchange);
        }

        String authHeader = exchange.getRequest().getHeaders().getFirst(HttpHeaders.AUTHORIZATION);
        if (authHeader == null || !authHeader.startsWith("Bearer ")) {
            return unauthorized(exchange, "Missing bearer token");
        }

        String token = authHeader.substring(7);

        try {

            Claims claims = Jwts.parser()
                    .verifyWith(signingKey)
                    .build()
                    .parseSignedClaims(token)
                    .getPayload();

            String userId = claims.getSubject();
            Object roles = claims.get("roles");

            ServerHttpRequest mutatedRequest = exchange.getRequest().mutate()
                    .header("X-User-Id", userId == null ? "" : userId)
                    .header("X-User-Roles", roles == null ? "" : roles.toString())
                    .header("X-Request-Source", "api-gateway")
                    .build();

            return chain.filter(exchange.mutate().request(mutatedRequest).build());
        } catch (JwtException | IllegalArgumentException ex){
            return unauthorized(exchange, "Invalid or expired token");
        }
    }

    private boolean isPublicPath(String path) {
        return jwtProperties.getPublicPaths().stream()
                .anyMatch(pattern -> pathMatcher.match(pattern, path));
    }

    private Mono<Void> unauthorized(ServerWebExchange exchange, String reason) {
        ServerHttpResponse response = exchange.getResponse();

        response.setStatusCode(HttpStatus.UNAUTHORIZED);
        response.getHeaders().setContentType(MediaType.APPLICATION_JSON);

        String correlationId =
                exchange.getRequest()
                        .getHeaders()
                        .getFirst("X-Correlation-Id");

        ApiError error = new ApiError(
                HttpStatus.UNAUTHORIZED.value(),
                "UNAUTHORIZED",
                reason,
                null
        );

        ApiResponse<Object> body =
                ApiResponse.error(error, correlationId);

        try {
            byte[] bytes = objectMapper.writeValueAsBytes(body);

            DataBuffer buffer =
                    response.bufferFactory().wrap(bytes);

            return response.writeWith(Mono.just(buffer));

        } catch (Exception e) {
            return response.setComplete();
        }

    }

}
