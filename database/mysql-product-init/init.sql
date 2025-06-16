CREATE DATABASE IF NOT EXISTS `product_db`;

CREATE USER IF NOT EXISTS 'root'@'%' IDENTIFIED BY 'root';
GRANT ALL PRIVILEGES ON `product_db`.* TO 'root'@'%';
FLUSH PRIVILEGES;


USE `product_db`;

CREATE TABLE IF NOT EXISTS `product` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `price` DECIMAL(10,2) NOT NULL,
    `description` TEXT
);


INSERT INTO product (name, price, description) VALUES
('Product 1', 10.99, 'Description for product 1'),
('Product 2', 15.50, 'Description for product 2'),
('Product 3', 20.00, 'Description for product 3'),
('Product 4', 25.75, 'Description for product 4'),
('Product 5', 30.30, 'Description for product 5'),
('Product 6', 35.10, 'Description for product 6'),
('Product 7', 40.00, 'Description for product 7'),
('Product 8', 45.25, 'Description for product 8'),
('Product 9', 50.60, 'Description for product 9'),
('Product 10', 55.99, 'Description for product 10');