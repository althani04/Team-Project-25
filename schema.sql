-- Active: 1733344538931@@127.0.0.1@3306@caf_lab
DROP DATABASE IF EXISTS caf_lab;
CREATE DATABASE caf_lab;
USE caf_lab;

-- Table: Users
CREATE TABLE Users (
    user_id INT NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'customer') NOT NULL,
    first_login BOOLEAN DEFAULT TRUE,
    phone_number VARCHAR(15),
    address_line VARCHAR(255),
    postcode VARCHAR(10),
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id)
);

-- Table: Category
CREATE TABLE Category (
    category_id INT NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    PRIMARY KEY (category_id)
);

-- Table: Products
CREATE TABLE Products (
    product_id INT NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    category_id INT NOT NULL,
    image_url TEXT,
    stock_level ENUM('in stock', 'low stock', 'out of stock') NOT NULL,
    size VARCHAR(50),
    PRIMARY KEY (product_id),
    FOREIGN KEY (category_id) REFERENCES Category(category_id) ON DELETE CASCADE
);


-- Table: Orders
CREATE TABLE Orders (
    order_id INT NOT NULL AUTO_INCREMENT,
    user_id INT NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total_price DECIMAL(10, 2) NOT NULL,
    status ENUM('processing', 'shipped', 'completed', 'cancelled', 'return_pending') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (order_id),
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE
);

-- Table: Order_Items
CREATE TABLE Order_Items (
    order_item_id INT NOT NULL AUTO_INCREMENT,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    PRIMARY KEY (order_item_id),
    FOREIGN KEY (order_id) REFERENCES Orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES Products(product_id) ON DELETE CASCADE
);

-- Table: Reviews
CREATE TABLE Reviews (
    review_id INT NOT NULL AUTO_INCREMENT,
    user_id INT NOT NULL,
    product_id INT,
    order_id INT NOT NULL,
    review_type ENUM('product', 'service') NOT NULL,
    rating INT NULL CHECK (rating BETWEEN 1 AND 5),
    review_text TEXT,
    admin_response TEXT,
    response_date TIMESTAMP NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (review_id),
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES Products(product_id) ON DELETE CASCADE,
    FOREIGN KEY (order_id) REFERENCES Orders(order_id) ON DELETE CASCADE,
    website_usability_rating INT NULL CHECK (website_usability_rating BETWEEN 1 AND 5),
    delivery_service_rating INT NULL CHECK (delivery_service_rating BETWEEN 1 AND 5),
    customer_support_rating INT NULL CHECK (customer_support_rating BETWEEN 1 AND 5),
    overall_website_service_rating INT NULL CHECK (overall_website_service_rating BETWEEN 1 AND 5)
);
ALTER TABLE Reviews ADD CONSTRAINT valid_review_type CHECK (
    (review_type = 'product' AND product_id IS NOT NULL) OR
    (review_type = 'service' AND product_id IS NULL)
);
ALTER TABLE Reviews ADD CONSTRAINT service_ratings_required CHECK (
    (review_type = 'product') OR
    (review_type = 'service' AND (
        website_usability_rating IS NOT NULL OR
        delivery_service_rating IS NOT NULL OR
        customer_support_rating IS NOT NULL OR
        overall_website_service_rating IS NOT NULL
    ))
);

-- -- Create index for faster review lookups
-- CREATE INDEX idx_reviews_product ON Reviews(product_id) WHERE product_id IS NOT NULL;
-- CREATE INDEX idx_reviews_order ON Reviews(order_id);

-- Table: Subscription_Plans
CREATE TABLE Subscription_Plans (
    plan_id INT NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    type ENUM('whole-bean', 'ground') NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    PRIMARY KEY (plan_id)
);
-- Table: Subscriptions
CREATE TABLE Subscriptions (
    subscription_id INT NOT NULL AUTO_INCREMENT,
    user_id INT NOT NULL,
    plan_id INT NOT NULL,
    quantity INT NOT NULL,
    frequency ENUM('2 weeks', 'Month') NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE DEFAULT NULL,
    payment_plan ENUM('monthly', 'annually') NOT NULL,
    PRIMARY KEY (subscription_id),
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (plan_id) REFERENCES Subscription_Plans(plan_id) ON DELETE CASCADE
);

-- Table: Inventory Logs (Corrected)
CREATE TABLE Inventory_Logs (
    log_id INT NOT NULL AUTO_INCREMENT,
    product_id INT NOT NULL,
    action ENUM('add stock', 'remove stock') NOT NULL,
    quantity INT NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (log_id)
);

CREATE TABLE Contact_Messages (
    message_id INT NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    company VARCHAR(255),
    subject VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('new', 'responded', 'resolved') NOT NULL DEFAULT 'new',
    admin_response TEXT,
    response_date TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (message_id)
);

-- Table: Inventory
CREATE TABLE Inventory (
    inventory_id INT NOT NULL AUTO_INCREMENT,
    product_id INT NOT NULL,
    size VARCHAR(50),
    units INT NOT NULL DEFAULT 0,
    quantity INT NOT NULL DEFAULT 0,
    low_stock_threshold INT NOT NULL DEFAULT 5,
    last_restock_date TIMESTAMP,
    last_restock_quantity INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (inventory_id),
    FOREIGN KEY (product_id) REFERENCES Products(product_id) ON DELETE CASCADE,
    UNIQUE KEY unique_product_size (product_id, size)
);

-- Table: Inventory_Transactions
CREATE TABLE Inventory_Transactions (
    transaction_id INT NOT NULL AUTO_INCREMENT,
    inventory_id INT NOT NULL,
    type ENUM('restock', 'sale', 'adjustment', 'return') NOT NULL,
    quantity INT NOT NULL,
    previous_quantity INT NOT NULL,
    new_quantity INT NOT NULL,
    reference_id VARCHAR(255),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INT NOT NULL,
    PRIMARY KEY (transaction_id),
    FOREIGN KEY (inventory_id) REFERENCES Inventory(inventory_id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES Users(user_id)
);

-- Create Returns Table
CREATE TABLE Returns (
    return_id INT NOT NULL AUTO_INCREMENT,
    order_id INT NOT NULL,
    user_id INT NOT NULL,
    reason VARCHAR(255) NOT NULL,
    comments TEXT,
    status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
    admin_response TEXT,
    email_sent BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (return_id),
    FOREIGN KEY (order_id) REFERENCES Orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE
);

-- Create Return_Items Table
CREATE TABLE Return_Items (
    return_item_id INT NOT NULL AUTO_INCREMENT,
    return_id INT NOT NULL,
    order_item_id INT NOT NULL,
    quantity INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (return_item_id),
    FOREIGN KEY (return_id) REFERENCES Returns(return_id) ON DELETE CASCADE,
    FOREIGN KEY (order_item_id) REFERENCES Order_Items(order_item_id) ON DELETE CASCADE
);

