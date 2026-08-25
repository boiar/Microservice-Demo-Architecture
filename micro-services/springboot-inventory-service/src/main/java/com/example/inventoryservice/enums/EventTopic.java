package com.example.inventoryservice.enums;

public enum EventTopic {
    INVENTORY_STOCK("inventory.stock"),
    ORDER("order");

    private final String value;

    EventTopic(String value) {
        this.value = value;
    }

    public String value() {
        return value;
    }
}
