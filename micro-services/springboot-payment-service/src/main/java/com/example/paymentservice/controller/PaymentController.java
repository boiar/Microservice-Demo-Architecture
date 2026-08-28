package com.example.paymentservice.controller;

import com.example.paymentservice.dto.response.PaymentResponse;
import com.example.paymentservice.dto.response.ResponseDto;
import com.example.paymentservice.service.PaymentService;
import lombok.RequiredArgsConstructor;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PathVariable;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RestController;

import java.util.List;

@RestController
@RequestMapping("/api/v1/payments")
@RequiredArgsConstructor
public class PaymentController {

    private final PaymentService paymentService;

    @GetMapping
    public ResponseEntity<ResponseDto<List<PaymentResponse>>> getAll() {
        List<PaymentResponse> payments = paymentService.getAll();
        return ResponseEntity.ok(ResponseDto.success("Payments retrieved", payments));
    }

    @GetMapping("/order/{orderId}")
    public ResponseEntity<ResponseDto<PaymentResponse>> getByOrderId(@PathVariable String orderId) {
        PaymentResponse payment = paymentService.getByOrderId(orderId);
        return ResponseEntity.ok(ResponseDto.success("Payment retrieved", payment));
    }


}
