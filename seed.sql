-- Insert into Category
INSERT INTO Category (name) VALUES
('Single Origin'),
('Accessories'),
('Coffee Capsules'),
('Instant Coffee'),
('Decaf Coffee');

-- Insert into Products
INSERT INTO Products (name, description, price, category_id, image_url, stock_level, size) VALUES
('Nicaraguan Coffee Beans', 'Single-origin Nicaraguan coffee beans.', 10.00, 1, 'Nicaraguan_Coffee.png', 'in stock', 'Whole Beans'),
('Colombian Coffee Beans', 'Single-origin Colombian coffee beans.', 12.00, 1, 'Colombian_Coffee.png', 'in stock', 'Whole Beans'),
('Brazilian Coffee Beans', 'Single-origin Brazilian coffee beans.', 11.50, 1, 'Brazagian_coffee.png', 'in stock', 'Whole Beans'),
('Ethiopian Coffee Beans', 'Single-origin Ethiopian coffee beans.', 12.00, 1, 'Ethiopian_coffee.png', 'in stock', 'Whole Beans'),
('Arabic Coffee Beans', 'Single-origin Arabic coffee beans.', 12.00, 1, 'Arabic_coffee.png', 'in stock', 'Whole Beans'),
('Chocolate Coffee Pods', 'Delicious chocolate flavoured coffee pods.', 7.99, 2, 'chocolatepods.png', 'in stock', 'Pods'),
('Toffee Nut Coffee Pods', 'Sweet toffee nut flavoured coffee pods.', 7.99, 2, 'toffee_pods.png', 'in stock', 'Pods'),
('Rich Hazelnut Coffee Pods', 'Rich hazelnut flavoured coffee pods.', 7.99, 2, 'Hazelenut_pods.png', 'in stock', 'Pods'),
('Creamy Caramel Coffee Pods', 'Creamy caramel flavoured coffee pods.', 7.99, 2, 'creamy_caramel_pods.png', 'in stock', 'Pods'),
('Vanilla Coffee Pods', 'Smooth vanilla flavoured coffee pods.', 7.99, 2, 'Coffee_pods.png', 'in stock', 'Pods'),
('Black Cold Brew', 'Ready-made black cold brew coffee.', 8.99, 3, 'Black_coldbrew.png', 'in stock', 'Cold Brew'),
('Nitro Cold Brew', 'Nitro-infused cold brew coffee.', 8.99, 3, 'nitro_coldbrew.png', 'in stock', 'Cold Brew'),
('Berry Infused Cold Brew', 'Cold brew coffee infused with berry flavors.', 8.99, 3, 'berry_coldbrew.png', 'in stock', 'Cold Brew'),
('Oat Milk Cold Brew', 'Cold brew coffee with oat milk.', 8.99, 3, 'oatmilk_coldbrew.png', 'in stock', 'Cold Brew'),
('Mocha Cold Brew', 'Mocha flavored cold brew coffee.', 8.99, 3, 'Mocha_coldbrew.png', 'in stock', 'Cold Brew'),
('Black Instant Coffee', 'Instant black coffee.', 6.99, 4, 'N/A', 'in stock', 'Instant Coffee'),
('Americano Instant Coffee', 'Instant Americano coffee.', 6.99, 4, 'N/A', 'in stock', 'Instant Coffee'),
('Dalgona Instant Coffee', 'Instant Dalgona coffee mix.', 6.99, 4, 'N/A', 'in stock', 'Instant Coffee'),
('Spiced Instant Coffee', 'Instant coffee with spices.', 6.99, 4, 'N/A', 'in stock', 'Instant Coffee'),
('Salted Caramel Instant Coffee', 'Instant salted caramel coffee.', 6.99, 4, 'N/A', 'in stock', 'Instant Coffee'),
('Decaf Black Coffee', 'Decaffeinated black coffee.', 8.99, 5, 'N/A', 'in stock', 'Decaf'),
('Decaf Latte', 'Decaffeinated latte coffee.', 8.99, 5, 'N/A', 'in stock', 'Decaf'),
('Decaf Caramel Coffee', 'Decaffeinated caramel flavored coffee.', 8.99, 5, 'N/A', 'in stock', 'Decaf'),
('Decaf Hazelnut Coffee', 'Decaffeinated hazelnut flavored coffee.', 8.99, 5, 'N/A', 'in stock', 'Decaf'),
('Decaf Mocha', 'Decaffeinated mocha flavored coffee.', 8.99, 5, 'N/A', 'in stock', 'Decaf');

-- Insert into Users
INSERT INTO Users (name, email, password, role, phone_number, address_line, postcode) VALUES
('John Doe', 'john.doe@example.com', 'hashed_password_1', 'customer', '1234567890', '123 Street Name', '12345'),
('Jane Smith', 'jane.smith@example.com', 'hashed_password_2', 'admin', '0987654321', '456 Another St', '54321');

-- Insert into Subscription_Plans
INSERT INTO Subscription_Plans (name, type, description, price) VALUES
('Basic Plan', 'whole-bean', 'Access to standard products with monthly delivery', 20.00),
('Premium Plan', 'ground', 'Includes premium products with weekly delivery', 50.00);

