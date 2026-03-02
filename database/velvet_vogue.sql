-- ============================================
-- Velvet Vogue - Online Fashion Store Database
-- ============================================

CREATE DATABASE IF NOT EXISTS velvet_vogue;
USE velvet_vogue;

-- Categories Table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products Table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    sale_price DECIMAL(10,2) DEFAULT NULL,
    stock INT DEFAULT 0,
    images TEXT,
    sizes VARCHAR(255) DEFAULT 'XS,S,M,L,XL,XXL',
    colors VARCHAR(255) DEFAULT 'Black,White,Red,Blue',
    featured TINYINT(1) DEFAULT 0,
    trending TINYINT(1) DEFAULT 0,
    new_arrival TINYINT(1) DEFAULT 1,
    rating DECIMAL(3,2) DEFAULT 4.50,
    reviews_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    city VARCHAR(100),
    country VARCHAR(100) DEFAULT 'Pakistan',
    role ENUM('user','admin') DEFAULT 'user',
    avatar VARCHAR(255) DEFAULT 'default.png',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Cart Table
CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    session_id VARCHAR(255),
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    size VARCHAR(10),
    color VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Orders Table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    order_number VARCHAR(50) NOT NULL UNIQUE,
    status ENUM('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
    total_amount DECIMAL(10,2) NOT NULL,
    shipping_fee DECIMAL(10,2) DEFAULT 200.00,
    discount DECIMAL(10,2) DEFAULT 0.00,
    payment_method ENUM('cod','card','easypaisa','jazzcash') DEFAULT 'cod',
    payment_status ENUM('pending','paid','failed') DEFAULT 'pending',
    shipping_name VARCHAR(255),
    shipping_email VARCHAR(255),
    shipping_phone VARCHAR(20),
    shipping_address TEXT,
    shipping_city VARCHAR(100),
    shipping_country VARCHAR(100),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Order Items Table
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(255),
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    size VARCHAR(10),
    color VARCHAR(50),
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Contact Table
CREATE TABLE IF NOT EXISTS contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    subject VARCHAR(255),
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Wishlist Table
CREATE TABLE IF NOT EXISTS wishlist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Reviews Table
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    user_id INT,
    reviewer_name VARCHAR(100),
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- ============================================
-- SAMPLE DATA
-- ============================================

-- Insert Categories
INSERT INTO categories (name, slug, description, image) VALUES
('Women', 'women', 'Explore our exclusive women fashion collection', 'cat_women.jpg'),
('Men', 'men', 'Premium men fashion and accessories', 'cat_men.jpg'),
('Kids', 'kids', 'Adorable and trendy kids collection', 'cat_kids.jpg'),
('Accessories', 'accessories', 'Complete your look with our accessories', 'cat_accessories.jpg'),
('Sale', 'sale', 'Up to 70% off on selected items', 'cat_sale.jpg');

-- Insert Admin User (password: admin123)
INSERT INTO users (first_name, last_name, email, password, role) VALUES
('Admin', 'Velvet', 'admin@velvetvogue.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insert Sample Products
INSERT INTO products (category_id, name, slug, description, price, sale_price, stock, featured, trending, new_arrival, rating, reviews_count) VALUES
(1, 'Elegant Velvet Dress', 'elegant-velvet-dress', 'Luxurious deep purple velvet midi dress with cinched waist and flowing skirt. Perfect for evening occasions.', 8500.00, 6800.00, 25, 1, 1, 1, 4.8, 124),
(1, 'Silk Floral Maxi', 'silk-floral-maxi', 'Breathtaking silk maxi dress adorned with delicate floral prints. A timeless piece for any wardrobe.', 12000.00, NULL, 15, 1, 0, 1, 4.6, 89),
(1, 'Power Blazer Set', 'power-blazer-set', 'Sophisticated tailored blazer and trouser co-ord in classic black. Redefine your workwear.', 9500.00, 7500.00, 20, 0, 1, 1, 4.7, 67),
(1, 'Boho Wrap Skirt', 'boho-wrap-skirt', 'Flowy bohemian wrap skirt in earthy tones. Pairs perfectly with a simple crop top.', 3200.00, NULL, 40, 0, 1, 1, 4.4, 45),
(2, 'Classic Oxford Shirt', 'classic-oxford-shirt', 'Premium cotton Oxford shirt in crisp white. A wardrobe essential for the modern gentleman.', 4500.00, 3500.00, 35, 1, 0, 1, 4.5, 78),
(2, 'Slim Fit Chinos', 'slim-fit-chinos', 'Tailored slim fit chinos in versatile khaki. Dress up or down with ease.', 5500.00, NULL, 30, 0, 1, 0, 4.6, 56),
(2, 'Leather Biker Jacket', 'leather-biker-jacket', 'Genuine leather biker jacket with silver hardware. Make a statement wherever you go.', 18000.00, 14000.00, 10, 1, 1, 0, 4.9, 203),
(4, 'Gold Chain Necklace', 'gold-chain-necklace', 'Elegant 18k gold-plated chain necklace. The perfect finishing touch to any outfit.', 2800.00, NULL, 50, 1, 1, 1, 4.7, 91),
(4, 'Designer Sunglasses', 'designer-sunglasses', 'UV400 protection designer sunglasses with acetate frame. Chic and functional.', 3500.00, 2800.00, 25, 1, 0, 1, 4.5, 44),
(1, 'Linen Wide Leg Pants', 'linen-wide-leg-pants', 'Breathable linen wide leg trousers in sand beige. Perfect for summer days.', 4800.00, NULL, 28, 0, 1, 1, 4.3, 33),
(1, 'Cashmere Turtleneck', 'cashmere-turtleneck', 'Soft cashmere blend turtleneck in deep burgundy. Cozy luxury for winter.', 7200.00, 5800.00, 18, 1, 0, 0, 4.8, 112),
(2, 'Formal Suit', 'formal-suit', 'Two-piece formal suit in charcoal grey. Italian-inspired tailoring for the discerning man.', 22000.00, 18000.00, 8, 1, 1, 0, 4.9, 167);

-- Insert Sample Reviews
INSERT INTO reviews (product_id, reviewer_name, rating, comment) VALUES
(1, 'Aisha Khan', 5, 'Absolutely stunning dress! The quality is exceptional and the fit is perfect.'),
(1, 'Fatima Ali', 5, 'Got so many compliments at the wedding. Worth every penny!'),
(7, 'Hamza Sheikh', 5, 'Best leather jacket I have ever owned. Premium quality!'),
(8, 'Sara Ahmed', 4, 'Beautiful necklace, looks exactly like the picture.');

-- Insert Sample Contacts
INSERT INTO contacts (name, email, subject, message) VALUES 
('Test User', 'test@example.com', 'Product Inquiry', 'I would like to know more about your velvet dress collection.');
