-- Insert into Category
INSERT INTO Category (name) VALUES
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
('Decaf Black Coffee', 'Decaffeinated black coffee.', 8.99, 3, 'Decaf_Black.png', 'in stock', '100g'),
('Decaf Latte', 'Decaffeinated latte coffee.', 8.99, 3, 'Decaf_Caramel.png', 'in stock', '100g'),
('Decaf Caramel Coffee', 'Decaffeinated caramel flavored coffee.', 8.99, 3, 'Decaf_Caramel.png', 'in stock', '100g'),
('Decaf Hazelnut Coffee', 'Decaffeinated hazelnut flavored coffee.', 8.99, 3, 'Decaf_Hazelnut.png', 'in stock', '100g'),
('Decaf Mocha Coffee', 'Decaffeinated mocha flavored coffee.', 8.99, 3, 'Decaf_Mocha.png', 'in stock', '100g'),
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
INSERT INTO Users (name, email, password, role) VALUES
('Admin User', 'admin@caflab.com', '$2y$12$epgQ01VfS7lPnTwvuOJySO/YFIVTg.FuKLyzpptPnTXqnu5/.A2Um', 'admin');

-- Insert into Subscription_Plans
INSERT INTO Subscription_Plans (name, type, description, price, image_url) VALUES
('Discovery', 'whole-bean', 'Discover our whole range of coffees from around the world', 15.35, 'images/plan_1.jpg'),
('Bolder', 'ground', 'Our dark and medium roasted blends and single origins', 12.15, 'images/plan_2.jpg'),
('Seasonal', 'ground', 'Will receive exclusive festive flavours every season', 17.65, 'images/plan_3.jpg');


-- Insert into Inventory for each product (default quantity 10)
INSERT INTO Inventory (product_id, size, quantity, low_stock_threshold, units)
SELECT product_id, size, 10, 5, 10 FROM Products;
