<div style="text-align: center">

# PHP JWT Authorization Class
### Secure | Easy to Use | Session Management | Token Validation | IP Verification
###### Perfect for Managing Authentication and Authorization
</div>
<br />
<div style="text-align: center">

![PHP Version](https://img.shields.io/badge/php-7.4%20|%208.3-blue?style=for-the-badge&color=%388E3C)
![GitHub License](https://img.shields.io/github/license/samirkl/PHP-Authenticate-System?style=for-the-badge&color=%388E3C)
![Packagist Downloads](https://img.shields.io/packagist/dt/samirkl/PHP-Authenticate-System?style=for-the-badge&color=%388E3C)
![GitHub Repo stars](https://img.shields.io/github/stars/samirkl/PHP-Authenticate-System?style=for-the-badge&color=%388E3C)

</div>

<div style="text-align: center">

![GitHub commit activity](https://img.shields.io/github/commit-activity/t/samirkl/PHP-Authenticate-System?style=for-the-badge&color=%23303F9F)
![GitHub code size in bytes](https://img.shields.io/github/languages/code-size/samirkl/PHP-Authenticate-System?style=for-the-badge&color=%23303F9F)
![GitHub Discussions](https://img.shields.io/github/discussions/samirkl/PHP-Authenticate-System?style=for-the-badge&color=%23303F9F)
![Languages](https://img.shields.io/badge/01-languages?label=languages&style=for-the-badge&color=%23303F9F)

</div>

<br />
<br />

## Introduction

The **PHP JWT Authorization Class** provides a straightforward way to manage user authentication and authorization using JSON Web Tokens (JWT). This class is designed to handle token generation, validation, and user session management seamlessly, ensuring secure and efficient authentication for your application.

## Features
- JWT Encoding and Decoding: Securely encode and decode JWTs.
- User Authentication: Authenticate users and manage user sessions.
- Token Validation: Validate JWTs to ensure they haven't been tampered with.
- IP Verification: Ensure the IP address remains consistent during a session.
- Session Management: Manage user sessions with automatic logout and token renewal.

## Installation
1. Clone the repository:
```
git clone https://github.com/samirkl/PHP-Authenticate-System.git
```
2. Install dependencies:
```
composer require firebase/php-jwt
```
## Usage
Include the **Authorize** class in your project and use its methods to manage authentication.
## Generate Token
- To generate a JWT for a user:
```php
<?php
require 'Authorize.php';

// User information to protect
$userData = [
    'username' => 'bond',
    'password' => 'hashed_password',
    'name'     => 'James Bond',
    'phone'    => '123-456-7890',
    ...
];

// Authenticate user and set session
Authorize::auth($userData);
```
## Verify Token
- To verify the user's identity using a token:
```php
<?php
require 'Authorize.php';

try {
    $isApi = true; // Set to true if this is an API call
    $isAuthenticated = Authorize::verifyIdentity($isApi);

    if ($isAuthenticated) {
        // User is authenticated
        echo 'User is authenticated';
    } else {
        // Authentication failed
        echo 'Authentication failed';
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}

```
## Log Out
- To log out the user:
```php
<?php
require 'Authorize.php';

$isLoggedOut = Authorize::logOut();

if ($isLoggedOut) {
    echo 'User logged out successfully';
} else {
    echo 'User was not logged in';
}

```
---
## Methods
### Authorize::hash($value)
Encodes the given value into a JWT.
- Parameters: `string|array $value` - The data to encode.
- Returns: `string` - The encoded JWT
### Authorize::unHash($value)
Decodes the given JWT.
- Parameters: `string $value` - The encoded JWT.
- Returns: `string|false` - The decoded data as a JSON string or false on failure.
- Throws: `JsonException`
### Authorize::auth(array|bool $protectedData = false)
Authenticates the user and sets the session data.
- Parameters: `array|bool $protectedData` - User data to protect (e.g., username, password).
### Authorize::verifyIdentity(bool $isApi = false)
Verifies the user's identity using the stored token.
- Parameters: `bool $isApi` - If true, updates the token after authentication.
- Returns: `bool` - True if authentication is successful, false otherwise.
- Throws: `Exception`
### Authorize::getIPAddress()
Gets the user's IP address.
- Returns: `string` - The user's IP address.
### Authorize::logOut()
Logs out the user by clearing the session and cookie.
- Returns: `bool` - True if the user was logged out, false if the user was not logged in.
### Authorize::validateToken($token)
Validates the given token.
- Parameters: `string $token` - The JWT to validate.
- Returns: `array|null` - The decoded token data as an array or null on failure.
- Throws: `JsonException`
### Authorize::isValidToken($tokenData)
Checks if the token data is valid.
- Parameters: `array $tokenData ` - The decoded token data.
- Returns: `bool` - True if the token is valid, false otherwise.
### License
This project is licensed under the MIT License. See the LICENSE file for details.
### Contributing
Contributions are welcome! Please feel free to submit a Pull Request.
### Acknowledgements
- [Firebase JWT PHP](https://github.com/firebase/php-jwt) for the JWT handling.
