package com.example.gateway.dto;

import com.fasterxml.jackson.annotation.JsonInclude;
import lombok.Getter;
import lombok.Setter;

import java.time.Instant;

@Getter
@Setter
@JsonInclude(JsonInclude.Include.NON_NULL)
public class ApiResponse<T> {
    private boolean success;
    private String correlationId;
    private String timestamp;
    private T data;
    private ApiError error;


    public static <T> ApiResponse<T> success(T data, String correlationId) {
        ApiResponse<T> r = new ApiResponse<>();
        r.success = true;
        r.correlationId = correlationId;
        r.timestamp = Instant.now().toString();
        r.data = data;
        return r;
    }

    public static <T> ApiResponse<T> error(ApiError error, String correlationId) {
        ApiResponse<T> r = new ApiResponse<>();
        r.success = false;
        r.correlationId = correlationId;
        r.timestamp = Instant.now().toString();
        r.error = error;
        return r;
    }
}
