-- Insert into Category
INSERT INTO Category (name) VALUES
('Single Origin'),
('Accessories'),
('Coffee Capsules'),
('Instant Coffee'),
('Decaf Coffee');

-- Insert into Products
INSERT INTO Products (name, description, price, category_id, image_url, stock_level, size) VALUES
('Nicaraguan Bean', 'Origin Coffee Nicaraguan Bean', 10.00, 1, 'N/A', 'in stock', 'Large'),
('Colombian', 'Colombian Origin Bean', 12.00, 1, 'N/A', 'in stock', 'Large'),
('Brazilian', 'Brazilian Origin Bean', 11.50, 1, 'N/A', 'low stock', 'Large'),
('Chocolate', 'Chocolate Coffee Capsule', 8.00, 3, 'N/A', 'in stock', 'Standard'),
('Vanilla', 'Vanilla Coffee Capsule', 7.50, 3, 'N/A', 'in stock', 'Standard'),
('Salted Caramel', 'Salted Caramel Instant Coffee', 5.00, 4, 'N/A', 'in stock', 'Small'),
('Decaf Black', 'Black Decaf Coffee', 6.00, 5, 'N/A', 'in stock', 'Small');

-- Insert into Users
INSERT INTO Users (name, email, password, role, phone_number, address_line, postcode) VALUES
('John Doe', 'john.doe@example.com', 'hashed_password_1', 'customer', '1234567890', '123 Street Name', '12345'),
('Jane Smith', 'jane.smith@example.com', 'hashed_password_2', 'admin', '0987654321', '456 Another St', '54321');
