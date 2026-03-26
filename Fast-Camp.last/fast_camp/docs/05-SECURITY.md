# 🔒 ความปลอดภัย FAST CAMP

> Security Documentation - รายละเอียดกลไกรักษาความปลอดภัยทั้งหมด

---

## 📋 สารบัญ

1. [ภาพรวมความปลอดภัย](#-ภาพรวมความปลอดภัย)
2. [การเข้ารหัสรหัสผ่าน](#-การเข้ารหัสรหัสผ่าน)
3. [SQL Injection Protection](#-sql-injection-protection)
4. [XSS Protection](#-xss-protection)
5. [CSRF & Session Security](#-csrf--session-security)
6. [Stateless OTP Security](#-stateless-otp-security)
7. [File Upload Security](#-file-upload-security)
8. [Access Control](#-access-control)
9. [Security Checklist](#-security-checklist)

---

## 🛡️ ภาพรวมความปลอดภัย

FAST CAMP ใช้มาตรการรักษาความปลอดภัยหลายชั้น:

```
┌─────────────────────────────────────────────────────────────┐
│                    SECURITY LAYERS                          │
├─────────────────────────────────────────────────────────────┤
│ Layer 1: Access Control                                     │
│   - Authentication (Session-based)                          │
│   - Authorization (Owner checks)                              │
├─────────────────────────────────────────────────────────────┤
│ Layer 2: Input Validation                                     │
│   - Empty checks                                            │
│   - Type casting (int)                                      │
│   - Date validation                                         │
├─────────────────────────────────────────────────────────────┤
│ Layer 3: Database Security                                    │
│   - Prepared Statements (SQL Injection protection)          │
│   - Password Hashing (bcrypt)                               │
│   - Foreign Key Constraints                                 │
├─────────────────────────────────────────────────────────────┤
│ Layer 4: Output Encoding                                      │
│   - htmlspecialchars() (XSS protection)                       │
│   - URL encoding                                            │
├─────────────────────────────────────────────────────────────┤
│ Layer 5: File Security                                        │
│   - MIME type validation                                    │
│   - File size limits                                        │
│   - Extension whitelist                                     │
└─────────────────────────────────────────────────────────────┘
```

---

## 🔐 การเข้ารหัสรหัสผ่าน

### Algorithm: bcrypt

```php
// การเข้ารหัส (ตอนสมัครสมาชิก)
$hashedPassword = password_hash($user['password'], PASSWORD_DEFAULT);
// ผลลัพธ์: $2y$10$N9qo8uLOickgx2ZMRZoMy.Mqr....

// การตรวจสอบ (ตอน login)
if (password_verify($password, $user['password'])) {
    // รหัสผ่านถูกต้อง
}
```

**รายละเอียด:**
| ค่า | รายละเอียด |
|-----|------------|
| Algorithm | bcrypt |
| Cost | 10 (default) |
| Salt | Auto-generated |
| Output | 60 characters |

**Location:** `databases/users.php:11`

---

## 🛡️ SQL Injection Protection

### Prepared Statements ทั้งหมด

```php
// ✅ SECURE - ใช้ Prepared Statements
$stmt = $conn->prepare('SELECT * FROM users WHERE email = ?');
$stmt->bind_param('s', $email);
$stmt->execute();

// ❌ NEVER DO THIS - ไม่ปลอดภัย
$result = $conn->query("SELECT * FROM users WHERE email = '$email'");
```

### Bind Parameter Types

| Type | PHP Type | SQL Type |
|------|----------|----------|
| `i` | int | INTEGER |
| `d` | float | DOUBLE |
| `s` | string | STRING |
| `b` | blob | BLOB |

**ตัวอย่างการ bind หลายพารามิเตอร์:**
```php
$stmt->bind_param('ssi', $string1, $string2, $int1);
$stmt->bind_param('ii', $int1, $int2);
$stmt->bind_param('sssssi', $s1, $s2, $s3, $s4, $s5, $i1);
```

**Spread operator สำหรับ dynamic parameters:**
```php
$params = [$keyword, $keyword, $keyword, $startDate];
$types = 'ssss';
$stmt->bind_param($types, ...$params);
```

---

## 🛡️ XSS Protection

### htmlspecialchars() ทุก Output

```php
// ✅ SECURE
<input value="<?php echo htmlspecialchars($email); ?>">
<h1><?php echo htmlspecialchars($activity['title']); ?></h1>

// ❌ NEVER DO THIS
<input value="<?php echo $email; ?>">
```

**Default flags ของ htmlspecialchars():**
```php
htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
```

**Flags ที่ใช้:**
| Flag | รายละเอียด |
|------|------------|
| `ENT_QUOTES` | แปลงทั้ง single และ double quotes |
| `ENT_SUBSTITUTE` | แทนที่ invalid code point ด้วย U+FFFD |
| `ENT_HTML5` | ใช้ HTML5 entities |

**ตัวอักษรที่ถูกแปลง:**
| ตัวอักษร | แปลงเป็น |
|---------|---------|
| `&` | `&amp;` |
| `"` | `&quot;` |
| `'` | `&#039;` |
| `<` | `&lt;` |
| `>` | `&gt;` |

---

## 🔒 CSRF & Session Security

### Session Configuration

```php
// public/index.php
session_start();
```

**Session Data Structure:**
```php
$_SESSION = [
    'user_id'    => 1,           // int - ID ผู้ใช้
    'user_email' => 'a@b.com',   // string - อีเมล
    'user_name'  => 'John Doe'   // string - ชื่อแสดง
];
```

**Note:** `password` ไม่ถูกเก็บใน session (ลบออกตอน authenticate)

### Session Security Best Practices

```php
// ตรวจสอบการเข้าสู่ระบบทุก protected route
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}
```

### Session Regeneration (แนะนำเพิ่ม)

```php
// ควรเพิ่มตอน login สำเร็จ
session_regenerate_id(true);
```

---

## 🔑 Stateless OTP Security

### Algorithm: HMAC-SHA256

```
┌─────────────────────────────────────────────────────────────┐
│                    OTP GENERATION FLOW                       │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  1. คำนวณ Time Window                                        │
│     $window = floor(time() / 1800)  // 30 นาที              │
│                                                             │
│  2. สร้าง Data String                                       │
│     $data = "{userId}:{activityId}:{window}"                 │
│     ตัวอย่าง: "123:456:289012"                               │
│                                                             │
│  3. HMAC-SHA256                                              │
│     $hash = hash_hmac('sha256', $data, OTP_SECRET_KEY)     │
│                                                             │
│  4. แปลงเป็นเลข 6 หลัก                                        │
│     $decimal = hexdec(substr($hash, 0, 16))                │
│     $otp = $decimal % 1000000                                │
│     $otp = str_pad($otp, 6, '0', STR_PAD_LEFT)             │
│                                                             │
│  5. ส่งคืน OTP + Expiration                                  │
│     return ['code' => $otp, 'expires_in' => 1800]          │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

### Security Features

1. **Stateless** - ไม่ต้องเก็บในฐานข้อมูล
2. **Time-based** - มีอายุจำกัด (30 นาที)
3. **Unique per user+activity** - OTP ผูกกับ user และกิจกรรม
4. **HMAC-SHA256** - Cryptographically secure
5. **Grace Period** - ตรวจสอบ window ปัจจุบัน + ก่อนหน้า (30 นาที)

### Verification

```php
function verifyStatelessOtp(string $otpCode, int $userId, int $activityId): bool
{
    $otpCode = strtolower(trim($otpCode));
    $currentWindow = floor(time() / OTP_EXPIRY_SECONDS);
    
    // ตรวจสอบ window ปัจจุบัน และ window ก่อนหน้า (grace period)
    $windows = [$currentWindow, $currentWindow - 1];
    
    foreach ($windows as $window) {
        $data = "{$userId}:{$activityId}:{$window}";
        $hash = hash_hmac('sha256', $data, OTP_SECRET_KEY);
        $decimal = hexdec(substr($hash, 0, 16));
        $expectedOtp = str_pad((string)($decimal % 1000000), OTP_LENGTH, '0', STR_PAD_LEFT);
        
        // Constant-time comparison (ป้องกัน timing attack)
        if (hash_equals($expectedOtp, $otpCode)) {
            return true;
        }
    }
    return false;
}
```

### ทำไมถึงปลอดภัย?

| คุณสมบัติ | รายละเอียด |
|-----------|------------|
| Stateless | ไม่มี database storage ที่ถูก hack ได้ |
| HMAC | ถ้าไม่รู้ secret key คำนวณ OTP ไม่ได้ |
| Time-bound | OTP หมดอายุ 30 นาที |
| User-specific | OTP ผูกกับ user ID + activity ID |
| hash_equals | ป้องกัน timing attack |

---

## 📁 File Upload Security

### Validation Steps

```php
function uploadImages(array $files, int $activityId): array
{
    $uploaded = [];
    $uploadDir = __DIR__ . '/../public/uploads/';
    
    // 1. สร้างโฟลเดอร์ถ้ายังไม่มี
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);  // ไม่ใช่ 0777 ในผลิต
    }
    
    // 2. Whitelist MIME types
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    
    // 3. File size limit
    $maxSize = 5 * 1024 * 1024; // 5MB
    
    foreach ($files['tmp_name'] as $key => $tmpName) {
        if (empty($tmpName)) continue;
        
        $fileType = $files['type'][$key];
        $fileSize = $files['size'][$key];
        
        // 4. Validate MIME type
        if (!in_array($fileType, $allowedTypes)) {
            continue;  // ข้ามไฟล์ที่ไม่ถูกต้อง
        }
        
        // 5. Validate size
        if ($fileSize > $maxSize) {
            continue;
        }
        
        // 6. Generate safe filename
        $ext = pathinfo($files['name'][$key], PATHINFO_EXTENSION);
        $filename = 'activity_' . $activityId . '_' . time() . '_' . $key . '.' . $ext;
        
        // 7. Move with proper path
        $filepath = $uploadDir . $filename;
        if (move_uploaded_file($tmpName, $filepath)) {
            $uploaded[] = $filename;
        }
    }
    
    return $uploaded;
}
```

### Security Measures

| ขั้นตอน | การป้องกัน |
|---------|-----------|
| MIME Type Whitelist | รับเฉพาะ image/jpeg, png, gif, webp |
| File Size Limit | สูงสุด 5MB |
| Filename Generation | ไม่ใช้ชื่อไฟล์จาก user (สร้างเอง) |
| Extension Check | ดึงจาก pathinfo() |
| move_uploaded_file() | ป้องกัน path traversal |
| Directory Permissions | 0755 (ไม่ใช่ 0777) |

### แนะนำเพิ่มเติม (Production)

```php
// 1. ตรวจสอบ magic bytes (ไม่ใช่แค่ MIME type)
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$realType = finfo_file($finfo, $tmpName);

// 2. สร้างไฟล์ใหม่จาก image processing (ลบ metadata)
$image = imagecreatefromjpeg($tmpName);
imagejpeg($image, $filepath, 85);

// 3. เก็บไฟล์นอก public directory
// ให้ PHP serve ผ่าน readfile() หลังตรวจสอบสิทธิ์
```

---

## 👤 Access Control

### Authentication Flow

```
User Request
    │
    ▼
┌─────────────────┐
│  ตรวจสอบ Session │
│  isset($_SESSION['user_id'])?
└────────┬────────┘
         │
    No ──┼──► redirect /login
         │
    Yes  ▼
    ┌─────────────────┐
    │  ตรวจสอบสิทธิ์   │
    │  (ถ้าจำเป็น)     │
    └────────┬────────┘
             │
        ┌────┴────┐
        ▼         ▼
   ผ่าน      ไม่ผ่าน
    │          │
    ▼          ▼
Proceed    redirect /home
```

### Authorization Patterns

#### Pattern 1: Login Required
```php
// ทุก protected route
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}
```

#### Pattern 2: Owner Only
```php
// สำหรับ edit, delete, approve, stats, checkin
$activity = getActivityById($activityId);
if ($activity['owner_id'] != $_SESSION['user_id']) {
    header('Location: /home');  // ไม่ใช่เจ้าของ
    exit;
}
```

#### Pattern 3: Registration Check
```php
// ตรวจสอบว่าลงทะเบียนแล้วหรือยัง
$registration = isUserRegistered($activityId, $_SESSION['user_id']);
if (!$registration || $registration['status'] !== 'approved') {
    // ไม่สามารถเข้าถึงฟีเจอร์ที่ต้อง approved ได้
}
```

### ตารางสิทธิ์

| Route | Auth | Owner | Registration |
|-------|------|-------|--------------|
| `/login` | - | - | - |
| `/register` | - | - | - |
| `/home` | ✓ | - | - |
| `/profile` | ✓ | - | - |
| `/create` | ✓ | - | - |
| `/activity/{id}` | ✓ | - | - |
| `/edit/{id}` | ✓ | ✓ | - |
| `/delete` | ✓ | ✓ | - |
| `/approve` | ✓ | ✓ | - |
| `/checkin/{id}` | ✓ | ✓ | - |
| `/stats/{id}` | ✓ | ✓ | - |
| `/register_activity` | ✓ | - | - |
| `/generate_otp` | ✓ | - | Approved |

---

## ✅ Security Checklist

### Development Phase

- [ ] ใช้ `declare(strict_types=1)` ทุกไฟล์
- [ ] ใช้ Prepared Statements ทุก query
- [ ] `htmlspecialchars()` ทุก output
- [ ] ตรวจสอบ session ทุก protected route
- [ ] ตรวจสอบ ownership ทุก owner route
- [ ] Validate file uploads
- [ ] ใช้ `password_hash()` สำหรับรหัสผ่าน
- [ ] ไม่เก็บ secret keys ใน git

### Production Phase

- [ ] เปลี่ยน OTP_SECRET_KEY
- [ ] เปลี่ยน database credentials
- [ ] ตั้งค่า HTTPS only
- [ ] Secure session cookies:
```php
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_samesite', 'Strict');
```
- [ ] จำกัด file upload directory (no PHP execution)
- [ ] ทำ database backups สม่ำเสมอ
- [ ] บันทึก logs การใช้งาน

### Apache .htaccess Security

```apache
# ป้องกัน directory listing
Options -Indexes

# ป้องกันการเข้าถึงไฟล์สำคัญ
<FilesMatch "\.(sql|log|ini)$">
    Order allow,deny
    Deny from all
</FilesMatch>

# Rewrite ทุกอย่างไป index.php
RewriteEngine on
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule . index.php [L]
```

---

## 🚨 Common Vulnerabilities ที่ป้องกันแล้ว

| Vulnerability | วิธีป้องกัน | สถานะ |
|---------------|------------|--------|
| SQL Injection | Prepared Statements | ✅ ป้องกัน |
| XSS | htmlspecialchars() | ✅ ป้องกัน |
| Password Cracking | bcrypt hashing | ✅ ป้องกัน |
| Session Hijacking | Session checks | ✅ ป้องกัน |
| Unauthorized Access | Owner validation | ✅ ป้องกัน |
| File Upload | MIME whitelist + size limit | ✅ ป้องกัน |
| Path Traversal | Safe filename generation | ✅ ป้องกัน |
| Timing Attack | hash_equals() | ✅ ป้องกัน |
| CSRF | (แนะนำเพิ่ม CSRF tokens) | ⚠️ ควรปรับปรุง |
| Brute Force | (แนะนำเพิ่ม rate limiting) | ⚠️ ควรปรับปรุง |

---

## 📚 References

- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [PHP Security Best Practices](https://www.php.net/manual/en/security.php)
- [HMAC RFC 2104](https://tools.ietf.org/html/rfc2104)

---

<div align="center">
  <p><a href="04-DATABASE-LAYER.md">← Database Layer</a> | <a href="06-SETUP.md">ถัดไป: การติดตั้ง →</a></p>
</div>
