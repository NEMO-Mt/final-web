# ⚙️ การติดตั้ง FAST CAMP

> Setup & Installation Guide - ขั้นตอนการติดตั้งระบบให้ใช้งานได้

---

## 📋 สารบัญ

1. [ความต้องการระบบ](#-ความต้องการระบบ)
2. [ขั้นตอนการติดตั้ง](#-ขั้นตอนการติดตั้ง)
3. [การตั้งค่า Apache](#-การตั้งค่า-apache)
4. [การตั้งค่า Nginx](#-การตั้งค่า-nginx)
5. [การตั้งค่า Database](#-การตั้งค่า-database)
6. [การตั้งค่า XAMPP](#-การตั้งค่า-xampp-windows)
7. [การตั้งค่า Production](#-การตั้งค่า-production)
8. [แก้ไขปัญหาเบื้องต้น](#-แก้ไขปัญหาเบื้องต้น)

---

## 💻 ความต้องการระบบ

### Server Requirements

| ซอฟต์แวร์ | เวอร์ชันขั้นต่ำ | แนะนำ |
|-----------|----------------|--------|
| PHP | 8.0+ | 8.2+ |
| MySQL | 5.7+ | 8.0+ |
| MariaDB | 10.3+ | 10.6+ |
| Apache | 2.4+ | 2.4+ (พร้อม mod_rewrite) |
| Nginx | 1.18+ | 1.24+ |

### PHP Extensions ที่ต้องการ

```
extension=mysqli
extension=mbstring
extension=openssl
```

**ตรวจสอบ:**
```bash
php -m | grep -E "mysqli|mbstring|openssl"
```

---

## 🚀 ขั้นตอนการติดตั้ง

### ขั้นตอนที่ 1: ดาวน์โหลดโปรเจค

```bash
# วิธีที่ 1: Git Clone (ถ้ามี repository)
git clone [your-repo-url] fast_camp
cd fast_camp

# วิธีที่ 2: คัดลอกไฟล์ด้วยตนเอง
# คัดลอกโฟลเดอร์ fast_camp ไปยัง Document Root
```

### ขั้นตอนที่ 2: สร้างฐานข้อมูล

```bash
# Method 1: Command Line
mysql -u root -p < database.sql

# Method 2: ด้วยตนเองใน phpMyAdmin
# 1. เปิด phpMyAdmin
# 2. คลิก "Import"
# 3. เลือกไฟล์ database.sql
# 4. คลิก "Go"
```

**ผลลัพธ์:**
- Database: `fast_camp`
- Tables: `users`, `activities`, `activity_images`, `registrations`

### ขั้นตอนที่ 3: ตั้งค่าการเชื่อมต่อ Database

แก้ไขไฟล์ `includes/database.php`:

```php
<?php
declare(strict_types=1);

function getConnection(): mysqli
{
    $hostname = 'localhost';
    $dbName = 'fast_camp';
    $username = 'root';      // เปลี่ยนถ้าจำเป็น
    $password = '';          // เปลี่ยนถ้าจำเป็น
    
    $conn = new mysqli($hostname, $username, $password, $dbName);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}
```

### ขั้นตอนที่ 4: ตั้งค่า Permissions

```bash
# สร้างโฟลเดอร์ uploads (ถ้ายังไม่มี)
mkdir -p public/uploads/avatars

# ตั้งค่า permissions (Linux/Mac)
chmod -R 755 public/uploads

# หรือให้ PHP สามารถเขียนได้
chmod -R 777 public/uploads  # ระวัง: ใช้เฉพาะ development
```

### ขั้นตอนที่ 5: ตั้งค่า Web Server

เลือก Apache หรือ Nginx:

---

## 🌐 การตั้งค่า Apache

### Option A: ใช้ .htaccess (พร้อมใช้แล้ว)

ไฟล์ `public/.htaccess` มีอยู่แล้ว:
```apache
RewriteEngine on
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule . index.php [L]
```

### Option B: Apache VirtualHost

```apache
<VirtualHost *:80>
    ServerName fastcamp.local
    DocumentRoot /var/www/fast_camp/public
    
    <Directory /var/www/fast_camp/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/fastcamp_error.log
    CustomLog ${APACHE_LOG_DIR}/fastcamp_access.log combined
</VirtualHost>
```

### Option C: XAMPP (Windows)

แก้ไข `C:\xampp\apache\conf\httpd.conf`:
```apache
# เปลี่ยน DocumentRoot
DocumentRoot "C:/xampp/htdocs/fast_camp/public"
<Directory "C:/xampp/htdocs/fast_camp/public">
    Options Indexes FollowSymLinks
    AllowOverride All
    Require all granted
</Directory>
```

---

## 🌐 การตั้งค่า Nginx

```nginx
server {
    listen 80;
    server_name fastcamp.local;
    root /var/www/fast_camp/public;
    index index.php;

    # ป้องกัน access ไฟล์สำคัญ
    location ~ \.(sql|log|ini)$ {
        deny all;
    }

    # จัดการ uploads
    location /uploads/ {
        try_files $uri =404;
    }

    # PHP Processing
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Rewrite ทุกอย่างไป index.php
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
}
```

---

## 🗄️ การตั้งค่า Database

### MySQL/MariaDB Command Line

```bash
# เข้าสู่ MySQL
mysql -u root -p

# สร้าง database
CREATE DATABASE fast_camp 
    CHARACTER SET utf8mb4 
    COLLATE utf8mb4_unicode_ci;

# สร้าง user (แนะนำ)
CREATE USER 'fastcamp_user'@'localhost' IDENTIFIED BY 'strong_password';
GRANT ALL PRIVILEGES ON fast_camp.* TO 'fastcamp_user'@'localhost';
FLUSH PRIVILEGES;

# นำเข้า schema
USE fast_camp;
SOURCE /path/to/database.sql;

# ตรวจสอบ
SHOW TABLES;
```

### การแก้ไขไฟล์ database.php

```php
function getConnection(): mysqli
{
    $hostname = 'localhost';
    $dbName = 'fast_camp';
    $username = 'fastcamp_user';     // เปลี่ยนเป็น user ที่สร้าง
    $password = 'strong_password';    // เปลี่ยนเป็นรหัสผ่านจริง
    // ...
}
```

---

## 🪟 การตั้งค่า XAMPP (Windows)

### Step 1: ติดตั้ง XAMPP
1. ดาวน์โหลดจาก https://www.apachefriends.org/
2. ติดตั้งที่ `C:\xampp`
3. เปิด XAMPP Control Panel
4. Start Apache และ MySQL

### Step 2: ติดตั้งโปรเจค
```
C:\xampp\htdocs\
    └── fast_camp\
        ├── databases\
        ├── includes\
        ├── public\
        ├── routes\
        ├── templates\
        └── database.sql
```

### Step 3: สร้าง VirtualHost (แนะนำ)

แก้ไข `C:\xampp\apache\conf\extra\httpd-vhosts.conf`:
```apache
<VirtualHost *:80>
    DocumentRoot "C:/xampp/htdocs"
    ServerName localhost
</VirtualHost>

<VirtualHost *:80>
    DocumentRoot "C:/xampp/htdocs/fast_camp/public"
    ServerName fastcamp.test
    <Directory "C:/xampp/htdocs/fast_camp/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

แก้ไข `C:\Windows\System32\drivers\etc\hosts`:
```
127.0.0.1  fastcamp.test
```

Restart Apache

### Step 4: สร้างฐานข้อมูล
1. ไปที่ http://localhost/phpmyadmin
2. คลิก "New"
3. ชื่อ: `fast_camp`
4. เลือก utf8mb4_unicode_ci
5. คลิก "Create"
6. คลิก "Import" tab
7. เลือกไฟล์ `database.sql`
8. คลิก "Go"

### Step 5: ทดสอบ
เปิด browser: http://fastcamp.test

---

## 🔐 การตั้งค่า Production

### 1. Environment Variables (แนะนำ)

สร้างไฟล์ `.env` (อยู่นอก git):
```bash
DB_HOST=localhost
DB_NAME=fast_camp
DB_USER=fastcamp_prod
DB_PASS=your_very_strong_password

OTP_SECRET=change_this_to_random_32_chars
```

สร้างไฟล์ `includes/config.php`:
```php
<?php
// Load .env
$env = parse_ini_file(__DIR__ . '/../.env');

// หรือใช้ getenv()
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_USER', getenv('DB_USER') ?: 'root');
// ...
```

### 2. SSL/HTTPS

```apache
<VirtualHost *:443>
    ServerName your-domain.com
    DocumentRoot /var/www/fast_camp/public
    
    SSLEngine on
    SSLCertificateFile /path/to/cert.pem
    SSLCertificateKeyFile /path/to/key.pem
    
    # HSTS
    Header always set Strict-Transport-Security "max-age=31536000"
</VirtualHost>
```

### 3. Security Headers

เพิ่มใน `.htaccess`:
```apache
Header always set X-Frame-Options "SAMEORIGIN"
Header always set X-XSS-Protection "1; mode=block"
Header always set X-Content-Type-Options "nosniff"
Header always set Referrer-Policy "strict-origin-when-cross-origin"
```

### 4. Database Production Settings

```php
// includes/database.php - Production version
function getConnection(): mysqli
{
    $conn = new mysqli(
        getenv('DB_HOST') ?: 'localhost',
        getenv('DB_USER') ?: 'fastcamp_prod',
        getenv('DB_PASS') ?: '',
        getenv('DB_NAME') ?: 'fast_camp'
    );
    
    if ($conn->connect_error) {
        error_log("Database connection failed: " . $conn->connect_error);
        die("Service temporarily unavailable");
    }
    
    return $conn;
}
```

### 5. File Permissions (Production)

```bash
# ตั้งค่าให้ถูกต้อง
chown -R www-data:www-data /var/www/fast_camp
chmod -R 755 /var/www/fast_camp
chmod -R 775 /var/www/fast_camp/public/uploads

# ห้าม execute PHP ใน uploads
chattr +i /var/www/fast_camp/public/uploads/.htaccess
```

สร้าง `public/uploads/.htaccess`:
```apache
# ป้องกัน execution
php_flag engine off
<FilesMatch "\.(php|php\.)$">
    Order allow,deny
    Deny from all
</FilesMatch>
```

### 6. Backup Script

สร้าง `backup.sh`:
```bash
#!/bin/bash
BACKUP_DIR="/backup/fast_camp"
DATE=$(date +%Y%m%d_%H%M%S)

# Database backup
mysqldump -u root -p'password' fast_camp > "$BACKUP_DIR/db_$DATE.sql"

# Files backup
tar -czf "$BACKUP_DIR/files_$DATE.tar.gz" /var/www/fast_camp

# Keep only last 7 days
find $BACKUP_DIR -name "*.sql" -mtime +7 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +7 -delete
```

---

## 🔧 แก้ไขปัญหาเบื้องต้น

### ปัญหา 1: "404 Not Found" ทุกหน้า

**สาเหตุ:** mod_rewrite ไม่ทำงาน หรือ .htaccess ไม่ถูกต้อง

**แก้ไข:**
```bash
# Apache - เปิด mod_rewrite
sudo a2enmod rewrite
sudo systemctl restart apache2

# ตรวจสอบ AllowOverride
# ใน VirtualHost ต้องมี:
AllowOverride All
```

### ปัญหา 2: "Connection failed" ฐานข้อมูล

**ตรวจสอบ:**
```php
// เพิ่ม debug ชั่วคราว
$conn = new mysqli($hostname, $username, $password, $dbName);
if ($conn->connect_error) {
    die("Error: " . $conn->connect_error);
}
echo "Connected successfully!";
```

**สาเหตุทั่วไป:**
- Username/password ผิด
- Database ยังไม่ถูกสร้าง
- MySQL ไม่ทำงาน

### ปัญหา 3: อัพโหลดรูปภาพไม่ได้

**ตรวจสอบ:**
```bash
# Permissions
ls -la public/uploads/

# ควรเห็น:
drwxrwxr-x  www-data www-data  uploads

# แก้ไข:
sudo chown -R www-data:www-data public/uploads
sudo chmod -R 755 public/uploads
```

**php.ini settings:**
```ini
upload_max_filesize = 8M
post_max_size = 8M
max_file_uploads = 20
```

### ปัญหา 4: Session ไม่ทำงาน

**ตรวจสอบ:**
```php
<?php
session_start();
if (session_status() === PHP_SESSION_NONE) {
    echo "Session not started";
}
echo "Session ID: " . session_id();
print_r($_SESSION);
```

**php.ini:**
```ini
session.save_path = "/tmp"
session.gc_maxlifetime = 1440
```

### ปัญหา 5: Thai Language เป็นสัญลักษณ์ ?

**แก้ไข:** ตรวจสอบ charset ทั้งหมด
```php
// ใน database.php หลัง connect
$conn->set_charset("utf8mb4");
```

```html
<!-- ใน HTML template -->
<meta charset="UTF-8">
```

```sql
-- ใน database
CREATE DATABASE fast_camp 
    CHARACTER SET utf8mb4 
    COLLATE utf8mb4_unicode_ci;
```

---

## ✅ Post-Installation Checklist

- [ ] สามารถเข้าถึง http://your-domain/login ได้
- [ ] สามารถสมัครสมาชิกได้
- [ ] สามารถเข้าสู่ระบบได้
- [ ] สามารถสร้างกิจกรรมได้
- [ ] สามารถอัพโหลดรูปภาพได้
- [ ] สามารถลงทะเบียนกิจกรรมได้
- [ ] OTP Check-in ทำงานได้
- [ ] สถิติแสดงผลถูกต้อง

---

## 📞 การขอความช่วยเหลือ

ถ้าพบปัญหา:

1. ตรวจสอบ `error_log` ของ Apache/Nginx
2. ตรวจสอบ permission ของไฟล์/โฟลเดอร์
3. ตรวจสอบการเชื่อมต่อฐานข้อมูล
4. ดูที่ [docs/05-SECURITY.md](05-SECURITY.md) สำหรับ security tips

---

<div align="center">
  <p><a href="05-SECURITY.md">← ความปลอดภัย</a> | <a href="../README.md">กลับไปหน้าหลัก →</a></p>
</div>
