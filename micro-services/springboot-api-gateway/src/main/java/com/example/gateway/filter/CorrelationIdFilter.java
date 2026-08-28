package com.example.gateway.filter;

import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.slf4j.MDC;
import org.springframework.core.Ordered;
import org.springframework.http.server.reactive.ServerHttpRequest;
import org.springframework.stereotype.Component;
import org.springframework.web.server.ServerWebExchange;
import org.springframework.web.server.WebFilter;
import org.springframework.web.server.WebFilterChain;
import reactor.core.publisher.Mono;

import java.util.UUID;


/**
 * Stamps every request with an X-Correlation-Id
 * Downstream services should log this id
 * it in the event payload so a single request can be traced across async hops too.
 */

@Component
public class CorrelationIdFilter implements WebFilter, Ordered {
    private static final Logger log = LoggerFactory.getLogger(CorrelationIdFilter.class);
    private static final String HEADER = "X-Correlation-Id";
    private static final String MDC_KEY = "correlationId";

    @Override
    public int getOrder() {
        return -2; // before JWT filter, so even 401s get logged with an id
    }

    @Override
    public Mono<Void> filter(ServerWebExchange exchange, WebFilterChain chain) {

        ServerHttpRequest request = exchange.getRequest();
        String correlationId = request.getHeaders().getFirst(HEADER);

        if (correlationId == null || correlationId.isBlank()) {
            correlationId = UUID.randomUUID().toString();
        }

        final String cid = correlationId;
        final long start = System.currentTimeMillis();


        ServerHttpRequest mutatedRequest = exchange.getRequest().mutate()
                .header(HEADER, cid)
                .build();

        exchange.getResponse().getHeaders().add(HEADER, correlationId);

        // Log the inbound request immediately
        log.info("--> reqId={} method={} path={} remote={}",
                cid, request.getMethod(), request.getURI().getPath(),
                request.getRemoteAddress());

        return chain.filter(exchange.mutate().request(mutatedRequest).build())
            // Log outcome once the chain completes (success or error)
                .doOnEach(signal -> {
                    if(signal.isOnComplete() || signal.isOnError()) {
                        long duration = System.currentTimeMillis() - start;
                        log.info("<-- reqId={} method={} path={} status={} duration={}ms",
                                cid, request.getMethod(), request.getURI().getPath(),
                                exchange.getResponse().getStatusCode(), duration);
                    }
                })
                // Make correlationId available via MDC for THIS request's log lines,
                // safely scoped per-subscription even though threads get reused.
                .doOnEach(signal -> {
                    if (!signal.isOnComplete()) {
                        MDC.put(MDC_KEY, cid);
                    }
                })
                .doFinally(signalType -> MDC.remove(MDC_KEY))
                .contextWrite(ctx -> ctx.put(MDC_KEY, cid));
    }
}
