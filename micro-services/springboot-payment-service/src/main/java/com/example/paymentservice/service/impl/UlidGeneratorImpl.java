package com.example.paymentservice.service.impl;

import com.example.paymentservice.service.UlidGenerator;
import com.github.f4b6a3.ulid.UlidCreator;
import org.springframework.stereotype.Service;

@Service
public class UlidGeneratorImpl implements UlidGenerator {

    @Override
    public String generate() {
        return UlidCreator.getUlid().toString();
    }
}
