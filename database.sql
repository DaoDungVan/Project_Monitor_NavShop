-- CREATE TABLE users (
--     id INT AUTO_INCREMENT PRIMARY KEY,

--     name VARCHAR(100) NOT NULL,
--     email VARCHAR(100) NOT NULL UNIQUE,
--     password VARCHAR(255) NOT NULL,

--     role ENUM('admin','user') DEFAULT 'user',

--     phone VARCHAR(20) NULL,
--     address VARCHAR(255) NULL,
--     gender ENUM('male','female','other') NULL,
--     avatar VARCHAR(255) NULL,

--     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
-- );


-- INSERT INTO users (name, email, password, role)
-- VALUES (
--     'Admin',
--     'admin@gmail.com',
--     '$2y$10$SCjQW6bHGWE.dJhnZfr4ZuF3ELOB.l4c1OHMMwhEl3ZqqiFuSI7eK',
--     'admin'
-- );


-- CREATE TABLE products (
--     id INT AUTO_INCREMENT PRIMARY KEY,

--     name VARCHAR(150) NOT NULL,
--     brand VARCHAR(50) NOT NULL,

--     size INT NOT NULL,                 -- inch (24, 27, 32)
--     resolution VARCHAR(20) NOT NULL,   -- FHD, 2K, 4K
--     panel VARCHAR(20) NOT NULL,        -- IPS, VA, OLED
--     is_curved TINYINT(1) DEFAULT 0,    -- 0: phẳng, 1: cong

--     price INT NOT NULL,
--     image VARCHAR(255) NULL,
--     description TEXT NULL,

--     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
-- );

-- CREATE TABLE orders (
--     id INT AUTO_INCREMENT PRIMARY KEY,

--     total_price INT NOT NULL,
--     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
-- );


-- CREATE TABLE order_items (
--     id INT AUTO_INCREMENT PRIMARY KEY,

--     order_id INT NOT NULL,
--     product_id INT NOT NULL,

--     product_name VARCHAR(150) NOT NULL,
--     price INT NOT NULL,
--     qty INT NOT NULL,

--     FOREIGN KEY (order_id) REFERENCES orders(id)
-- );