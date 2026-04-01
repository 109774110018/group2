-- Sales Tracking System Database
-- Run this in phpMyAdmin after creating db_sales

CREATE DATABASE IF NOT EXISTS db_sales;
USE db_sales;

-- Users table (for login system)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin','staff') DEFAULT 'staff',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products / Items table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(100) NOT NULL,
    category VARCHAR(50) NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    stock_quantity INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Customers table
CREATE TABLE IF NOT EXISTS customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Sales table
CREATE TABLE IF NOT EXISTS sales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    sale_date DATE NOT NULL,
    payment_status ENUM('paid','pending','cancelled') DEFAULT 'pending',
    notes TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Default admin user (password: admin123)
INSERT INTO users (username, password, full_name, role) VALUES
('admin', 'admin123', 'System Admin', 'admin');

-- Sample products
INSERT INTO products (product_name, category, unit_price, stock_quantity) VALUES
('Laptop Pro 15"', 'Electronics', 45000.00, 20),
('Wireless Mouse', 'Accessories', 850.00, 50),
('USB-C Hub', 'Accessories', 1200.00, 35),
('Mechanical Keyboard', 'Electronics', 3500.00, 15),
('Monitor 24"', 'Electronics', 12000.00, 10);

-- Sample customers
INSERT INTO customers (customer_name, email, phone) VALUES
('Juan dela Cruz', 'juan@email.com', '09171234567'),
('Maria Santos', 'maria@email.com', '09281234567'),
('Pedro Reyes', 'pedro@email.com', '09391234567');

-- Sample sales
INSERT INTO sales (customer_id, product_id, quantity, unit_price, total_amount, sale_date, payment_status) VALUES
(1, 1, 1, 45000.00, 45000.00, '2025-03-01', 'paid'),
(2, 2, 2, 850.00, 1700.00, '2025-03-05', 'paid'),
(3, 4, 1, 3500.00, 3500.00, '2025-03-10', 'pending'),
(1, 3, 3, 1200.00, 3600.00, '2025-03-15', 'paid'),
(2, 5, 1, 12000.00, 12000.00, '2025-03-20', 'pending');
