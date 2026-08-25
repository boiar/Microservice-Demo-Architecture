package com.example.paymentservice.config;

import org.springframework.amqp.core.Binding;
import org.springframework.amqp.core.BindingBuilder;
import org.springframework.amqp.core.Queue;
import org.springframework.amqp.core.TopicExchange;
import org.springframework.context.annotation.Bean;
import org.springframework.context.annotation.Configuration;

@Configuration
public class RabbitMQConfig {
    @Bean
    public Queue paymentQueue() {
        return new Queue("payment.inventory.reserved", true);
    }

    @Bean
    public TopicExchange exchange() {
        return new TopicExchange("payment.exchange");
    }

    @Bean
    public Binding binding() {
        return BindingBuilder
                .bind(paymentQueue())
                .to(exchange())
                .with("inventory.reserved");
    }
}