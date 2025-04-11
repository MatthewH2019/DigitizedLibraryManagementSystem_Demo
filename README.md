Digitized Library Management System
A PHP-based RESTful API and frontend for managing books and authors.

Prerequisites
PHP: Version 7.4 or higher

Composer

PostgreSQL (or another supported PDO driver)

Environment File: A .env file in the project root containing:

HOST=your_database_host
DBNAME=your_database_name
USERNAME=your_db_username
PASSWORD=your_db_password
PORT=5432

Verifying Your Environment
Check PostgreSQL installation:
psql --version

Check PHP PDO and PostgreSQL drivers:
Create a temporary info.php file containing:

<?php phpinfo(); ?>
Then start PHP’s built-in server:
php -S localhost:8000

Visit http://localhost:8000/info.php in your browser and verify that you see sections for PDO and PDO_pgsql.

Installation
Clone the repository:
git clone https://github.com/YourOrg/DigitizedLibraryManagementSystem_Demo.git
cd DigitizedLibraryManagementSystem_Demo

Install dependencies:
composer install

Set up your .env file:
cp .env.example .env
(then edit .env with your actual database credentials)

Testing
This project includes automated tests for:

Environment configuration

Database connectivity

Author model CRUD operations

Book model CRUD operations

Run all tests with:
composer test

This command runs the following scripts sequentially:

Environment Test:
php test_env.php
(Verifies that the required environment variables are loaded)

Database Connectivity Test:
php test_db.php
(Verifies that the database connection works correctly)

Author Model CRUD Test:
php test_author.php
(Runs CRUD operations on the Author model and cleans up)

Book Model CRUD Test:
php test_book.php
(Runs CRUD operations on the Book model and cleans up)

Manual Utilities
List all authors:
php check_authors.php
(Lists all authors with IDs, names, and total count)

Remove test records (both books and authors):
php remove_test_records.php
or
composer cleanup

Usage
Start the built-in PHP server:
php -S localhost:8000 -t public

Visit http://localhost:8000 in your browser.

Project Structure
DigitizedLibraryManagementSystem_Demo-main/
├── api/
│ ├── authors/
│ │ ├── create.php
│ │ ├── delete.php
│ │ ├── get.php
│ │ ├── getAll.php
│ │ ├── index.php
│ │ ├── read_single.php
│ │ ├── read.php
│ │ └── update.php
│ └── books/
│ ├── create.php
│ ├── delete.php
│ ├── get.php
│ ├── getAll.php
│ ├── index.php
│ └── update.php
├── check_authors.php
├── composer-setup.php
├── composer.json
├── composer.lock
├── config/
│ └── Database.php
├── Database_Query.txt
├── JSON_Entries.txt
├── models/
│ ├── Author.php
│ └── book.php
├── public/
│ ├── admin.html
│ ├── authors.html
│ ├── authors.js
│ ├── books.html
│ ├── books.js
│ ├── create-account.html
│ ├── index.html
│ ├── login.html
│ └── styles.css
├── README.md
├── remove_test_records.php
├── test_author.php
├── test_book.php
├── test_db.php
├── test_env.php
├── UI/
│ ├── admin.html
│ ├── authors.html
│ ├── books.html
│ ├── create-account.html
│ ├── css/
│ │ └── styles.css
│ ├── index.html
│ ├── js/
│ │ ├── authors.js
│ │ └── books.js
│ └── login.html
└── vendor/
└── ... (Third-party packages)

Composer Scripts
Run the full test suite:
composer test

Remove all test records (authors and books):
composer cleanup

Contributing
Fork the repository

Create a feature branch:
git checkout -b feature/YourFeature

Commit your changes:
git commit -m "Add feature"

Push your changes:
git push origin feature/YourFeature

Open a pull request
