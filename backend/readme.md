# Laravel 5 Backend

This is the backend of the **HFR** project, built with Laravel 5. This guide will help you set up and configure the application.

---

## 📌 1. Installation and Setup

### Step 1: Clone the Repository

```bash
git clone https://gitlab.com/e4e-webdev2/hfr.git
cd hfr/backend
```

### Step 2: Install Dependencies

Ensure you have **PHP (>=7.4), Composer, and MySQL** installed. Then, run:

```bash
composer install
```

### Step 3: Setup Environment Variables

Copy the example environment file and update the required variables.

```bash
cp .env.example .env
```

Edit `.env` and set the following variables:

```ini
APP_NAME="HFR Backend"
APP_ENV=local
APP_KEY=base64:GENERATE_KEY_HERE
APP_DEBUG=true
APP_URL=http://your-backend-url.test

FRONTEND_URL=http://your-frontend-url.test
CONTACT_US_MAIL=your-email@example.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hfr_database
DB_USERNAME=root
DB_PASSWORD=your_password
```

### Step 4: Generate Application Key

```bash
php artisan key:generate
```

### Step 5: Set Up Storage Symlink

To make files in `storage/app/public` accessible via `public/storage`, run:

```bash
php artisan storage:link
```

---

## 📌 2. Database Setup

Since we are **not using models or migrations**, manually create the necessary tables.

### Step 1: Access MySQL

```bash
mysql -u root -p
```

### Step 2: Create Database

```sql
CREATE DATABASE hfr_database;
```

### Step 3: Use Database

```sql
USE hfr_database;
```

### Step 4: Create Tables Manually

Create your tables based on the project requirements. Example:

```sql
CREATE TABLE sliders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    sub_title VARCHAR(255) NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    status TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE origins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);


CREATE TABLE processes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE process_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    status TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);


ALTER TABLE hs_hospitals_history 
ADD COLUMN image_url JSON NULL AFTER facility_name;

```

---

## 📌 3. SMTP Setup (Email Configuration)

To enable email sending, configure SMTP in `.env`:

```ini
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_smtp_username
MAIL_PASSWORD=your_smtp_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=no-reply@example.com
MAIL_FROM_NAME="HFR Backend"
```

---

## 📌 4. Running the Application

### Start the Laravel Development Server

```bash
php artisan serve
```

By default, it runs on `http://127.0.0.1:8000/`.

---

## 📌 5. Common Issues & Solutions

### 1️⃣ **Error: `.env` Not Loaded**

Run:

```bash
php artisan config:clear
php artisan cache:clear
```

### 2️⃣ **Storage Link Not Working**

```bash
php artisan storage:link
```

### 3️⃣ **Permission Issues (Linux)**

```bash
chmod -R 777 storage bootstrap/cache
```

---

## 📌 6. API Documentation

Refer to the `routes/api.php` file for API endpoints.

---

## 📌 7. Contribution Guidelines

1. **Create a new branch** before making changes.
2. **Commit messages should be clear and descriptive.**
3. **Test before pushing changes.**

---

## 📌 8. Contact

For issues or questions, contact the team at **your-email@example.com**.
