package com.example.inventoryservice.config;

import org.springframework.amqp.core.*;
import org.springframework.amqp.rabbit.connection.ConnectionFactory;
import org.springframework.amqp.rabbit.core.RabbitTemplate;
import org.springframework.context.annotation.Bean;
import org.springframework.context.annotation.Configuration;


@Configuration
public class RabbitMQConfig {

    // Inventory's own exchange
    public static final String INVENTORY_EXCHANGE = "inventory.exchange";

    // Upstream exchanges/queues Inventory listens to
    public static final String ORDER_EXCHANGE = "order.exchange";
    public static final String ORDER_CREATED_QUEUE = "inventory.order-created.queue";
    public static final String ORDER_CANCELED_QUEUE = "inventory.order-canceled.queue";

    private static final String ORDER_CREATED_ROUTING_KEY = "order.created";
    private static final String ORDER_CANCELLED_ROUTING_KEY = "order.cancelled";


    @Bean
    public RabbitTemplate rabbitTemplate(ConnectionFactory connectionFactory) {
        return new RabbitTemplate(connectionFactory);
    }


    // Exchanges
    @Bean
    public TopicExchange inventoryExchange() {
        return new TopicExchange(INVENTORY_EXCHANGE);
    }

    @Bean
    public TopicExchange orderExchange() {
        return new TopicExchange(ORDER_EXCHANGE);
    }

    // Queues
    @Bean
    public Queue orderCreatedQueue() {
        return new Queue(ORDER_CREATED_QUEUE);
    }

    @Bean
    public Queue orderCanceledQueue() {
        return new Queue(ORDER_CANCELED_QUEUE);
    }

    // Bindings

    @Bean
    public Binding orderCreatedBinding(Queue orderCreatedQueue, TopicExchange orderExchange) {
        return BindingBuilder.bind(orderCreatedQueue).to(orderExchange).with(ORDER_CREATED_ROUTING_KEY);
    }

    @Bean
    public Binding orderCanceledBinding(Queue orderCanceledQueue, TopicExchange orderExchange) {
        return BindingBuilder.bind(orderCanceledQueue).to(orderExchange).with(ORDER_CANCELLED_ROUTING_KEY);
    }

}
