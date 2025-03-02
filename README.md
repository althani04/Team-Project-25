# CAF LAB Coffee Company

Welcome to CAF LAB, your premier destination for artisanal coffee experiences. We offer a curated selection of premium coffee products, from single-origin beans to specialty coffee capsules, delivering exceptional quality and taste to coffee enthusiasts.

## About CAF LAB

CAF LAB is a luxury e-commerce platform dedicated to providing the finest coffee products and accessories. Our commitment to quality, sustainability, and customer satisfaction drives everything we do. We source our coffee beans ethically and work directly with responsible farms across Central and South America.

## Current Functionality

The full list of functionality includes:

- **Product Browsing & Shopping**
  - Browse products by categories
  - Detailed product pages with descriptions and pricing
  - Advanced filtering and search capabilities
  - Shopping cart management

- **User Account Features**
  - User registration and authentication
  - Profile management
  - Order history tracking
  - Return request system

- **Checkout Process**
  - Secure payment processing
  - Multiple payment options
  - Order confirmation and tracking

- **Admin Dashboard**
  - Inventory management
  - Order processing
  - Customer management
  - Return request handling
  - Sales analytics

- **Additional Features**
  - Blog section with coffee guides
  - Contact and support system
  - Newsletter subscription
  - Dark/Light mode theme toggle

## Running Locally

### Prerequisites

- XAMPP (latest version)
- Web browser (Chrome, Firefox, Safari, or Edge)
- MySQL/MariaDB
- PHP 7.4 or higher

### Installation Steps

1. **Install XAMPP**
   - Download and install XAMPP from [https://www.apachefriends.org/](https://www.apachefriends.org/)
   - Ensure Apache and MySQL services are installed

2. **Clone the Repository**
   ```bash
   cd /Applications/XAMPP/xamppfiles/htdocs/
   git clone [repository-url]
   cd Team-Project-255
   ```

3. **Database Setup**
   - Open phpMyAdmin (http://localhost/phpmyadmin)
   - Create a new database named 'caf_lab'
   - Import the database schema:
     - Navigate to the 'Import' tab
     - Select the 'schema.sql' file from the project root
     - Click 'Go' to import the schema
   - Import initial data:
     - Select the 'seed.sql' file
     - Click 'Go' to import the data

4. **Configure Database Connection**
   - Navigate to `CaflabProject/config/`
   - Update database.php with your credentials if different from defaults:
     ```php
     $host = 'localhost';
     $db = 'caf_lab';
     $user = 'root';
     $pass = '';
     ```

5. **Start Services**
   - Start XAMPP Control Panel
   - Start Apache and MySQL services
   - Access the website at: http://localhost/Team-Project-255/CaflabProject/public/home.php

### File Structure

```
Team-Project-255/
├── admin/                 # Admin dashboard files
├── assets/               # Static assets (images, etc.)
├── CaflabProject/        # Main application files
│   ├── api/             # API endpoints
│   ├── config/          # Configuration files
│   └── public/          # Public-facing files
├── schema.sql           # Database schema
└── seed.sql             # Initial data
```

## Features in Detail

### User Features

1. **Product Browsing**
   - Category-based navigation
   - Advanced filtering options
   - Search functionality
   - Detailed product information

2. **Account Management**
   - Personal information management
   - Order history
   - Return requests
   - Password management

3. **Shopping Experience**
   - Add/remove items from cart
   - Update quantities
   - Save items for later
   - Secure checkout process

### Admin Features

1. **Inventory Management**
   - Stock level tracking
   - Product updates
   - Category management
   - Price adjustments

2. **Order Processing**
   - Order status updates
   - Customer communication
   - Return processing
   - Refund management

3. **Analytics**
   - Sales reports
   - Customer insights
   - Inventory analytics
   - Return statistics

## Security Implementation

Our application implements comprehensive security measures to protect user data and ensure safe transactions:

### Authentication & Authorization
- Secure password hashing using PHP's password_hash()
- Session-based authentication with secure session management
- Role-based access control (Admin/Customer)
- Automatic session timeout for security

### Data Protection
- Prepared statements for all database queries
- Input validation and sanitization
- XSS (Cross-Site Scripting) protection
- CSRF (Cross-Site Request Forgery) protection
- SQL injection prevention
- Secure password reset mechanism

### System Security
- HTTPS enforcement
- Secure cookie handling
- Rate limiting on login attempts
- Error logging with sensitive data masking
- Regular security audits and updates

## Troubleshooting

Common issues and solutions:

1. **Database Connection Issues**
   - Verify XAMPP services are running
   - Check database credentials
   - Ensure proper file permissions

2. **Missing Images**
   - Verify image paths in database
   - Check file permissions
   - Ensure images are in correct directory

3. **Session Issues**
   - Clear browser cache
   - Check PHP session configuration
   - Verify cookie settings

## Technologies Used

The project is built using modern web technologies and follows industry best practices:

### Frontend
- HTML5, CSS3, JavaScript
- Bootstrap for responsive design
- Font Awesome for icons
- AOS (Animate On Scroll) library
- SweetAlert2 for notifications

### Backend
- PHP 7.4+
- MySQL/MariaDB database
- PDO for database operations
- Session-based authentication
- RESTful API principles

### Development Tools
- XAMPP for local development
- Git for version control
- Visual Studio Code
- phpMyAdmin for database management

## The Team

CAF LAB is being developed by Team 25 for the Team Project module at Aston University. Our team consists of:

### Core Development
- **Frontend Development**
  - Home page and product catalog implementation
  - User interface design and responsiveness
  - Shopping cart and checkout functionality
  - Account management features

- **Backend Development**
  - Database architecture and management
  - User authentication system
  - Order processing system
  - Return management system
  - Admin dashboard implementation

### Additional Features
- Blog section with coffee guides
- Newsletter subscription system
- Contact form with admin response system
- Dark/Light mode implementation

## Project Status & Roadmap

### Current Status
- Core e-commerce functionality implemented
- User authentication system complete
- Product catalog and shopping cart operational
- Admin dashboard with basic features
- Initial security measures in place

### Upcoming Features
- Enhanced inventory management system
- Advanced analytics dashboard
- Improved return processing system
- Dark/Light mode implementation
- Performance optimizations
- Additional security enhancements

### Future Plans
- Mobile application development
- Integration with additional payment gateways
- Advanced product recommendation system
- Customer loyalty program
- International shipping support

## License & Attribution

This project is developed as part of the Team Project module at Aston University. All rights reserved. The code and assets are for educational purposes and may not be used commercially without permission.

---

© 2024 Team 25, Aston University. Developed for CAF LAB Coffee Company.
