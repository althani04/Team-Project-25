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

