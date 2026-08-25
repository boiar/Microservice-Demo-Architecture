package com.example.gateway.dto;

import lombok.Getter;
import lombok.Setter;

@Setter
@Getter
public class ApiError {
    private int status;
    private String code;
    private String message;
    private Object details;

    public ApiError(int status, String code, String message, Object details) {
        this.status = status;
        this.code = code;
        this.message = message;
        this.details = details;
    }

}
