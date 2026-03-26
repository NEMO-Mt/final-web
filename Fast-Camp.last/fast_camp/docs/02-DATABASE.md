# 🗄️ ฐานข้อมูล FAST CAMP

> Database Schema & Design Documentation

---

## 📋 สารบัญ

1. [ภาพรวมฐานข้อมูล](#-ภาพรวมฐานข้อมูล)
2. [ER Diagram](#-er-diagram)
3. [รายละเอียดตาราง](#-รายละเอียดตาราง)
4. [ความสัมพันธ์ระหว่างตาราง](#-ความสัมพันธ์ระหว่างตาราง)
5. [SQL Schema](#-sql-schema)
6. [Indexes & Constraints](#-indexes--constraints)

---

## 📊 ภาพรวมฐานข้อมูล

**Database Name:** `fast_camp`  
**Engine:** MySQL / MariaDB  
**Charset:** utf8mb4_unicode_ci (แนะนำ)

### ตารางทั้งหมด (4 ตาราง)

| ลำดับ | ตาราง | รายละเอียด |
|-------|-------|------------|
| 1 | `users` | ข้อมูลผู้ใช้งานระบบ |
| 2 | `activities` | ข้อมูลกิจกรรม |
| 3 | `activity_images` | รูปภาพประกอบกิจกรรม |
| 4 | `registrations` | การลงทะเบียนเข้าร่วมกิจกรรม |

---

## 🔗 ER Diagram

```
┌─────────────────────────────────────────────────────────────────────┐
│                            ER DIAGRAM                                │
└─────────────────────────────────────────────────────────────────────┘

    ┌─────────────┐           ┌───────────────┐
    │    users    │           │  activities   │
    ├─────────────┤           ├───────────────┤
    │ PK user_id  │◄──────┐   │ PK activity_id│
    │    email    │       │   │    title      │
    │    password │       │   │    detail     │
    │    full_name│       │   │    start_date │
    │    birthday │       │   │    end_date   │
    │    gender   │       │   │    location   │
    │    occupation      │   │    owner_id   │────┐
    │    phone    │       │   │    created_at │    │
    │    created_at      │   └───────────────┘    │
    └─────────────┘              ▲                │
           ▲                     │                │
           │                     │                │
           │          ┌──────────┴───────────┐    │
           │          │   activity_images    │    │
           │          ├──────────────────────┤    │
           │          │ PK image_id          │    │
           │          │    image_path        │    │
           │          │ FK activity_id       │────┘
           │          │    created_at        │
           │          └──────────────────────┘
           │                     ▲
           │                     │
           │          ┌──────────┴───────────┐
           │          │    registrations     │
           │          ├──────────────────────┤
           │          │ PK reg_id            │
           └──────────┤ FK user_id           │
                      │ FK activity_id      │
                      │    status           │
                      │    is_checkin       │
                      │    created_at       │
                      │    updated_at       │
                      └──────────────────────┘

LEGEND:
─────── Primary Key (PK)
├┤├┤├    Foreign Key (FK)
───►     One-to-Many Relationship

```

---

## 📑 รายละเอียดตาราง

### 1️⃣ ตาราง `users`

เก็บข้อมูลผู้ใช้งานระบบ

| คอลัมน์ | ประเภท | Null | Default | รายละเอียด |
|---------|--------|------|---------|------------|
| `user_id` | `INT` | NO | AUTO_INCREMENT | **PK** - ID ผู้ใช้ |
| `email` | `VARCHAR(255)` | NO | - | **UNIQUE** - อีเมล |
| `password` | `VARCHAR(255)` | NO | - | รหัสผ่าน (bcrypt hash) |
| `full_name` | `VARCHAR(255)` | NO | - | ชื่อ-นามสกุล |
| `birthday` | `DATE` | NO | - | วันเกิด (ใช้คำนวณอายุ) |
| `gender` | `ENUM('male','female','other')` | NO | - | เพศ |
| `occupation` | `VARCHAR(255)` | NO | - | อาชีพ |
| `phone` | `VARCHAR(20)` | NO | - | เบอร์โทรศัพท์ |
| `created_at` | `TIMESTAMP` | NO | CURRENT_TIMESTAMP | วันที่สมัคร |

**SQL:**
```sql
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    birthday DATE NOT NULL,
    gender ENUM('male', 'female', 'other') NOT NULL,
    occupation VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

### 2️⃣ ตาราง `activities`

เก็บข้อมูลกิจกรรม

| คอลัมน์ | ประเภท | Null | Default | รายละเอียด |
|---------|--------|------|---------|------------|
| `activity_id` | `INT` | NO | AUTO_INCREMENT | **PK** - ID กิจกรรม |
| `title` | `VARCHAR(255)` | NO | - | ชื่อกิจกรรม |
| `detail` | `TEXT` | NO | - | รายละเอียดกิจกรรม |
| `start_date` | `DATETIME` | NO | - | วันที่เริ่ม |
| `end_date` | `DATETIME` | NO | - | วันที่สิ้นสุด |
| `location` | `VARCHAR(255)` | NO | - | สถานที่ |
| `owner_id` | `INT` | NO | - | **FK** → users(user_id) |
| `created_at` | `TIMESTAMP` | NO | CURRENT_TIMESTAMP | วันที่สร้าง |

**SQL:**
```sql
CREATE TABLE IF NOT EXISTS activities (
    activity_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    detail TEXT NOT NULL,
    start_date DATETIME NOT NULL,
    end_date DATETIME NOT NULL,
    location VARCHAR(255) NOT NULL,
    owner_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (owner_id) REFERENCES users(user_id) ON DELETE CASCADE
);
```

---

### 3️⃣ ตาราง `activity_images`

เก็บรูปภาพประกอบกิจกรรม (1 กิจกรรม = หลายรูป)

| คอลัมน์ | ประเภท | Null | Default | รายละเอียด |
|---------|--------|------|---------|------------|
| `image_id` | `INT` | NO | AUTO_INCREMENT | **PK** - ID รูปภาพ |
| `image_path` | `VARCHAR(255)` | NO | - | path รูปภาพ (uploads/xxx.jpg) |
| `activity_id` | `INT` | NO | - | **FK** → activities(activity_id) |
| `created_at` | `TIMESTAMP` | NO | CURRENT_TIMESTAMP | วันที่อัพโหลด |

**SQL:**
```sql
CREATE TABLE IF NOT EXISTS activity_images (
    image_id INT AUTO_INCREMENT PRIMARY KEY,
    image_path VARCHAR(255) NOT NULL,
    activity_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (activity_id) REFERENCES activities(activity_id) ON DELETE CASCADE
);
```

---

### 4️⃣ ตาราง `registrations`

เก็บการลงทะเบียนเข้าร่วมกิจกรรม

| คอลัมน์ | ประเภท | Null | Default | รายละเอียด |
|---------|--------|------|---------|------------|
| `reg_id` | `INT` | NO | AUTO_INCREMENT | **PK** - ID การลงทะเบียน |
| `status` | `ENUM('pending','approved','rejected')` | NO | 'pending' | สถานะ |
| `is_checkin` | `BOOLEAN` | NO | 0 | เช็คชื่อแล้วหรือยัง |
| `activity_id` | `INT` | NO | - | **FK** → activities(activity_id) |
| `user_id` | `INT` | NO | - | **FK** → users(user_id) |
| `created_at` | `TIMESTAMP` | NO | CURRENT_TIMESTAMP | วันที่ลงทะเบียน |
| `updated_at` | `TIMESTAMP` | NO | CURRENT_TIMESTAMP ON UPDATE | วันที่อัพเดท |

**Constraints:**
- `UNIQUE KEY unique_registration (activity_id, user_id)` - ผู้ใช้ 1 คน ลงทะเบียนกิจกรรมเดียวกันได้ 1 ครั้ง

**SQL:**
```sql
CREATE TABLE IF NOT EXISTS registrations (
    reg_id INT AUTO_INCREMENT PRIMARY KEY,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    is_checkin BOOLEAN DEFAULT 0,
    activity_id INT NOT NULL,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (activity_id) REFERENCES activities(activity_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    UNIQUE KEY unique_registration (activity_id, user_id)
);
```

---

## 🔗 ความสัมพันธ์ระหว่างตาราง

### ความสัมพันธ์แบบ One-to-Many (1:N)

| ตารางหลัก | ตารางย่อย | ความสัมพันธ์ |
|-----------|-----------|--------------|
| `users` (1) | `activities` (N) | ผู้ใช้ 1 คน สร้างกิจกรรมได้หลายอัน |
| `activities` (1) | `activity_images` (N) | กิจกรรม 1 อัน มีรูปภาพได้หลายรูป |
| `activities` (1) | `registrations` (N) | กิจกรรม 1 อัน มีผู้ลงทะเบียนหลายคน |
| `users` (1) | `registrations` (N) | ผู้ใช้ 1 คน ลงทะเบียนกิจกรรมได้หลายอัน |

### ความสัมพันธ์แบบ Many-to-Many (N:M)

`users` ↔ `activities` ผ่านตาราง `registrations`
- ผู้ใช้หลายคน เข้าร่วมกิจกรรมหลายอัน
- กิจกรรมหลายอัน มีผู้เข้าร่วมหลายคน

---

## 💾 SQL Schema ทั้งหมด

```sql
-- สร้างฐานข้อมูล
CREATE DATABASE IF NOT EXISTS fast_camp 
    CHARACTER SET utf8mb4 
    COLLATE utf8mb4_unicode_ci;

USE fast_camp;

-- ============================================
-- ตาราง 1: users
-- ============================================
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    birthday DATE NOT NULL,
    gender ENUM('male', 'female', 'other') NOT NULL,
    occupation VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- ตาราง 2: activities
-- ============================================
CREATE TABLE IF NOT EXISTS activities (
    activity_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    detail TEXT NOT NULL,
    start_date DATETIME NOT NULL,
    end_date DATETIME NOT NULL,
    location VARCHAR(255) NOT NULL,
    owner_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (owner_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- ตาราง 3: activity_images
-- ============================================
CREATE TABLE IF NOT EXISTS activity_images (
    image_id INT AUTO_INCREMENT PRIMARY KEY,
    image_path VARCHAR(255) NOT NULL,
    activity_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (activity_id) REFERENCES activities(activity_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- ตาราง 4: registrations
-- ============================================
CREATE TABLE IF NOT EXISTS registrations (
    reg_id INT AUTO_INCREMENT PRIMARY KEY,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    is_checkin BOOLEAN DEFAULT 0,
    activity_id INT NOT NULL,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (activity_id) REFERENCES activities(activity_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    UNIQUE KEY unique_registration (activity_id, user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 🔍 Indexes & Constraints

### Primary Keys (PK)
| ตาราง | PK | Auto Increment |
|-------|----|----------------|
| users | user_id | ✓ |
| activities | activity_id | ✓ |
| activity_images | image_id | ✓ |
| registrations | reg_id | ✓ |

### Unique Constraints
| ตาราง | คอลัมน์ | รายละเอียด |
|-------|--------|------------|
| users | email | อีเมลต้องไม่ซ้ำ |
| registrations | (activity_id, user_id) | ลงทะเบียนซ้ำไม่ได้ |

### Foreign Keys (FK) & ON DELETE

| FK | References | ON DELETE | ผลกระทบ |
|----|------------|-----------|---------|
| activities.owner_id → users.user_id | `CASCADE` | ลบ user → ลบกิจกรรมของ user |
| activity_images.activity_id → activities.activity_id | `CASCADE` | ลบ activity → ลบรูปภาพ |
| registrations.activity_id → activities.activity_id | `CASCADE` | ลบ activity → ลบการลงทะเบียน |
| registrations.user_id → users.user_id | `CASCADE` | ลบ user → ลบการลงทะเบียน |

---

## 🔄 Registration Status Flow

```
┌─────────────────────────────────────────────────────────┐
│              Registration Status Lifecycle               │
└─────────────────────────────────────────────────────────┘

┌──────────┐     ┌───────────┐     ┌───────────┐
│  START   │────►│  pending  │────►│  approved │
└──────────┘     └───────────┘     └─────┬─────┘
      │                    │             │
      │                    ▼             ▼
      │              ┌───────────┐   ┌───────────┐
      └─────────────►│  rejected │   │ is_checkin│
                     └───────────┘   │    = 1    │
                                     └───────────┘

Flow:
1. User ลงทะเบียน → status = 'pending', is_checkin = 0
2. Owner อนุมัติ → status = 'approved', is_checkin = 0
3. User แสดง OTP → Owner กรอกใน Check-in
4. Check-in สำเร็จ → is_checkin = 1 (status ยังเป็น approved)

ถ้า Owner ปฏิเสธ → status = 'rejected'
```

---

## 📊 ตัวอย่างข้อมูล (Sample Data)

```sql
-- ผู้ใช้ตัวอย่าง
INSERT INTO users (email, password, full_name, birthday, gender, occupation, phone) 
VALUES (
    'john@example.com',
    '$2y$10$...bcrypt_hash...',
    'John Doe',
    '1995-05-15',
    'male',
    'Software Developer',
    '0812345678'
);

-- กิจกรรมตัวอย่าง
INSERT INTO activities (title, detail, start_date, end_date, location, owner_id)
VALUES (
    'Camping ที่เขาใหญ่',
    'กิจกรรมแคมป์ปิ้ง 2 วัน 1 คืน',
    '2024-03-15 08:00:00',
    '2024-03-16 18:00:00',
    'อุทยานแห่งชาติเขาใหญ่',
    1
);

-- รูปภาพตัวอย่าง
INSERT INTO activity_images (image_path, activity_id)
VALUES ('uploads/activity_1_1709000000_0.jpg', 1);

-- การลงทะเบียนตัวอย่าง
INSERT INTO registrations (activity_id, user_id, status, is_checkin)
VALUES (1, 2, 'approved', 1);
```

---

## 💡 คำแนะนำเพิ่มเติม

### 1. การเพิ่ม Full-Text Search Index
```sql
-- สำหรับค้นหาข้อความในกิจกรรม
ALTER TABLE activities ADD FULLTEXT INDEX ft_search (title, detail, location);
```

### 2. การเพิ่ม Index สำหรับการค้นหา
```sql
-- เร็วขึ้นเมื่อค้นหาตามวันที่
CREATE INDEX idx_start_date ON activities(start_date);

-- เร็วขึ้นเมื่อค้นหาด้วย owner
CREATE INDEX idx_owner_id ON activities(owner_id);

-- เร็วขึ้นเมื่อค้นหาการลงทะเบียนตาม status
CREATE INDEX idx_reg_status ON registrations(activity_id, status);
```

### 3. การ Backup ฐานข้อมูล
```bash
mysqldump -u root -p fast_camp > backup_$(date +%Y%m%d).sql
```

---

<div align="center">
  <p><a href="01-ARCHITECTURE.md">← สถาปัตยกรรม</a> | <a href="03-ROUTES.md">ถัดไป: Routes →</a></p>
</div>
