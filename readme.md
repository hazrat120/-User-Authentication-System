# PHP User Authentication System

A comprehensive PHP-based user authentication system implementing secure user management with email verification, password reset, and session handling using PDO for database operations.

## 🌟 Key Features

### User Management

- **Registration System**
  - Username and email validation
  - Password hashing using PHP's native `password_hash()`
  - Email verification token generation
  - Duplicate email check
- **Login System**

  - Secure session management
  - Email and password validation
  - Account verification check
  - Automatic dashboard redirection

- **Password Recovery**
  - Secure password reset workflow
  - Token-based verification
  - Email notification system
  - Time-based token expiration

### Security Implementation

- **Database Security**
  - PDO prepared statements
  - SQL injection prevention
  - Secure connection handling
- **Password Security**
  - Strong password hashing (PASSWORD_DEFAULT)
  - Secure token generation using `random_bytes()`
- **Session Security**
  - Session-based authentication
  - Protection against session hijacking
  - Secure logout mechanism

## 🛠️ Technical Stack

- **Backend**: PHP 7+
- **Database**: MySQL with PDO
- **Security**: Native PHP Security Functions
- **Server**: Apache/Nginx
- **Frontend**: HTML/CSS

## �� Project Structure
