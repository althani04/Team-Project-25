# Caf Lab - Specitialty Coffee e-commerce subscription platform

Caf Lab, a comprehensive subscription based e-commerce platform, aimed at providing premium coffee blends on a regular basis. The platform provides a seamless shopping experience for customers while offering robust inventory management capabilities for administrators.

## Features

### Customer Features
- User registration and authentication
- Product browsing with search capabilities
- Shopping basket functionality
- Product reviews and ratings
- Contact form for customer support
- Personal order history
- Password change functionality

### Admin Features
- Inventory management system
- Order processing and tracking
- Customer management
- Stock level monitoring with automatic alerts
- Real-time inventory reports
- Order fulfillment processing
- Product management (add, edit, remove)

## Project Structure
---
© 2024 CAF LAB Coffee Company. All Rights Reserved. Ecommerce software by Team Project 25/
├── assets/
│   ├── css/
│   │   └── style.css
│   ├── js/
│   │   └── main.js
│   └── images/
│       └── products/
├── config/
│   └── database.php
├── includes/
│   ├── header.php
│   ├── footer.php
│   └── nav.php
├── admin/
│   ├── dashboard.php
│   ├── inventory.php
│   ├── orders.php
│   └── customers.php
├── pages/
│   ├── home.php
│   ├── about.php
│   ├── contact.php
│   ├── products.php
│   ├── cart.php
│   ├── returns.php
│   ├── orders.php
│   ├── checkout.php
│   └── terms.php
├── auth/
│   ├── login.php
│   ├── signup.php
│   ├── logout.php
│   └── forgotpassword.php
├── database/
│   ├── schema.sql
│   └── seed.sql
├── README.md
└── index.php

## Technologies Used
- Frontend: HTML5, CSS3, JavaScript
- Backend: PHP
- Database: MySQL
- Additional Libraries: AOS (Animate On Scroll)

## Requirements
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)

## Installation

1. Clone the repository:
```bash
git clone https://github.com/althani04/Team-Project-25
```

2. Configure your web server to point to the project directory

3. Create the database:
```bash
mysql -u root -p < database/schema.sql
mysql -u root -p < database/seed.sql
```

4. Update database configuration in `config/database.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'caf_lab');
```

5. Ensure the `assets/images/products` directory has write permissions for product image uploads

## Usage

### Customer Access
- Visit the homepage to browse products
- Create an account or log in to place orders
- Use the search functionality to find specific products
- Add items to cart and proceed to checkout

### Admin Access
- Log in with admin credentials
- Access the admin dashboard at `/admin/dashboard.php`
- Manage inventory, orders, and customers
- Monitor stock levels and process orders

## Security Features
- Password hashing
- SQL injection prevention
- XSS protection
- CSRF protection
- Secure session management

## Contributing
This project is part of a team project under the Aston University and is not open for external contributions.

## Team
- Team Project 25

## License
All rights reserved. This project is proprietary and confidential.

---
© 2024 CAF LAB Coffee Company. All Rights Reserved. Ecommerce software by Team Project 25/
├── assets/
│   ├── css/
│   │   └── style.css
│   ├── js/
│   │   └── main.js
│   └── images/
│       └── products/
├── config/
│   └── database.php
├── includes/
│   ├── header.php
│   ├── footer.php
│   └── nav.php
├── admin/
│   ├── dashboard.php
│   ├── inventory.php
│   ├── orders.php
│   └── customers.php
├── pages/
│   ├── home.php
│   ├── about.php
│   ├── contact.php
│   ├── products.php
│   ├── cart.php
│   ├── returns.php
│   ├── orders.php
│   ├── checkout.php
│   └── terms.php
├── auth/
│   ├── login.php
│   ├── signup.php
│   ├── logout.php
│   └── forgotpassword.php
├── database/
│   ├── schema.sql
│   └── seed.sql
├── README.md
└── index.php
