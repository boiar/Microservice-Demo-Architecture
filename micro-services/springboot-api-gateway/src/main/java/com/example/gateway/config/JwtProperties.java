package com.example.gateway.config;

import org.springframework.boot.context.properties.ConfigurationProperties;

import java.util.List;

@ConfigurationProperties(prefix = "jwt")
public class JwtProperties {

    /** Shared HMAC secret used to verify token signatures. */
    private String secret;

    /** Route path patterns that skip JWT validation entirely (login, register, health checks). */
    private List<String> publicPaths = List.of();

    public String getSecret(){
        return secret;
    }

    public void setSecret(String secret) {
        this.secret = secret;
    }

    public List<String> getPublicPaths() {
        return publicPaths;
    }

    public void setPublicPaths(List<String> publicPaths) {
        this.publicPaths = publicPaths;
    }

}
