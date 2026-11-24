CREATE DATABASE IF NOT EXISTS vaporsaint_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE vaporsaint_db;

CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  first_name VARCHAR(80) NOT NULL,
  last_name VARCHAR(80) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('customer', 'admin') NOT NULL DEFAULT 'customer',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS products (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  category VARCHAR(80) NOT NULL,
  sku VARCHAR(50) NOT NULL UNIQUE,
  price DECIMAL(10,2) NOT NULL DEFAULT 0,
  stock INT NOT NULL DEFAULT 0,
  reorder_level INT NOT NULL DEFAULT 5,
  image VARCHAR(255) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS orders (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NULL,
  customer_name VARCHAR(160) NOT NULL,
  customer_email VARCHAR(190) NOT NULL,
  address VARCHAR(255) NOT NULL,
  phone VARCHAR(40) NOT NULL,
  payment_method VARCHAR(40) NOT NULL,
  total DECIMAL(10,2) NOT NULL DEFAULT 0,
  status VARCHAR(30) NOT NULL DEFAULT 'PENDING',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS order_items (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id INT UNSIGNED NOT NULL,
  product_id INT UNSIGNED NULL,
  product_name VARCHAR(150) NOT NULL,
  unit_price DECIMAL(10,2) NOT NULL,
  quantity INT UNSIGNED NOT NULL,
  CONSTRAINT fk_items_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  CONSTRAINT fk_items_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
);

INSERT IGNORE INTO products (id, name, category, sku, price, stock, reorder_level, image) VALUES
(1, 'Static Bloom Tee', 'Tops', 'VS-TEE-001', 1290, 20, 5, 'images/static-bloom.jpg'),
(2, 'Signal Hoodie', 'Tops', 'VS-HOOD-001', 2490, 12, 5, 'images/signal-hoodie.jpg'),
(3, 'Night Shift Shell', 'Outerwear', 'VS-SHELL-001', 3290, 8, 3, 'images/night-shell.jpg'),
(4, 'Transit Cap', 'Accessories', 'VS-CAP-001', 890, 15, 5, 'images/transit-cap.jpg'),
(5, 'Afterglow Bomber', 'Outerwear', 'VS-BOMB-001', 3990, 7, 3, 'images/afterglow-bomber.jpg'),
(6, 'Void Cargo Pant', 'Bottoms', 'VS-CARGO-001', 2190, 10, 4, 'images/void-cargo.jpg');
