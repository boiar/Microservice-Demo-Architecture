package com.example.inventoryservice.dto.response;

import com.fasterxml.jackson.annotation.JsonFormat;
import com.fasterxml.jackson.annotation.JsonPropertyOrder;
import lombok.Builder;
import lombok.Value;
import org.jspecify.annotations.Nullable;

import java.time.LocalDateTime;

@Value
@Builder
@JsonPropertyOrder({"status", "message", "timestamp", "data"})
public class ResponseAPI<T>{

    String status;
    String message;

    @Nullable
    T data;

    @JsonFormat(pattern = "yyyy-MM-dd hh:mm:ss")
    LocalDateTime timestamp;

    public static <T> ResponseAPI<T> success(String message, @Nullable T data) {
        return ResponseAPI.<T>builder()
                .status("success")
                .message(message)
                .data(data)
                .timestamp(LocalDateTime.now())
                .build();
    }
}
