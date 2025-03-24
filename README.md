## About CAF LAB

CAF LAB is a luxury e-commerce platform dedicated to providing the finest coffee products and accessories. Our commitment to quality, sustainability, and customer satisfaction drives everything we do. We source our coffee beans ethically and work directly with responsible farms across Central and South America.

## Our Team
Samin Ahmed (as svmmy-a)
...

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


