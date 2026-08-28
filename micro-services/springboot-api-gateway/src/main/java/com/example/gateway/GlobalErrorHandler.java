package com.example.gateway;

import com.example.gateway.dto.ApiError;
import com.example.gateway.dto.ApiResponse;
import org.springframework.boot.web.reactive.error.ErrorWebExceptionHandler;
import org.springframework.core.annotation.Order;
import org.springframework.core.io.buffer.DataBuffer;
import org.springframework.http.HttpStatus;
import org.springframework.http.server.reactive.ServerHttpResponse;
import org.springframework.stereotype.Component;
import org.springframework.web.server.ResponseStatusException;
import org.springframework.web.server.ServerWebExchange;
import reactor.core.publisher.Mono;
import com.fasterxml.jackson.databind.ObjectMapper;


@Component
@Order(-2)
public class GlobalErrorHandler implements ErrorWebExceptionHandler {

    private final ObjectMapper mapper = new ObjectMapper();

    @Override
    public Mono<Void> handle(ServerWebExchange exchange, Throwable ex) {
        ServerHttpResponse response = exchange.getResponse();
        HttpStatus status = resolveStatus(ex);
        response.setStatusCode(status);
        response.getHeaders().add("Content-Type", "application/json");

        String correlationId = exchange.getRequest().getHeaders().getFirst("X-Correlation-Id");
        ApiError error = new ApiError(status.value(), status.name(), ex.getMessage(), null);
        ApiResponse<Object> envelope = ApiResponse.error(error, correlationId);

        try {
            byte[] bytes = mapper.writeValueAsBytes(envelope);
            DataBuffer buffer = response.bufferFactory().wrap(bytes);
            return response.writeWith(Mono.just(buffer));
        } catch (Exception e) {
            return response.setComplete();
        }
    }

    private HttpStatus resolveStatus(Throwable ex) {
        if (ex instanceof ResponseStatusException rse) {
            return HttpStatus.valueOf(rse.getStatusCode().value());
        }
        return HttpStatus.INTERNAL_SERVER_ERROR;
    }
}
