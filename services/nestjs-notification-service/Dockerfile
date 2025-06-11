FROM node:20-alpine AS builder

WORKDIR /app

# Copy package.json and package-lock.json to install dependencies first
COPY services/nestjs-notification-service/package*.json ./

RUN npm install

# Copy the rest of the application source code
COPY services/nestjs-notification-service/ .

# Build the NestJS application
RUN npm run build

# === RUN STAGE ===
# Use a leaner Node.js image for the final production container
FROM node:20-alpine AS runner

WORKDIR /app

# Copy only the compiled output ('dist' folder) and node_modules from the builder stage
COPY --from=builder /app/dist ./dist
COPY --from=builder /app/node_modules ./node_modules

EXPOSE 3000
CMD ["node", "dist/main"]

COPY services/nestjs-notification-service/wait-for-it.sh /usr/bin/wait-for-it
RUN chmod +x /usr/bin/wait-for-it
SHELL ["/bin/sh", "-c"]
