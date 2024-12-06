-- Insert into Category
INSERT INTO Category (name) VALUES
('Single Origin'),
('Accessories'),
('Coffee Capsules'),
('Instant Coffee'),
('Decaf Coffee');

-- Insert into Products
INSERT INTO Products (name, description, price, category_id, image_url, stock_level, size) VALUES
('Nicaraguan Coffee Beans', 'Single-origin Nicaraguan coffee beans.', 10.00, 1, 'N/A', 'in stock', 'Whole Beans'),
('Colombian Coffee Beans', 'Single-origin Colombian coffee beans.', 12.00, 1, 'N/A', 'in stock', 'Whole Beans'),
('Brazilian Coffee Beans', 'Single-origin Brazilian coffee beans.', 11.50, 1, 'N/A', 'in stock', 'Whole Beans'),
('Ethiopian Coffee Beans', 'Single-origin Ethiopian coffee beans.', 12.00, 1, 'N/A', 'in stock', 'Whole Beans'),
('Arabic Coffee Beans', 'Single-origin Arabic coffee beans.', 12.00, 1, 'N/A', 'in stock', 'Whole Beans'),

('Chocolate Coffee Pods', 'Delicious chocolate flavoured coffee pods.', 7.99, 2, 'N/A', 'in stock', 'Pods'),
('Toffee Nut Coffee Pods', 'Sweet toffee nut flavoured coffee pods.', 7.99, 2, 'N/A', 'in stock', 'Pods'),
('Rich Hazelnut Coffee Pods', 'Rich hazelnut flavoured coffee pods.', 7.99, 2, 'N/A', 'in stock', 'Pods'),
('Creamy Caramel Coffee Pods', 'Creamy caramel flavoured coffee pods.', 7.99, 2, 'N/A', 'in stock', 'Pods'),
('Vanilla Coffee Pods', 'Smooth vanilla flavoured coffee pods.', 7.99, 2, 'N/A', 'in stock', 'Pods'),



-- Insert into Users
INSERT INTO Users (name, email, password, role, phone_number, address_line, postcode) VALUES
('John Doe', 'john.doe@example.com', 'hashed_password_1', 'customer', '1234567890', '123 Street Name', '12345'),
('Jane Smith', 'jane.smith@example.com', 'hashed_password_2', 'admin', '0987654321', '456 Another St', '54321');

-- Insert into Subscription_Plans
INSERT INTO Subscription_Plans (name, description, price, frequency) VALUES
('Basic Plan', 'Access to standard products with monthly delivery', 20.00, 'Monthly'),
('Premium Plan', 'Includes premium products with weekly delivery', 50.00, 'Weekly');


-- Insert into Subscriptions
INSERT INTO Subscriptions (user_id, plan_id, product_id, quantity, start_date) VALUES
(1, 1, 1, 1, '2024-01-01'),
(2, 2, 2, 2, '2024-02-01');
