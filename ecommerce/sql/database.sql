-- ============================================================
-- E-Trgovina — SQL skripta za kreiranje baze podataka
-- Import: phpMyAdmin ili mysql CLI
-- Admin nalog se kreira pokretanjem: install/setup.php
-- ============================================================

CREATE DATABASE IF NOT EXISTS ecommerce_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE ecommerce_db;

-- Korisnici
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('customer', 'admin') NOT NULL DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Kategorije
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Proizvodi
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    image VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_products_category
        FOREIGN KEY (category_id) REFERENCES categories(id)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Korpa
CREATE TABLE IF NOT EXISTS cart_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_user_product (user_id, product_id),
    CONSTRAINT fk_cart_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_cart_product
        FOREIGN KEY (product_id) REFERENCES products(id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Narudžbe
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    shipping_address TEXT NOT NULL,
    phone VARCHAR(20) NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled')
        NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_orders_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Stavke narudžbe
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    CONSTRAINT fk_order_items_order
        FOREIGN KEY (order_id) REFERENCES orders(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_order_items_product
        FOREIGN KEY (product_id) REFERENCES products(id)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE INDEX idx_products_category ON products(category_id);
CREATE INDEX idx_orders_user ON orders(user_id);
CREATE INDEX idx_orders_status ON orders(status);

-- Početne kategorije
INSERT INTO categories (name, description) VALUES
('Elektronika', 'Računarska oprema i elektronski uređaji'),
('Odjeća', 'Muška i ženska odjeća'),
('Knjige', 'Udžbenici i stručna literatura');

-- Početni proizvodi (slike preko URL-a)
INSERT INTO products (category_id, name, description, price, stock, image) VALUES
(1, 'Bežična miš', 'Ergonomska bežična miš sa USB prijemnikom. Idealna za svakodnevni rad.', 29.90, 50, 'https://picsum.photos/seed/mouse/400/300'),
(1, 'Mehanička tastatura', 'Tastatura sa plavim prekidačima i pozadinskim osvjetljenjem.', 89.00, 30, 'https://picsum.photos/seed/keyboard/400/300'),
(1, 'USB-C hub', '7-u-1 USB-C adapter sa HDMI, USB i SD čitačem kartica.', 45.50, 25, 'https://picsum.photos/seed/hub/400/300'),
(2, 'Pamučna majica', '100% pamuk, dostupna u više boja. Udobna za svakodnevno nošenje.', 19.99, 100, 'https://picsum.photos/seed/tshirt/400/300'),
(2, 'Džemper', 'Topli džemper za jesenje i zimske dane.', 49.99, 40, 'https://picsum.photos/seed/sweater/400/300'),
(3, 'Uvod u programiranje', 'Udžbenik za početnike u svijetu programiranja.', 35.00, 60, 'https://picsum.photos/seed/book1/400/300'),
(3, 'Baze podataka — praktični vodič', 'Knjiga o dizajnu i implementaciji relacionih baza podataka.', 42.00, 35, 'https://picsum.photos/seed/book2/400/300');
