-- Insert into Category
INSERT INTO Category (name) VALUES
INSERT INTO Categories (id, name) VALUES
('Single Origin'),
('Coffee Capsules'),
('Instant Coffee'),
('Decaf Coffee'),
('Cold Brew'),
('Accessories');

-- Insert into Products
INSERT INTO Products (name, description, price, category_id, image_url, stock_level, size) VALUES
('Nicaraguan Coffee Beans', 'Single-origin Nicaraguan coffee beans.', 41.99, 1, 'Nicaraguan_Coffee.png', 'in stock', '350g'),
('Colombian Coffee Beans', 'Single-origin Colombian coffee beans.', 22.50, 1, 'Colombian_Coffee.png', 'in stock', '250g'),
('Brazilian Coffee Beans', 'Single-origin Brazilian coffee beans.', 27.99, 1, 'Brazagian_coffee.png', 'in stock', '350g'),
('Ethiopian Coffee Beans', 'Single-origin Ethiopian coffee beans.', 18.50, 1, 'Ethiopian_coffee.png', 'in stock', '250g'),
('Arabic Coffee Beans', 'Single-origin Arabic coffee beans.', 30.00, 1, 'Arabic_coffee.png', 'in stock', '350g'),
('Chocolate Coffee Pods', 'Delicious chocolate flavoured coffee pods.', 7.99, 2, 'chocolatepods.png', 'in stock', '12 pods'),
('Toffee Nut Coffee Pods', 'Sweet toffee nut flavoured coffee pods.', 7.99, 2, 'toffee_pods.png', 'in stock', '12 pods'),
('Rich Hazelnut Coffee Pods', 'Rich hazelnut flavoured coffee pods.', 7.99, 2, 'Hazelenut_pods.png', 'in stock', '12 pods'),
('Creamy Caramel Coffee Pods', 'Creamy caramel flavoured coffee pods.', 7.99, 2, 'creamy_caramel_pods.png', 'in stock', '12 pods'),
('Vanilla Coffee Pods', 'Smooth vanilla flavoured coffee pods.', 7.99, 2, 'Coffee_pods.png', 'in stock', '12 pods'),
('Black Cold Brew', 'Ready-made black cold brew coffee.', 8.99, 5, 'Black_coldbrew.png', 'in stock', '330ml'),
('Nitro Cold Brew', 'Nitro-infused cold brew coffee.', 8.99, 5, 'nitro_coldbrew.png', 'in stock', '330ml'),
('Berry Infused Cold Brew', 'Cold brew coffee infused with berry flavors.', 8.99, 5, 'berry_coldbrew.png', 'in stock', '330ml'),
('Oat Milk Cold Brew', 'Cold brew coffee with oat milk.', 8.99, 5, 'oatmilk_coldbrew.png', 'in stock', '330ml'),
('Mocha Cold Brew', 'Mocha flavored cold brew coffee.', 8.99, 5, 'Mocha_coldbrew.png', 'in stock', '330ml'),
('Black Instant Coffee', 'Instant black coffee.', 6.99, 4, 'Black_Coffee.png', 'in stock', '100g'),
('Americano Instant Coffee', 'Instant Americano coffee.', 6.99, 4, 'Americano_Coffee.png', 'in stock', '100g'),
('Dalgona Instant Coffee', 'Instant Dalgona coffee mix.', 6.99, 4, 'Dalgona_Coffee.png', 'in stock', '100g'),
('Spiced Instant Coffee', 'Instant coffee with spices.', 6.99, 4, 'Spiced_Coffee.png', 'in stock', '100g'),
('Salted Caramel Instant Coffee', 'Instant salted caramel coffee.', 6.99, 4, 'Salted_Caramel_Coffee.png', 'in stock', '100g'),
('Decaf Black Coffee', 'Decaffeinated black coffee.', 8.99, 5, 'Decaf_Black.png', 'in stock', '100g'),
('Decaf Latte', 'Decaffeinated latte coffee.', 8.99, 5, 'Decaf_Caramel.png', 'in stock', '100g'),
('Decaf Caramel Coffee', 'Decaffeinated caramel flavored coffee.', 8.99, 5, 'Decaf_Caramel.png', 'in stock', '100g'),
('Decaf Hazelnut Coffee', 'Decaffeinated hazelnut flavored coffee.', 8.99, 5, 'Decaf_Hazelnut.png', 'in stock', '100g'),
('Decaf Mocha Coffee', 'Decaffeinated mocha flavored coffee.', 8.99, 5, 'Decaf_Mocha.png', 'in stock', '100g');
('Classic Ceramic Mug', 'Durable white ceramic mug for everyday coffee enjoyment.', 6.99, 6, 'ceramic_mug.png', 'in stock', '350ml'),
('Glass Double-Wall Mug', 'Heat-resistant double-wall glass mug for a stylish brew.', 9.99, 6, 'double_wall_mug.png', 'in stock', '300ml'),
('Travel Mug with Lid', 'Insulated stainless steel mug with spill-proof lid.', 12.99, 6, 'travel_mug.png', 'in stock', '400ml'),
('Branded Caf Lab Mug', 'Signature Caf Lab ceramic mug with logo.', 7.99, 6, 'ceramic_mug.png', 'in stock', '350ml'),
('Hario V60 Dripper', 'Iconic pour-over coffee maker for precise brewing.', 18.99, 6, 'v60_dripper.png', 'in stock', '1-2 cups'),
('Pack of 5 Coffee Filters', 'High-quality paper filters for V60 or pour-over brewing.', 3.99, 6, 'coffee_filters.png', 'in stock', '5 pcs'),
('Traditional Arabic Dallah', 'Elegant Arabic coffee pot (dallah) for serving Qahwa.', 29.99, 6, 'arabic_dallah.png', 'in stock', '600ml'),
('Insulated Thermos Flask', 'Keeps your coffee hot or cold for hours on the go.', 15.99, 6, 'thermos_flask.png', 'in stock', '750ml'),
('Electric Milk Frother', 'Handheld frother for cappuccinos, lattes and more.', 14.99, 6, 'milk_frother.png', 'in stock', '1 unit'),
('Coffee Capsule Machine', 'Compact and modern machine for coffee pods.', 59.99, 6, 'capsule_machine.png', 'in stock', '1 unit');

-- Insert into Users
INSERT INTO Users (name, email, password, role, phone_number, address_line, postcode) VALUES
('John Doe', 'john.doe@example.com', 'hashed_password_1', 'customer', '1234567890', '123 Street Name', '12345'),
('Jane Smith', 'jane.smith@example.com', 'hashed_password_2', 'admin', '0987654321', '456 Another St', '54321');


-- Insert into Subscription_Plans
INSERT INTO Subscription_Plans (name, type, description, price) VALUES
('Basic Plan', 'whole-bean', 'Access to standard products with monthly delivery', 20.00),
('Premium Plan', 'ground', 'Includes premium products with weekly delivery', 50.00);

-- Insert into Subscriptions
INSERT INTO Subscriptions (user_id, plan_id, product_id, quantity, start_date) VALUES
(1, 1, 1, 1, '2024-01-01'),
(2, 2, 2, 2, '2024-02-01');