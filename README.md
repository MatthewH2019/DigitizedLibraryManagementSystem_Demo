# Digitized Library Management System

A PHP-based RESTful API and frontend for managing books and authors.

## Prerequisites

- **PHP:** Version 7.4 or higher  
- **Composer**  
- **PostgreSQL** (or another supported PDO driver)  
- **Environment File:** A `.env` file in the project root containing:

```
HOST=your_database_host
DBNAME=your_database_name
USERNAME=your_db_username
PASSWORD=your_db_password
PORT=5432
```

## Verifying Your Environment

**Check PostgreSQL Installation:**

```
psql --version
```

**Check PHP PDO and PostgreSQL Drivers:**

Create a temporary file `info.php`:

```
<?php phpinfo(); ?>
```

Start PHP’s built-in server:

```
php -S localhost:8000
```

Visit [http://localhost:8000/info.php](http://localhost:8000/info.php) in your browser and confirm you see sections for `PDO` and `PDO_pgsql`.

---

## Installation

**Clone the Repository**

```
git clone https://github.com/YourOrg/DigitizedLibraryManagementSystem_Demo.git
cd DigitizedLibraryManagementSystem_Demo
```

**Install Dependencies**

```
composer install
```

**Set Up Your .env File**

```
cp .env.example .env
```

Then edit `.env` with your actual credentials.

---

## Testing

This project includes automated tests for:

- Environment configuration  
- Database connectivity  
- Author model CRUD operations  
- Book model CRUD operations  

**Run All Tests**

```
composer test
```

Which runs the following:

- Environment Test  
  ```
  php test_env.php
  ```

- Database Connectivity Test  
  ```
  php test_db.php
  ```

- Author Model CRUD Test  
  ```
  php test_author.php
  ```

- Book Model CRUD Test  
  ```
  php test_book.php
  ```

---

## Manual Utilities

**List All Authors**

```
php check_authors.php
```

**Remove Test Records (Authors + Books)**

```
php remove_test_records.php
```

Or use the Composer command:

```
composer cleanup
```

---

## Usage

Start the built-in PHP server:

```
php -S localhost:8000 -t public
```

Then visit:

```
http://localhost:8000
```

---

## Project Structure

```
DigitizedLibraryManagementSystem_Demo-main/
├── api/
│   ├── authors/
│   │   ├── create.php
│   │   ├── delete.php
│   │   ├── get.php
│   │   ├── getAll.php
│   │   ├── index.php
│   │   ├── read_single.php
│   │   ├── read.php
│   │   └── update.php
│   └── books/
│       ├── create.php
│       ├── delete.php
│       ├── get.php
│       ├── getAll.php
│       ├── index.php
│       └── update.php
├── check_authors.php
├── composer-setup.php
├── composer.json
├── composer.lock
├── config/
│   └── Database.php
├── Database_Query.txt
├── JSON_Entries.txt
├── models/
│   ├── Author.php
│   └── book.php
├── public/
│   ├── admin.html
│   ├── authors.html
│   ├── authors.js
│   ├── books.html
│   ├── books.js
│   ├── create-account.html
│   ├── index.html
│   ├── login.html
│   └── styles.css
├── README.md
├── remove_test_records.php
├── test_author.php
├── test_book.php
├── test_db.php
├── test_env.php
├── UI/
│   ├── admin.html
│   ├── authors.html
│   ├── books.html
│   ├── create-account.html
│   ├── css/
│   │   └── styles.css
│   ├── index.html
│   ├── js/
│   │   ├── authors.js
│   │   └── books.js
│   └── login.html
└── vendor/
    └── ... (Third-party packages)
```

---

## Composer Scripts

**Run All Tests**

```
composer test
```

**Cleanup Test Records**

```
composer cleanup
```

---

## Contributing

**Fork the Repository**

**Create a Feature Branch**

```
git checkout -b feature/YourFeature
```

**Commit Your Changes**

```
git commit -m "Add feature"
```

**Push to Your Branch**

```
git push origin feature/YourFeature
```

**Open a Pull Request**