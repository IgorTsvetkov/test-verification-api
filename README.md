# Requirements
- PHP 8.1 or higher
# Configure the project
1. Clone the project from the repository
2. Run `composer install` to install the dependencies
3. Run `php artisan key:generate` to generate the application key
4. Copy the `.env.example` file to `.env`
5. Create a new mysql database and update the next variables in `.env` using your database credentials:
    ```
    DB_DATABASE=your_database
    DB_USERNAME=your_username
    DB_PASSWORD=your_password
    ```
6. Run `php artisan migrate --seed` to create the database tables
7. Done! You can now run the project using `php artisan serve`
8. To run the tests, use `php artisan test` (should show green otherwise there is an issue in your configuration, please check the steps above)
---
## API Documentation
The API documentation is available at `/docs` route. You can access it by running the project `php artisan serve` and visiting `http://127.0.0.1:8000/docs` (the host name may different on your machine).
Please use the next token in the documentation to call endpoint (basic auth):
`Basic dGVzdEBleGFtcGxlLmNvbToxMjM0NTY=`
Note: if you can't call the docs please verify that the host in the browser and APP_URL in .env matches:
```
APP_URL=http://127.0.0.1:8000
```  
If you updated the above variable please stop existing execution and use the next commands:
1. `php artisan scribe:generate`
2. `php artisan serve`
[How to call api/files/verification](how-to-call-endpoint.png)

# Structure

This document provides an overview of the file verification logic implemented in the `FileVerificationController`.

## Route: 
POST `api/files/verification` 

## Overview

The file verification logic is designed to process uploaded files, verify their contents, and store the verification results in the database. The main components involved in this process are:

1. **FileVerificationController**: Handles the file upload and verification logic.
    - **Input**: HTTP request containing the uploaded file.
    - **Output**: JSON response with the verification result and issuer name.
2. **VerifyFile**: Action class that responsible for validating the uploaded file.
It uses pipe pattern to validate the file with 3 following pipes:
    - **ValidateIssuer**: Validate the issuer of the file.
    - **ValidateRecipient**: Validate the recipient of the file.
    - **ValidateSignature**: Validate the signature of the file.
 This method been chosen to separate the file validation logic into smaller pieces and make it easier to test and maintain. Also it allows to add new verification step by adding new pipe.
3. **VerificationRepository**: Store validation result into `Verification` model.
4. **FileVerificationControllerTest**: Contains test cases to ensure the file verification logic works as expected.

### Packages used:
--
[knuckleswtf/scribe] (https://scribe.knuckles.wtf/laravel/) - Generate OPEN API documentation from Laravel code
