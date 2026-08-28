CREATE DATABASE IF NOT EXISTS `product_db`;

CREATE USER IF NOT EXISTS 'root'@'%' IDENTIFIED BY 'root';
GRANT ALL PRIVILEGES ON `product_db`.* TO 'root'@'%';
FLUSH PRIVILEGES;

USE `product_db`;

CREATE TABLE IF NOT EXISTS `product` (
                                         id INT PRIMARY KEY AUTO_INCREMENT,
                                         name VARCHAR(255) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    description TEXT,
    qty INT DEFAULT 0
    );

INSERT INTO product (name, price, qty, description) VALUES
('Product 1', 50, 100, 'Description for product 1'),
('Product 2', 100, 50, 'Description for product 2'),
('Product 3', 100, 75, 'Description for product 3'),
('Product 4', 1000, 120, 'Description for product 4'),
('Product 5', 250, 200, 'Description for product 5'),
('Product 6', 350, 60, 'Description for product 6'),
('Product 7', 400, 80, 'Description for product 7'),
('Product 8', 450, 90, 'Description for product 8'),
('Product 9', 500, 30, 'Description for product 9'),
('Product 10', 55.99, 150, 'Description for product 10');
