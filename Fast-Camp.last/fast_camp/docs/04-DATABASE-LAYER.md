# 💾 Database Layer FAST CAMP

> Complete Database Functions Documentation - รายละเอียดฟังก์ชันทั้งหมดใน `databases/`

---

## 📋 สารบัญ

1. [ภาพรวม Database Layer](#-ภาพรวม-database-layer)
2. [users.php - ฟังก์ชันผู้ใช้](#-usersphp)
3. [activities.php - ฟังก์ชันกิจกรรม](#-activitiesphp)
4. [registrations.php - ฟังก์ชันการลงทะเบียน](#-registrationsphp)
5. [helpers.php - ฟังก์ชันช่วยเหลือ](#-helpersphp)

---

## 📊 ภาพรวม Database Layer

โครงสร้างไฟล์ใน `databases/`:

```
databases/
├── users.php          # ฟังก์ชัน CRUD สำหรับ users
├── activities.php     # ฟังก์ชัน CRUD สำหรับ activities และ activity_images
├── registrations.php  # ฟังก์ชัน CRUD สำหรับ registrations + stats
├── helpers.php        # ฟังก์ชันช่วยเหลือ (OTP, Avatar)
└── otp.php           # (ไม่ใช้แล้ว) OTP แบบ database-based
```

หลักการทำงาน:
- ทุกไฟล์ `require_once __DIR__ . '/../includes/database.php'`
- ใช้ **MySQLi Prepared Statements** ทุกครั้ง
- ปิด connection (`$conn->close()`) ทุกครั้งที่เสร็จ
- ใช้ `declare(strict_types=1)` เพื่อ type safety

---

## 👤 users.php

### รายการฟังก์ชัน

| ฟังก์ชัน | รายละเอียด |
|----------|------------|
| `createUser(array $user): bool` | สร้างผู้ใช้ใหม่ |
| `getUserByEmail(string $email): ?array` | ค้นหาด้วยอีเมล |
| `getUserById(int $userId): ?array` | ค้นหาด้วย ID |
| `updateUser(int $userId, array $user): bool` | อัพเดทข้อมูล |
| `authenticateUser(string $email, string $password): ?array` | ตรวจสอบ login |

---

### createUser(array $user): bool

**สร้างผู้ใช้ใหม่**

```php
function createUser(array $user): bool
{
    $conn = getConnection();
    $sql = 'INSERT INTO users (email, password, full_name, birthday, gender, occupation, phone) 
            VALUES (?, ?, ?, ?, ?, ?, ?)';
    $stmt = $conn->prepare($sql);
    $hashedPassword = password_hash($user['password'], PASSWORD_DEFAULT);
    $stmt->bind_param('sssssss', 
        $user['email'], 
        $hashedPassword, 
        $user['full_name'], 
        $user['birthday'], 
        $user['gender'], 
        $user['occupation'], 
        $user['phone']
    );
    $result = $stmt->execute();
    $stmt->close();
    $conn->close();
    return $result;
}
```

**Parameters:**
| ตัวแปร | ชนิด | รายละเอียด |
|--------|------|------------|
| `$user['email']` | string | อีเมล |
| `$user['password']` | string | รหัสผ่าน (plaintext) |
| `$user['full_name']` | string | ชื่อ-นามสกุล |
| `$user['birthday']` | string | วันเกิด (YYYY-MM-DD) |
| `$user['gender']` | string | 'male' | 'female' | 'other' |
| `$user['occupation']` | string | อาชีพ |
| `$user['phone']` | string | เบอร์โทร |

**Returns:** `bool` - true ถ้าสำเร็จ

---

### getUserByEmail(string $email): ?array

**ค้นหาผู้ใช้ด้วยอีเมล**

```php
function getUserByEmail(string $email): ?array
{
    $conn = getConnection();
    $sql = 'SELECT * FROM users WHERE email = ?';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
    return $user ?: null;
}
```

**Returns:** `?array` - ข้อมูล user หรือ null ถ้าไม่พบ

```php
// Return structure
[
    'user_id' => 1,
    'email' => 'john@example.com',
    'password' => '$2y$10$...',
    'full_name' => 'John Doe',
    'birthday' => '1995-05-15',
    'gender' => 'male',
    'occupation' => 'Developer',
    'phone' => '0812345678',
    'created_at' => '2024-01-15 10:30:00'
]
```

---

### getUserById(int $userId): ?array

**ค้นหาผู้ใช้ด้วย ID**

```php
function getUserById(int $userId): ?array
{
    $conn = getConnection();
    $sql = 'SELECT * FROM users WHERE user_id = ?';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
    return $user ?: null;
}
```

**Parameters:**
| ตัวแปร | ชนิด | รายละเอียด |
|--------|------|------------|
| `$userId` | int | ID ผู้ใช้ |

**Returns:** `?array` - ข้อมูล user หรือ null

---

### updateUser(int $userId, array $user): bool

**อัพเดทข้อมูลผู้ใช้**

```php
function updateUser(int $userId, array $user): bool
{
    $conn = getConnection();
    
    if (isset($user['profile_image'])) {
        $sql = 'UPDATE users SET full_name = ?, birthday = ?, gender = ?, 
                occupation = ?, phone = ?, profile_image = ? WHERE user_id = ?';
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssssssi', 
            $user['full_name'], 
            $user['birthday'], 
            $user['gender'], 
            $user['occupation'], 
            $user['phone'], 
            $user['profile_image'], 
            $userId
        );
    } else {
        $sql = 'UPDATE users SET full_name = ?, birthday = ?, gender = ?, 
                occupation = ?, phone = ? WHERE user_id = ?';
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sssssi', 
            $user['full_name'], 
            $user['birthday'], 
            $user['gender'], 
            $user['occupation'], 
            $user['phone'], 
            $userId
        );
    }
    
    $result = $stmt->execute();
    $stmt->close();
    $conn->close();
    return $result;
}
```

**Parameters:**
| ตัวแปร | ชนิด | จำเป็น? | รายละเอียด |
|--------|------|---------|------------|
| `$userId` | int | ✓ | ID ผู้ใช้ |
| `$user['full_name']` | string | ✓ | ชื่อ |
| `$user['birthday']` | string | ✓ | วันเกิด |
| `$user['gender']` | string | ✓ | เพศ |
| `$user['occupation']` | string | ✓ | อาชีพ |
| `$user['phone']` | string | ✓ | เบอร์โทร |
| `$user['profile_image']` | string | ✗ | Path รูปโปรไฟล์ |

**Returns:** `bool`

---

### authenticateUser(string $email, string $password): ?array

**ตรวจสอบการเข้าสู่ระบบ**

```php
function authenticateUser(string $email, string $password): ?array
{
    $user = getUserByEmail($email);
    if ($user && password_verify($password, $user['password'])) {
        unset($user['password']);  // ลบ password ออกจากผลลัพธ์
        return $user;
    }
    return null;
}
```

**Parameters:**
| ตัวแปร | ชนิด | รายละเอียด |
|--------|------|------------|
| `$email` | string | อีเมล |
| `$password` | string | รหัสผ่าน plaintext |

**Returns:** `?array` - ข้อมูล user (ไม่มี password) หรือ null

---

## 📅 activities.php

### รายการฟังก์ชัน

| ฟังก์ชัน | รายละเอียด |
|----------|------------|
| `createActivity(array $activity): int` | สร้างกิจกรรม |
| `getActivityById(int $activityId): ?array` | ดึงกิจกรรมตาม ID |
| `getActivitiesByOwner(int $ownerId): array` | กิจกรรมของเจ้าของ |
| `searchActivities(?string $keyword, ?string $startDate, ?string $endDate): array` | ค้นหากิจกรรม |
| `updateActivity(int $activityId, array $activity): bool` | แก้ไขกิจกรรม |
| `deleteActivity(int $activityId, int $ownerId): bool` | ลบกิจกรรม |
| `addActivityImage(int $activityId, string $imagePath): bool` | เพิ่มรูปภาพ |
| `getActivityImages(int $activityId): array` | ดึงรูปภาพกิจกรรม |
| `deleteActivityImage(int $imageId, int $activityId): bool` | ลบรูปภาพ |

---

### createActivity(array $activity): int

**สร้างกิจกรรมใหม่**

```php
function createActivity(array $activity): int
{
    $conn = getConnection();
    $sql = 'INSERT INTO activities (title, detail, start_date, end_date, location, owner_id) 
            VALUES (?, ?, ?, ?, ?, ?)';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sssssi', 
        $activity['title'], 
        $activity['detail'], 
        $activity['start_date'], 
        $activity['end_date'], 
        $activity['location'], 
        $activity['owner_id']
    );
    $stmt->execute();
    $activityId = $stmt->insert_id;
    $stmt->close();
    $conn->close();
    return $activityId;
}
```

**Parameters:**
| ตัวแปร | ชนิด | รายละเอียด |
|--------|------|------------|
| `$activity['title']` | string | ชื่อกิจกรรม |
| `$activity['detail']` | string | รายละเอียด |
| `$activity['start_date']` | string | วันที่เริ่ม (Y-m-d H:i:s) |
| `$activity['end_date']` | string | วันที่สิ้นสุด |
| `$activity['location']` | string | สถานที่ |
| `$activity['owner_id']` | int | ID เจ้าของ |

**Returns:** `int` - activity_id ที่สร้าง

---

### getActivityById(int $activityId): ?array

**ดึงข้อมูลกิจกรรม (พร้อมชื่อเจ้าของ)**

```php
function getActivityById(int $activityId): ?array
{
    $conn = getConnection();
    $sql = 'SELECT a.*, u.full_name as owner_name, u.email as owner_email 
            FROM activities a 
            JOIN users u ON a.owner_id = u.user_id 
            WHERE a.activity_id = ?';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $activityId);
    $stmt->execute();
    $result = $stmt->get_result();
    $activity = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
    return $activity ?: null;
}
```

**Returns:** `?array`
```php
[
    'activity_id' => 1,
    'title' => 'Camping',
    'detail' => '...',
    'start_date' => '2024-03-15 08:00:00',
    'end_date' => '2024-03-16 18:00:00',
    'location' => 'เขาใหญ่',
    'owner_id' => 1,
    'owner_name' => 'John Doe',
    'owner_email' => 'john@example.com',
    'created_at' => '2024-01-15 10:30:00'
]
```

---

### getActivitiesByOwner(int $ownerId): array

**ดึงกิจกรรมทั้งหมดของเจ้าของ**

```php
function getActivitiesByOwner(int $ownerId): array
{
    $conn = getConnection();
    $sql = 'SELECT * FROM activities WHERE owner_id = ? ORDER BY created_at DESC';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $ownerId);
    $stmt->execute();
    $result = $stmt->get_result();
    $activities = [];
    while ($row = $result->fetch_assoc()) {
        $activities[] = $row;
    }
    $stmt->close();
    $conn->close();
    return $activities;
}
```

**Returns:** `array` - array ของกิจกรรม (ว่าง = ไม่มีกิจกรรม)

---

### searchActivities(?string $keyword, ?string $startDate, ?string $endDate): array

**ค้นหากิจกรรมแบบ Dynamic Query**

```php
function searchActivities(
    ?string $keyword = null, 
    ?string $startDate = null, 
    ?string $endDate = null
): array {
    $conn = getConnection();
    
    // Base query
    $sql = 'SELECT a.*, u.full_name as owner_name 
            FROM activities a 
            JOIN users u ON a.owner_id = u.user_id 
            WHERE 1=1';
    $params = [];
    $types = '';
    
    // Add keyword filter
    if ($keyword) {
        $sql .= ' AND (a.title LIKE ? OR a.detail LIKE ? OR a.location LIKE ?)';
        $keyword = '%' . $keyword . '%';
        $params[] = $keyword;
        $params[] = $keyword;
        $params[] = $keyword;
        $types .= 'sss';
    }
    
    // Add date filters
    if ($startDate) {
        $sql .= ' AND a.start_date >= ?';
        $params[] = $startDate;
        $types .= 's';
    }
    if ($endDate) {
        $sql .= ' AND a.end_date <= ?';
        $params[] = $endDate;
        $types .= 's';
    }
    
    $sql .= ' ORDER BY a.start_date DESC';
    
    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $activities = [];
    while ($row = $result->fetch_assoc()) {
        $activities[] = $row;
    }
    $stmt->close();
    $conn->close();
    return $activities;
}
```

**Parameters:**
| ตัวแปร | ชนิด | ค่าเริ่มต้น | รายละเอียด |
|--------|------|------------|------------|
| `$keyword` | ?string | null | ค้นหาใน title, detail, location |
| `$startDate` | ?string | null | วันที่เริ่มต้น (>=) |
| `$endDate` | ?string | null | วันที่สิ้นสุด (<=) |

**Returns:** `array`

**Usage Examples:**
```php
// ค้นหาทั้งหมด
$all = searchActivities();

// ค้นหาด้วยคำค้น
$withKeyword = searchActivities('camping');

// ค้นหาด้วยช่วงวัน
$withDates = searchActivities(null, '2024-03-01', '2024-03-31');

// ค้นหาทั้งหมด
$full = searchActivities('camping', '2024-03-01', '2024-03-31');
```

---

### updateActivity(int $activityId, array $activity): bool

**แก้ไขกิจกรรม (พร้อมตรวจสอบเจ้าของ)**

```php
function updateActivity(int $activityId, array $activity): bool
{
    $conn = getConnection();
    $sql = 'UPDATE activities 
            SET title = ?, detail = ?, start_date = ?, end_date = ?, location = ? 
            WHERE activity_id = ? AND owner_id = ?';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sssssii', 
        $activity['title'], 
        $activity['detail'], 
        $activity['start_date'], 
        $activity['end_date'], 
        $activity['location'], 
        $activityId, 
        $activity['owner_id']
    );
    $result = $stmt->execute();
    $affectedRows = $stmt->affected_rows;
    $stmt->close();
    $conn->close();
    return $result && $affectedRows > 0;
}
```

**Security:** มี `AND owner_id = ?` เพื่อให้แค่เจ้าของแก้ไขได้

**Returns:** `bool` - true ถ้ามีการอัพเดทจริง (affected_rows > 0)

---

### deleteActivity(int $activityId, int $ownerId): bool

**ลบกิจกรรม**

```php
function deleteActivity(int $activityId, int $ownerId): bool
{
    $conn = getConnection();
    $sql = 'DELETE FROM activities WHERE activity_id = ? AND owner_id = ?';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $activityId, $ownerId);
    $result = $stmt->execute();
    $affectedRows = $stmt->affected_rows;
    $stmt->close();
    $conn->close();
    return $result && $affectedRows > 0;
}
```

**Note:** เนื่องจากมี `ON DELETE CASCADE` ข้อมูลที่เกี่ยวข้องจะถูกลบอัตโนมัติ:
- `activity_images` - รูปภาพ
- `registrations` - การลงทะเบียน

---

### ฟังก์ชันรูปภาพ

```php
// เพิ่มรูปภาพ
function addActivityImage(int $activityId, string $imagePath): bool
{
    $conn = getConnection();
    $sql = 'INSERT INTO activity_images (image_path, activity_id) VALUES (?, ?)';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $imagePath, $activityId);
    $result = $stmt->execute();
    $stmt->close();
    $conn->close();
    return $result;
}

// ดึงรูปภาพทั้งหมด
function getActivityImages(int $activityId): array
{
    $conn = getConnection();
    $sql = 'SELECT * FROM activity_images WHERE activity_id = ?';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $activityId);
    $stmt->execute();
    $result = $stmt->get_result();
    $images = [];
    while ($row = $result->fetch_assoc()) {
        $images[] = $row;
    }
    $stmt->close();
    $conn->close();
    return $images;
}

// ลบรูปภาพ (ตรวจสอบ activity_id เพื่อความปลอดภัย)
function deleteActivityImage(int $imageId, int $activityId): bool
{
    $conn = getConnection();
    $sql = 'DELETE FROM activity_images WHERE image_id = ? AND activity_id = ?';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $imageId, $activityId);
    $result = $stmt->execute();
    $affectedRows = $stmt->affected_rows;
    $stmt->close();
    $conn->close();
    return $result && $affectedRows > 0;
}
```

---

## 📝 registrations.php

### รายการฟังก์ชัน

| ฟังก์ชัน | รายละเอียด |
|----------|------------|
| `createRegistration(int $activityId, int $userId): bool` | สร้างการลงทะเบียน |
| `getRegistrationById(int $regId): ?array` | ดึงการลงทะเบียน |
| `getRegistrationsByActivity(int $activityId): array` | ดึงทั้งหมดของกิจกรรม |
| `getRegistrationsByUser(int $userId): array` | ดึงทั้งหมดของผู้ใช้ |
| `updateRegistrationStatus(int $regId, string $status, int $ownerId): bool` | อัพเดทสถานะ |
| `checkInRegistration(int $regId, int $ownerId): bool` | เช็คชื่อ |
| `getRegistrationStats(int $activityId): array` | สถิติการลงทะเบียน |
| `isUserRegistered(int $activityId, int $userId): ?array` | ตรวจสอบการลงทะเบียน |

---

### createRegistration(int $activityId, int $userId): bool

```php
function createRegistration(int $activityId, int $userId): bool
{
    $conn = getConnection();
    $sql = 'INSERT INTO registrations (activity_id, user_id, status) 
            VALUES (?, ?, "pending")';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $activityId, $userId);
    try {
        $result = $stmt->execute();
    } catch (mysqli_sql_exception $e) {
        $result = false;  // Duplicate entry = ลงทะเบียนซ้ำ
    }
    $stmt->close();
    $conn->close();
    return $result;
}
```

**Note:** ถ้าลงทะเบียนซ้ำจะ throw exception (catch แล้ว return false)

---

### getRegistrationStats(int $activityId): array

**สถิติการลงทะเบียน (สำหรับกราฟ)**

```php
function getRegistrationStats(int $activityId): array
{
    $conn = getConnection();
    $stats = [];
    
    // Total
    $sql = 'SELECT COUNT(*) as total FROM registrations WHERE activity_id = ?';
    // ... คล้ายๆ กัน ...
    $stats['total'] = ...
    
    // Pending, Approved, Rejected, Checked_in
    // ใช้แยก query เพื่อความชัดเจน
    
    // Gender distribution (only approved)
    $sql = 'SELECT u.gender, COUNT(*) as count 
            FROM registrations r 
            JOIN users u ON r.user_id = u.user_id 
            WHERE r.activity_id = ? AND r.status = "approved" 
            GROUP BY u.gender';
    // Returns: ['male' => 10, 'female' => 15, 'other' => 2]
    
    // Age groups (only approved)
    $sql = 'SELECT 
            CASE 
                WHEN TIMESTAMPDIFF(YEAR, u.birthday, CURDATE()) < 18 THEN "under18"
                WHEN TIMESTAMPDIFF(YEAR, u.birthday, CURDATE()) BETWEEN 18 AND 25 THEN "18-25"
                WHEN TIMESTAMPDIFF(YEAR, u.birthday, CURDATE()) BETWEEN 26 AND 35 THEN "26-35"
                WHEN TIMESTAMPDIFF(YEAR, u.birthday, CURDATE()) BETWEEN 36 AND 50 THEN "36-50"
                ELSE "over50"
            END as age_group,
            COUNT(*) as count 
            FROM registrations r 
            JOIN users u ON r.user_id = u.user_id 
            WHERE r.activity_id = ? AND r.status = "approved" 
            GROUP BY age_group';
    
    $conn->close();
    return $stats;
}
```

**Returns:**
```php
[
    'total' => 50,
    'pending' => 10,
    'approved' => 35,
    'rejected' => 5,
    'checked_in' => 20,
    'gender' => [
        'male' => 15,
        'female' => 18,
        'other' => 2
    ],
    'age_groups' => [
        'under18' => 5,
        '18-25' => 12,
        '26-35' => 10,
        '36-50' => 6,
        'over50' => 2
    ]
]
```

---

## 🛠️ helpers.php

### OTP Functions (Stateless OTP)

```php
const OTP_SECRET_KEY = 'fastcamp_secret_key_2024';
const OTP_EXPIRY_SECONDS = 1800;  // 30 นาที
const OTP_LENGTH = 6;
```

#### generateStatelessOtp(int $userId, int $activityId): array

```php
function generateStatelessOtp(int $userId, int $activityId): array
{
    $timeWindow = floor(time() / OTP_EXPIRY_SECONDS);
    $data = "{$userId}:{$activityId}:{$timeWindow}";
    $hash = hash_hmac('sha256', $data, OTP_SECRET_KEY);
    $decimal = hexdec(substr($hash, 0, 16));
    $otp = str_pad((string)($decimal % 1000000), OTP_LENGTH, '0', STR_PAD_LEFT);
    $expiresAt = (int)(($timeWindow + 1) * OTP_EXPIRY_SECONDS);
    $expiresIn = $expiresAt - time();
    
    return [
        'code' => $otp,           // 6 digits
        'expires_at' => date('Y-m-d H:i:s', $expiresAt),
        'expires_in' => $expiresIn  // seconds
    ];
}
```

**Algorithm:**
1. คำนวณ time window (30 นาทีต่อ 1 window)
2. สร้าง data string: `"{userId}:{activityId}:{window}"`
3. HMAC-SHA256 ด้วย secret key
4. เอา 16 chars แรก แปลงเป็น decimal
5. Modulo 1,000,000 ได้เลข 6 หลัก
6. Pad ด้วย 0 ข้างหน้าถ้าจำเป็น

---

#### verifyStatelessOtp(string $otpCode, int $userId, int $activityId): bool

```php
function verifyStatelessOtp(string $otpCode, int $userId, int $activityId): bool
{
    $otpCode = strtolower(trim($otpCode));
    $currentWindow = floor(time() / OTP_EXPIRY_SECONDS);
    $windows = [$currentWindow, $currentWindow - 1];  // Grace period
    
    foreach ($windows as $window) {
        $data = "{$userId}:{$activityId}:{$window}";
        $hash = hash_hmac('sha256', $data, OTP_SECRET_KEY);
        $decimal = hexdec(substr($hash, 0, 16));
        $expectedOtp = str_pad((string)($decimal % 1000000), OTP_LENGTH, '0', STR_PAD_LEFT);
        
        if (hash_equals($expectedOtp, $otpCode)) {
            return true;  // ตรงกัน (constant-time comparison)
        }
    }
    return false;
}
```

**Security Features:**
- `hash_equals()` - Constant-time comparison (ป้องกัน timing attack)
- Grace period - ตรวจสอบ window ปัจจุบัน และ window ก่อนหน้า (สำหรับ OTP ที่เกือบหมดอายุ)
- Stateless - ไม่ต้องเก็บในฐานข้อมูล

---

#### getAvatarUrl(string $seed, string $style = 'micah'): string

```php
function getAvatarUrl(string $seed, string $style = 'micah'): string
{
    return "https://api.dicebear.com/9.x/{$style}/svg?seed=" . urlencode($seed);
}
```

**Usage:**
```php
$avatar = getAvatarUrl('john@example.com');
// https://api.dicebear.com/9.x/micah/svg?seed=john%40example.com
```

---

<div align="center">
  <p><a href="03-ROUTES.md">← Routes</a> | <a href="05-SECURITY.md">ถัดไป: Security →</a></p>
</div>
