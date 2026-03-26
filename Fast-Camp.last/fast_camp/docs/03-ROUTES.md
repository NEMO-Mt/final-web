# 🛣️ เส้นทาง (Routes) FAST CAMP

> Complete Routes Documentation - รายการทุกเส้นทางในระบบ

---

## 📋 สารบัญ

1. [ภาพรวม Routes](#-ภาพรวม-routes)
2. [Public Routes (ไม่ต้อง Login)](#-public-routes-ไม่ต้อง-login)
3. [Protected Routes (ต้อง Login)](#-protected-routes-ต้อง-login)
4. [Owner-only Routes (ต้องเป็นเจ้าของ)](#-owner-only-routes-ต้องเป็นเจ้าของ)
5. [API Routes (JSON Response)](#-api-routes-json-response)
6. [รายละเอียดแต่ละ Route](#-รายละเอียดแต่ละ-route)
7. [Error Handling](#-error-handling)

---

## 📊 ภาพรวม Routes

### สรุปตามกลุ่ม

| กลุ่ม | จำนวน | Routes |
|-------|-------|--------|
| 🔓 Public | 2 | `/login`, `/register` |
| 🔒 Protected | 11 | `/home`, `/profile`, `/create`, `/activity/{id}`, `/my_activities`, etc. |
| 👤 Owner Only | 5 | `/edit/{id}`, `/delete`, `/approve`, `/checkin`, `/stats` |
| 🌐 API | 2 | `/ping`, `/generate_otp` |
| **รวม** | **20** | |

---

## 🔓 Public Routes (ไม่ต้อง Login)

| Method | URL | ไฟล์ | คำอธิบาย |
|--------|-----|------|----------|
| GET/POST | `/login` | `login.php` | หน้าเข้าสู่ระบบ |
| GET/POST | `/register` | `register.php` | หน้าสมัครสมาชิก |

---

## 🔒 Protected Routes (ต้อง Login)

| Method | URL | ไฟล์ | คำอธิบาย |
|--------|-----|------|----------|
| GET | `/home` | `home.php` | หน้าหลัก + ค้นหากิจกรรม |
| GET | `/activity/{id}` | `activity.php` | ดูรายละเอียดกิจกรรม |
| GET | `/profile` | `profile.php` | ดูโปรไฟล์ตัวเอง |
| GET/POST | `/edit_profile` | `edit_profile.php` | แก้ไขโปรไฟล์ |
| GET | `/my_activities` | `my_activities.php` | กิจกรรมที่สร้าง/เข้าร่วม |
| GET/POST | `/create` | `create.php` | สร้างกิจกรรมใหม่ |
| GET/POST | `/register_activity` | `register_activity.php` | ลงทะเบียนกิจกรรม |
| GET | `/logout` | `logout.php` | ออกจากระบบ |

---

## 👤 Owner-only Routes (ต้องเป็นเจ้าของกิจกรรม)

| Method | URL | ไฟล์ | คำอธิบาย |
|--------|-----|------|----------|
| GET/POST | `/edit/{id}` | `edit.php` | แก้ไขกิจกรรม |
| POST | `/delete` | `delete.php` | ลบกิจกรรม |
| POST | `/approve` | `approve.php` | อนุมัติ/ปฏิเสธการลงทะเบียน |
| GET/POST | `/checkin/{activity_id}` | `checkin.php` | เช็คชื่อผู้เข้าร่วม |
| GET | `/stats/{activity_id}` | `stats.php` | ดูสถิติกิจกรรม |

---

## 🌐 API Routes (JSON Response)

| Method | URL | ไฟล์ | คำอธิบาย |
|--------|-----|------|----------|
| GET | `/ping` | `ping.php` | Health check |
| GET | `/generate_otp` | `generate_otp.php` | สร้าง OTP สำหรับเช็คชื่อ |

---

## 📖 รายละเอียดแต่ละ Route

### 🔐 Authentication Routes

---

#### `/login` - เข้าสู่ระบบ

**ไฟล์:** `routes/login.php`

| | รายละเอียด |
|----|------------|
| **Methods** | GET, POST |
| **Auth Required** | ไม่ต้อง (ถ้า login แล้ว redirect ไป /home) |
| **Template** | `login.php` |

**GET /login:**
- แสดงฟอร์มเข้าสู่ระบบ
- ถ้ามี `?registered=1` แสดงข้อความ "สมัครสมาชิกสำเร็จ"

**POST /login:**
```php
Input:  email, password
Flow:   authenticateUser(email, password)
        → ถ้าสำเร็จ: $_SESSION['user_id', 'user_email', 'user_name']
        → redirect /home
        → ถ้าไม่สำเร็จ: $error = 'อีเมลหรือรหัสผ่านไม่ถูกต้อง'
```

**Validate:**
- `email` - ต้องกรอก
- `password` - ต้องกรอก

---

#### `/register` - สมัครสมาชิก

**ไฟล์:** `routes/register.php`

| | รายละเอียด |
|----|------------|
| **Methods** | GET, POST |
| **Auth Required** | ไม่ต้อง |
| **Template** | `register.php` |

**GET /register:**
- แสดงฟอร์มสมัครสมาชิก

**POST /register:**
```php
Input:  email, password, confirm_password, full_name, 
        birthday, gender, occupation, phone
Flow:   validate fields
        → check password == confirm_password
        → check password length >= 6
        → check email unique (getUserByEmail)
        → createUser(data)
        → redirect /login?registered=1
```

**Validation Rules:**
| Field | Rule |
|-------|------|
| email | ต้องไม่ซ้ำ |
| password | อย่างน้อย 6 ตัว |
| password | == confirm_password |
| อื่นๆ | ต้องไม่ว่าง |

---

#### `/logout` - ออกจากระบบ

**ไฟล์:** `routes/logout.php`

| | รายละเอียด |
|----|------------|
| **Methods** | GET |
| **Auth Required** | ไม่ตรวจสอบ |

**Flow:**
```php
session_destroy();
header('Location: /login');
```

---

### 👤 User Routes

---

#### `/profile` - โปรไฟล์ผู้ใช้

**ไฟล์:** `routes/profile.php`

| | รายละเอียด |
|----|------------|
| **Methods** | GET |
| **Auth Required** | ✅ ต้อง Login |
| **Template** | `profile.php` |

**Flow:**
```php
$user = getUserById($_SESSION['user_id']);
$createdActivities = getActivitiesByOwner($_SESSION['user_id']);
$myRegistrations = getRegistrationsByUser($_SESSION['user_id']);
$age = calculateAge($user['birthday']);

renderView('profile', [
    'user' => $user,
    'age' => $age,
    'createdCount' => count($createdActivities),
    'registrationCount' => count($myRegistrations),
    'registrations' => $myRegistrations
]);
```

---

#### `/edit_profile` - แก้ไขโปรไฟล์

**ไฟล์:** `routes/edit_profile.php`

| | รายละเอียด |
|----|------------|
| **Methods** | GET, POST |
| **Auth Required** | ✅ |
| **Template** | `edit_profile.php` |

**POST /edit_profile:**
```php
Input:  full_name, birthday, gender, occupation, phone, profile_image (file)
Flow:   validate fields
        → uploadProfileImage (if provided)
        → updateUser(userId, data)
        → $_SESSION['user_name'] = full_name
        → redirect /profile?updated=1
```

**File Upload:**
- Allowed: jpg, png, gif, webp
- Max: 5MB
- Path: `uploads/avatars/user_{id}_{timestamp}.{ext}`

---

#### `/my_activities` - กิจกรรมของฉัน

**ไฟล์:** `routes/my_activities.php`

| | รายละเอียด |
|----|------------|
| **Methods** | GET |
| **Auth Required** | ✅ |
| **Template** | `my_activities.php` |

**Flow:**
```php
$myActivities = getActivitiesByOwner($_SESSION['user_id']);
$myRegistrations = getRegistrationsByUser($_SESSION['user_id']);

renderView('my_activities', [
    'myActivities' => $myActivities,
    'myRegistrations' => $myRegistrations
]);
```

---

### 📅 Activity Routes

---

#### `/home` - หน้าหลัก (ค้นหากิจกรรม)

**ไฟล์:** `routes/home.php`

| | รายละเอียด |
|----|------------|
| **Methods** | GET |
| **Auth Required** | ✅ |
| **Template** | `home.php` |

**Query Parameters:**
```
GET /home?keyword=เขาใหญ่&start_date=2024-03-01&end_date=2024-03-31
```

| Param | รายละเอียด |
|-------|------------|
| `keyword` | ค้นหาใน title, detail, location |
| `start_date` | วันที่เริ่มต้น (>=) |
| `end_date` | วันที่สิ้นสุด (<=) |

**Flow:**
```php
$activities = searchActivities(
    $keyword ?: null,
    $startDate ?: null,
    $endDate ?: null
);
// เรียงตาม start_date DESC
```

---

#### `/activity/{id}` - ดูรายละเอียดกิจกรรม

**ไฟล์:** `routes/activity.php`

| | รายละเอียด |
|----|------------|
| **Methods** | GET |
| **Auth Required** | ✅ |
| **Template** | `activity_detail.php` |
| **Pattern** | `/^activity\/(\d+)$/`

**Parameters:**
```php
$_GET['id'] = 123;  // จาก URL /activity/123
```

**Flow:**
```php
$activity = getActivityById($activityId);
$isOwner = ($activity['owner_id'] == $_SESSION['user_id']);
$registration = isUserRegistered($activityId, $_SESSION['user_id']);
$images = getActivityImages($activityId);

if ($isOwner) {
    $registrations = getRegistrationsByActivity($activityId);
    $stats = getRegistrationStats($activityId);
}

renderView('activity_detail', [
    'activity', 'isOwner', 'registration', 'images',
    'registrations', 'stats', 'created', 'registered', 'userId'
]);
```

**Query Flags:**
| Flag | คำอธิบาย |
|------|----------|
| `?created=1` | แสดง "สร้างกิจกรรมสำเร็จ" |
| `?registered=1` | แสดง "ลงทะเบียนสำเร็จ" |

---

#### `/create` - สร้างกิจกรรมใหม่

**ไฟล์:** `routes/create.php`

| | รายละเอียด |
|----|------------|
| **Methods** | GET, POST |
| **Auth Required** | ✅ |
| **Template** | `create_activity.php` |

**POST /create:**
```php
Input:  title, detail, start_date, end_date, location, images[] (files)
Validate:
    - ทุก field ต้องไม่ว่าง
    - start_date <= end_date
Flow:
    $activityId = createActivity([
        'title' => $title,
        'detail' => $detail,
        'start_date' => $startDate,
        'end_date' => $endDate,
        'location' => $location,
        'owner_id' => $_SESSION['user_id']
    ]);
    
    if (มีรูปภาพ) {
        uploadImages($_FILES['images'], $activityId);
    }
    
    redirect /activity/{id}?created=1
```

**File Upload:**
- Allowed: jpeg, png, gif, webp
- Max: 5MB ต่อไฟล์
- Path: `uploads/activity_{id}_{timestamp}_{index}.{ext}`

---

#### `/edit/{id}` - แก้ไขกิจกรรม

**ไฟล์:** `routes/edit.php`

| | รายละเอียด |
|----|------------|
| **Methods** | GET, POST |
| **Auth Required** | ✅ + ต้องเป็น Owner |
| **Template** | `edit_activity.php` |
| **Pattern** | `/^edit\/(\d+)$/`

**Security Check:**
```php
$activity = getActivityById($activityId);
if (!$activity || $activity['owner_id'] != $_SESSION['user_id']) {
    header('Location: /home');  // ไม่ใช่เจ้าของ
}
```

**POST /edit/{id}:**
```php
Input:  title, detail, start_date, end_date, location, images[] (files)
Validate: เหมือน /create
Flow:
    updateActivity($activityId, [data]);
    uploadImages($_FILES['images'], $activityId);
    redirect /activity/{id}?updated=1
```

---

#### `/delete` - ลบกิจกรรม

**ไฟล์:** `routes/delete.php`

| | รายละเอียด |
|----|------------|
| **Methods** | POST |
| **Auth Required** | ✅ + ต้องเป็น Owner |
| **Pattern** | `/^delete\/(\d+)$/` |

**POST /delete:**
```php
Input:  id (from $_POST)
Flow:
    $activity = getActivityById($activityId);
    if ($activity['owner_id'] != $_SESSION['user_id']) {
        redirect /home;
    }
    deleteActivity($activityId, $_SESSION['user_id']);
    redirect /home?deleted=1
```

**Note:** ตาราง `activities` มี `ON DELETE CASCADE` ไปยัง:
- `activity_images` - รูปภาพจะถูกลบอัตโนมัติ
- `registrations` - การลงทะเบียนจะถูกลบอัตโนมัติ

---

### 📝 Registration Routes

---

#### `/register_activity` - ลงทะเบียนเข้าร่วม

**ไฟล์:** `routes/register_activity.php`

| | รายละเอียด |
|----|------------|
| **Methods** | POST |
| **Auth Required** | ✅ |

**POST /register_activity:**
```php
Input:  activity_id (from $_POST)
Validate:
    - กิจกรรมต้องมีอยู่จริง
    - ไม่ใช่เจ้าของ (owner ห้ามลงทะเบียนกิจกรรมตัวเอง)
    - ยังไม่ได้ลงทะเบียน (unique_registration constraint)
Flow:
    createRegistration($activityId, $_SESSION['user_id']);
    // status เริ่มต้น = 'pending'
    redirect /activity/{id}?registered=1
```

**Error Cases:**
| Error | Query Param |
|-------|-------------|
| เจ้าของลงทะเบียนเอง | `?error=self_register` |
| ลงทะเบียนซ้ำ | `?error=already_registered` |
| เกิดข้อผิดพลาด | `?error=register` |

---

#### `/approve` - อนุมัติ/ปฏิเสธการลงทะเบียน

**ไฟล์:** `routes/approve.php`

| | รายละเอียด |
|----|------------|
| **Methods** | POST |
| **Auth Required** | ✅ + ต้องเป็น Owner |

**POST /approve:**
```php
Input:  reg_id, action ('approve' | 'reject')
Flow:
    $status = ($action === 'approve') ? 'approved' : 'rejected';
    updateRegistrationStatus($regId, $status, $_SESSION['user_id']);
    // SQL มี JOIN เช็คว่า user เป็น owner ของกิจกรรมนั้น
    redirect /activity/{id}?{action}=1
```

---

### 📊 Owner Management Routes

---

#### `/stats/{activity_id}` - สถิติกิจกรรม

**ไฟล์:** `routes/stats.php`

| | รายละเอียด |
|----|------------|
| **Methods** | GET |
| **Auth Required** | ✅ + ต้องเป็น Owner |
| **Template** | `stats.php` |
| **Pattern** | `/^stats\/(\d+)$/` |

**Flow:**
```php
$stats = getRegistrationStats($activityId);
// Returns:
// - total, pending, approved, rejected, checked_in
// - gender: {male, female, other}
// - age_groups: {under18, 18-25, 26-35, 36-50, over50}

$registrations = getRegistrationsByActivity($activityId);

renderView('stats', ['activity', 'stats', 'registrations']);
```

---

#### `/checkin/{activity_id}` - เช็คชื่อผู้เข้าร่วม

**ไฟล์:** `routes/checkin.php`

| | รายละเอียด |
|----|------------|
| **Methods** | GET, POST |
| **Auth Required** | ✅ + ต้องเป็น Owner |
| **Template** | `checkin.php` |
| **Pattern** | `/^checkin\/(\d+)$/` |

**GET /checkin/{activity_id}:**
```php
// ดึงรายชื่อผู้ที่ยังไม่ได้เช็คชื่อ
$pendingCheckIns = [];
$allRegistrations = getRegistrationsByActivity($activityId);
foreach ($allRegistrations as $reg) {
    if ($reg['status'] === 'approved' && !$reg['is_checkin']) {
        $pendingCheckIns[] = $reg;
    }
}
renderView('checkin', ['pendingCheckIns', 'activityId']);
```

**POST /checkin/{activity_id}:**
```php
Input:  otp (from $_POST)
Flow:
    // หา registration ที่ OTP ตรงกัน
    foreach ($allRegistrations as $reg) {
        if ($reg['status'] === 'approved' && !$reg['is_checkin']) {
            if (verifyStatelessOtp($otp, $reg['user_id'], $activityId)) {
                checkInRegistration($reg['reg_id'], $_SESSION['user_id']);
                $success = 'เช็คชื่อ ' . $reg['full_name'] . ' สำเร็จ';
                break;
            }
        }
    }
```

---

### 🌐 API Routes

---

#### `/ping` - Health Check

**ไฟล์:** `routes/ping.php`

| | รายละเอียด |
|----|------------|
| **Methods** | GET |
| **Auth Required** | ไม่ต้อง |
| **Response** | `pong` |

---

#### `/generate_otp` - สร้าง OTP (AJAX API)

**ไฟล์:** `routes/generate_otp.php`

| | รายละเอียด |
|----|------------|
| **Methods** | GET |
| **Auth Required** | ✅ |
| **Response** | JSON |

**GET /generate_otp?activity_id=123:**
```php
Validate:
    - ผู้ใช้ต้องลงทะเบียนกิจกรรมนี้แล้ว
    - status ต้องเป็น 'approved'

Response:
{
    "success": true,
    "otp": "123456",
    "expires_in": 1800  // วินาที (30 นาที)
}

Error:
{
    "success": false,
    "error": "Not authenticated|Invalid activity|Not approved"
}
```

---

## ⚠️ Error Handling

### Redirect Patterns

| Error Type | Pattern | ตัวอย่าง |
|------------|---------|----------|
| Not Authenticated | → `/login` | ทุก protected route |
| Not Owner | → `/home` | edit, delete, approve |
| Invalid ID | → `/home` | activity/999 (ไม่มี) |
| Success | → `?{action}=1` | `?registered=1` |
| Error | → `?error={type}` | `?error=delete` |

### HTTP Status Codes

| Code | ใช้เมื่อ |
|------|---------|
| 404 | Route ไม่พบ |
| 302 | Redirect (default) |
| 200 | Success (API) |

---

## 🔀 Route Mapping Summary

```php
// ใน includes/router.php
$patterns = [
    '/^activity\/(\d+)$/'     => 'activity.php',      // /activity/123
    '/^edit\/(\d+)$/'         => 'edit.php',          // /edit/123
    '/^delete\/(\d+)$/'       => 'delete.php',        // /delete/123
    '/^stats\/(\d+)$/'        => 'stats.php',         // /stats/123
    '/^checkin\/(\d+)$/'      => 'checkin.php',       // /checkin/123
];

// Static routes (ตรงกับชื่อไฟล์)
$staticRoutes = [
    'home', 'login', 'register', 'profile', 'logout',
    'create', 'edit_profile', 'my_activities',
    'register_activity', 'approve', 'ping',
    'generate_otp'
];
```

---

<div align="center">
  <p><a href="02-DATABASE.md">← ฐานข้อมูล</a> | <a href="04-DATABASE-LAYER.md">ถัดไป: Database Layer →</a></p>
</div>
