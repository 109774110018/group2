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
INSERT INTO users (username, `password`, full_name, role) VALUES
('admin', 'admin123', 'System Admin', 'admin');

-- Products
INSERT INTO products (product_name, category, unit_price, stock_quantity) VALUES
('IPhone 17', 'Electronics', 75000.00, 15),
('Wireless Mouse', 'Accessories', 850.00, 50),
('USB-C Hub', 'Accessories', 1200.00, 35),
('Mechanical Keyboard', 'Electronics', 3500.00, 15),
('MacBook10', 'Electronics', 100000.01, 5);

-- Customers
INSERT INTO customers (customer_name, email, phone, address) VALUES
('Lebrone Aramil', 'leb@gmail.com', '09931234567', 'brgy tabi-tabi lang'),
('David Jamil C. Esteban', 'doobay@gmail.com', '09123456789', 'brgy lipad laging high'),
('Julian Carl Malolos', 'atJulian@email.com', '09391234567', 'where at @alaminos'),
('Aaron Mercado', 'AaronMercado@gmail.com', '093718273681', 'jeepney'),
('Sam Ceremonia', 'samceremonia@gmail.com', '09123456789', 'Energy Revive');

-- Sales
INSERT INTO sales (customer_id, product_id, quantity, unit_price, total_amount, sale_date, payment_status) VALUES
(2, 2, 2, 850.00, 1700.00, '2025-03-05', 'paid'),
(3, 4, 1, 3500.00, 3500.00, '2025-03-10', 'pending'),
(1, 3, 3, 1200.00, 3600.00, '2025-03-15', 'paid'),
(2, 4, 1, 3500.00, 3500.00, '2026-04-01', 'pending'),
(5, 1, 2, 75000.00, 150000.00, '2026-04-01', 'pending'),
(4, 5, 1, 100000.01, 100000.01, '2026-04-01', 'cancelled'),
(4, 5, 1, 100000.01, 100000.01, '2026-04-01', 'cancelled');
