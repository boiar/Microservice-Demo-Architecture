package com.example.gateway.filter;

import com.example.gateway.dto.ApiError;
import com.example.gateway.dto.ApiResponse;
import org.springframework.cloud.gateway.filter.GatewayFilterChain;
import org.springframework.cloud.gateway.filter.GlobalFilter;
import org.springframework.core.Ordered;
import org.springframework.core.io.buffer.DataBuffer;
import org.springframework.core.io.buffer.DataBufferUtils;
import org.springframework.http.HttpStatus;
import org.springframework.http.server.reactive.ServerHttpResponse;
import org.springframework.http.server.reactive.ServerHttpResponseDecorator;
import org.springframework.stereotype.Component;
import org.springframework.web.server.ServerWebExchange;
import reactor.core.publisher.Flux;
import reactor.core.publisher.Mono;
import com.fasterxml.jackson.databind.ObjectMapper;
import org.reactivestreams.Publisher;



@Component
public class ResponseEnvelopeFilter implements GlobalFilter, Ordered {

    private final ObjectMapper mapper = new ObjectMapper();

    @Override
    public int getOrder() {
        return -1; // run late to see response
    }

    @Override
    public Mono<Void> filter(ServerWebExchange exchange, GatewayFilterChain chain) {
        String correlationId = exchange.getRequest().getHeaders().getFirst("X-Correlation-Id");

        ServerHttpResponse originalResponse = exchange.getResponse();
        ServerHttpResponseDecorator decorated = new ServerHttpResponseDecorator(originalResponse){

            @Override
            public Mono<Void> writeWith(Publisher<? extends DataBuffer> body) {
                return DataBufferUtils.join(body).flatMap(dataBuffer -> {
                    byte[] bytes = new byte[dataBuffer.readableByteCount()];
                    dataBuffer.read(bytes);
                    DataBufferUtils.release(dataBuffer);

                    HttpStatus status = HttpStatus.valueOf(originalResponse.getStatusCode().value());
                    byte[] wrapped;
                    try {
                        Object originalJson = bytes.length > 0
                                ? mapper.readValue(bytes, Object.class)
                                : null;

                        if (status.is2xxSuccessful()) {
                            wrapped = mapper.writeValueAsBytes(
                                    ApiResponse.success(originalJson, correlationId));
                        } else {
                            ApiError error = new ApiError(
                                    status.value(),
                                    status.name(),
                                    status.getReasonPhrase(),
                                    originalJson
                            );
                            wrapped = mapper.writeValueAsBytes(
                                    ApiResponse.error(error, correlationId));
                        }
                    } catch (Exception e) {
                        wrapped = bytes;
                    }

                    originalResponse.getHeaders().setContentLength(wrapped.length);
                    DataBuffer buffer = originalResponse.bufferFactory().wrap(wrapped);
                    return originalResponse.writeWith(Mono.just(buffer));
                });
            }

            @Override
            public Mono<Void> writeAndFlushWith(Publisher<? extends Publisher<? extends DataBuffer>> body) {
                return writeWith(Flux.from(body).flatMapSequential(p -> p));
            }

        };
        return chain.filter(exchange.mutate().response(decorated).build());
    }
}
