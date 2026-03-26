# 📐 สถาปัตยกรรมระบบ FAST CAMP

> System Architecture Documentation

---

## 🎯 แนวคิดการออกแบบ (Design Principles)

FAST CAMP ใช้ **Custom MVC Pattern** ที่ออกแบบเองสำหรับ PHP แบบ Pure โดยไม่พึ่งพา Framework เพื่อ:

1. **เรียนรื่องง่าย** - โค้ดอ่านเข้าใจได้ทันทีไม่ต้องรู้ framework
2. **ควบคุมได้เต็มที่** - ไม่มี abstraction ที่ซ่อน logic
3. **เบาและเร็ว** - ไม่มี overhead จาก framework
4. **ง่ายต่อการดูแล** - โครงสร้างชัดเจน

---

## 🏗️ สถาปัตยกรรมแบบ MVC (Custom MVC)

```
┌─────────────────────────────────────────────────────────────────┐
│                        HTTP Request                             │
│                    (จาก Browser/User)                           │
└─────────────────────────┬───────────────────────────────────────┘
                          │
                          ▼
┌─────────────────────────────────────────────────────────────────┐
│                    📁 public/index.php                           │
│                    (Entry Point)                                │
│  • เริ่ม Session                                               │
│  • กำหนดค่าคงที่ (Constants)                                    │
│  • โหลด Core Files                                              │
└─────────────────────────┬───────────────────────────────────────┘
                          │
                          ▼
┌─────────────────────────────────────────────────────────────────┐
│                   📁 includes/router.php                         │
│                    (Router / Controller)                          │
│  • แยก URI และ Method                                           │
│  • ตรวจสอบ Pattern (Regex)                                       │
│  • Route ไปยังไฟล์ที่เหมาะสม                                     │
└─────────────────────────┬───────────────────────────────────────┘
                          │
                          ▼
┌─────────────────────────────────────────────────────────────────┐
│                    📁 routes/*.php                               │
│                    (Controllers)                                │
│  • รับ Request Data (GET/POST)                                  │
│  • เรียกใช้ Database Functions                                   │
│  • ประมวลผล Business Logic                                       │
│  • ส่ง Data ไป View                                              │
└─────────────────────────┬───────────────────────────────────────┘
                          │
                          ▼
┌─────────────────────────────────────────────────────────────────┐
│                   📁 databases/*.php                              │
│                    (Model / Database Layer)                       │
│  • CRUD Operations                                               │
│  • Business Logic ที่เกี่ยวกับข้อมูล                            │
│  • Connection Management                                         │
└─────────────────────────┬───────────────────────────────────────┘
                          │
                          ▼
┌─────────────────────────────────────────────────────────────────┐
│                  📁 includes/view.php                             │
│                    (View Renderer)                                │
│  • extract($data) - แปลง array เป็น variables                   │
│  • include template file                                         │
└─────────────────────────┬───────────────────────────────────────┘
                          │
                          ▼
┌─────────────────────────────────────────────────────────────────┐
│                   📁 templates/*.php                              │
│                    (Views - HTML Templates)                      │
│  • แสดงผล HTML                                                   │
│  • ใช้ Tailwind CSS                                              │
│  • PHP echo สำหรับแสดงข้อมูล                                      │
└─────────────────────────────────────────────────────────────────┘
                          │
                          ▼
┌─────────────────────────────────────────────────────────────────┐
│                      HTTP Response                               │
│                   (HTML/JSON กลับไป)                            │
└─────────────────────────────────────────────────────────────────┘
```

---

## 🔄 Flow การทำงานของ Request

### ตัวอย่าง: ผู้ใช้เข้าชมหน้ากิจกรรม

```
1. User: GET /activity/123
   │
   ▼
2. Apache + .htaccess
   │  RewriteRule → public/index.php
   ▼
3. public/index.php
   │  • session_start()
   │  • define constants
   │  • require includes
   ▼
4. includes/router.php - dispatch('/activity/123', 'GET')
   │  • normalizeUri('/activity/123') → 'activity/123'
   │  • preg_match('/^activity\/(\d+)$/') → match!
   │  • $_GET['id'] = 123
   │  • include routes/activity.php
   ▼
5. routes/activity.php (Controller)
   │  • ตรวจสอบ Session (login?)
   │  • $activityId = $_GET['id'] // 123
   │  • $activity = getActivityById(123)
   │  • $isOwner = ($activity['owner_id'] == $_SESSION['user_id'])
   │  • $registration = isUserRegistered(123, $userId)
   │  • $images = getActivityImages(123)
   │  • renderView('activity_detail', [...])
   ▼
6. includes/view.php - renderView('activity_detail', $data)
   │  • extract($data) → $activity, $isOwner, etc.
   │  • include templates/activity_detail.php
   ▼
7. templates/activity_detail.php
   │  • HTML Structure
   │  • echo $activity['title']
   │  • Tailwind CSS styling
   ▼
8. Response กลับไปยัง Browser
```

---

## 📊 องค์ประกอบหลัก (Core Components)

### 1. Entry Point (`public/index.php`)

```php
<?php
declare(strict_types=1);
session_start();

// Constants สำหรับ Paths
const INCLUDES_DIR = __DIR__ . '/../includes';
const ROUTE_DIR = __DIR__ . '/../routes';
const TEMPLATES_DIR = __DIR__ . '/../templates';
const DATABASES_DIR = __DIR__ . '/../databases';

// Load Core
require_once INCLUDES_DIR . '/router.php';
require_once INCLUDES_DIR . '/view.php';
require_once INCLUDES_DIR . '/database.php';

// Dispatch Request
dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
```

**หน้าที่:**
- เริ่มต้น Session
- กำหนดค่าคงที่สำหรับ paths (ใช้ `__DIR__` ให้ยืดหยุ่น)
- โหลดไฟล์ Core ที่จำเป็น
- ส่ง Request ไป Router

---

### 2. Router (`includes/router.php`)

```php
// ฟังก์ชันหลัก
function dispatch(string $uri, string $method): void
function normalizeUri(string $uri): string
function notFound(): void
function getFilePath(string $uri): string
```

**Routing Logic:**

| Pattern | Route File | Parameters |
|---------|------------|------------|
| `/` หรือ `/home` | `home.php` | - |
| `/activity/123` | `activity.php` | `$_GET['id'] = 123` |
| `/edit/123` | `edit.php` | `$_GET['id'] = 123` |
| `/delete/123` | `delete.php` | `$_GET['id'] = 123` |
| `/stats/123` | `stats.php` | `$_GET['activity_id'] = 123` |
| `/checkin/123` | `checkin.php` | `$_GET['activity_id'] = 123` |
| `/login`, `/register`, etc. | `{name}.php` | - |

**Regex Patterns ใน Router:**
```php
// Pattern: /activity/{id}
'/^activity\/(\d+)$/'

// Pattern: /edit/{id}
'/^edit\/(\d+)$/'

// Pattern: /delete/{id}
'/^delete\/(\d+)$/'
```

---

### 3. View Renderer (`includes/view.php`)

```php
function renderView(string $template, array $data = []): void
{
    extract($data);           // แปลง ['user' => $user] → $user
    include TEMPLATES_DIR . '/' . $template . '.php';
}
```

**วิธีทำงาน:**
1. รับชื่อ template (e.g., `'home'`)
2. รับข้อมูลแบบ array (e.g., `['activities' => $list, 'user' => $user]`)
3. `extract()` แปลง array เป็น variables `$activities`, `$user`
4. `include` ไฟล์ template

---

### 4. Database Connection (`includes/database.php`)

```php
function getConnection(): mysqli
{
    $hostname = 'localhost';
    $dbName = 'fast_camp';
    $username = 'root';
    $password = '';
    
    $conn = new mysqli($hostname, $username, $password, $dbName);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}
```

**หลักการ:**
- ใช้ MySQLi extension (native PHP)
- สร้าง connection ใหม่ทุกครั้งที่เรียก (ไม่ใช่ singleton)
- แต่ละ function ใน database layer จะ `close()` connection เมื่อเสร็จ

---

## 🧩 Design Patterns ที่ใช้

### 1. **Front Controller Pattern**
ทุก request เข้าผ่าน `public/index.php` → แล้ว router จัดสรรต่อ

```
Request ──► index.php ──► Router ──► Controller
```

### 2. **MVC (Model-View-Controller)**
- **Model** = `databases/*.php` (data access)
- **View** = `templates/*.php` (presentation)
- **Controller** = `routes/*.php` + `includes/router.php` (logic)

### 3. **Template Method Pattern**
ทุก route ใช้โครงสร้างเดียวกัน:
```php
1. Check Auth
2. Get Input
3. Validate
4. Call Database Functions
5. Render View or Redirect
```

### 4. **Stateless OTP Pattern**
สร้าง OTP โดยไม่ต้องเก็บในฐานข้อมูล:
```php
$data = "{$userId}:{$activityId}:{$timeWindow}";
$hash = hash_hmac('sha256', $data, OTP_SECRET_KEY);
$otp = generateFromHash($hash);
```

---

## 🌊 Data Flow แต่ละ Feature

### 1. Authentication Flow

```
[User] ──POST /login──► [login.php]
                         • authenticateUser(email, pass)
                         • ถ้าสำเร็จ → $_SESSION['user_id'] = ...
                         • redirect → /home
                              │
                              ▼
                    [Other Routes]
                    • if (!isset($_SESSION['user_id']))
                    •   redirect → /login
```

### 2. Activity CRUD Flow

**Create:**
```
/create ──► create.php ──► createActivity(data) ──► redirect /activity/{id}
```

**Read:**
```
/activity/123 ──► activity.php ──► getActivityById(123)
                                      ├── getActivityImages(123)
                                      ├── isUserRegistered(123, userId)
                                      └── getRegistrationsByActivity(123) [if owner]
```

**Update:**
```
/edit/123 ──► edit.php ──► updateActivity(123, data) ──► redirect /activity/123
```

**Delete:**
```
POST /delete ──► delete.php ──► deleteActivity(id, ownerId) ──► redirect /home
```

### 3. Registration Flow

```
[User] ──POST /register_activity──► [register_activity.php]
                                    • check: ไม่ใช่เจ้าของ
                                    • check: ยังไม่ได้ลงทะเบียน
                                    • createRegistration(activityId, userId)
                                    • status = 'pending'
                                           │
                                           ▼
[Owner] ──POST /approve────────────► [approve.php]
                                    • updateRegistrationStatus(regId, 'approved', ownerId)
                                           │
                                           ▼
[User] ──GET /generate_otp────────► [generate_otp.php]
                                    • getCurrentOtp(userId, activityId)
                                    • return JSON {otp, expires_in}
                                           │
                                           ▼
[Owner] ──POST /checkin───────────► [checkin.php]
                                    • verifyStatelessOtp(otp, userId, activityId)
                                    • checkInRegistration(regId, ownerId)
                                    • is_checkin = 1
```

---

## 🗂️ การจัดการ Session

### Session Data Structure
```php
$_SESSION = [
    'user_id'    => 1,           // int
    'user_email' => 'a@b.com',   // string
    'user_name'  => 'John Doe'   // string
];
```

### Session Security
- เริ่มที่ `index.php` → `session_start()`
- ทุก protected route ตรวจสอบ: `if (!isset($_SESSION['user_id']))`
- ลบข้อมูลลับ (password) ออกจาก session

---

## 📝 การจัดการ Error

### HTTP Status Codes
```php
// 404 Not Found
http_response_code(404);
renderView('404');

// หรือ Redirect กลับพร้อม error parameter
header('Location: /activity/123?error=delete');
```

### Error Display in Templates
```php
<?php if ($error): ?>
<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-2xl mb-4">
    <?php echo htmlspecialchars($error); ?>
</div>
<?php endif; ?>
```

---

## 🎨 Frontend Architecture

### CSS Framework: Tailwind CSS
- CDN: `https://cdn.tailwindcss.com`
- Custom Config ผ่าน `<script>` tag
- Font: Kanit (Google Fonts)
- Icons: Font Awesome 6

### Custom Theme Colors
```javascript
tailwind.config = {
    theme: {
        extend: {
            colors: {
                primary: '#1c3671',      // น้ำเงินเข้ม
                secondary: '#c8defa',    // ฟ้าอ่อน
                surface: '#e3efff',      // ฟ้าขาว
                bg_main: '#f2f6fc',      // พื้นหลัง
                accent: '#e93b81'        // ชมพู
            }
        }
    }
}
```

---

## 📈 สรุป

FAST CAMP ใช้ **Simple MVC Architecture** ที่:

1. ✅ **ชัดเจน** - แยก Model/View/Controller ชัดเจน
2. ✅ **ยืดหยุ่น** - ไม่ผูกติด framework
3. ✅ **เร็ว** - น้อย abstraction layers
4. ✅ **ปลอดภัย** - ตรวจสอบสิทธิ์ทุก route
5. ✅ **ง่ายต่อการดูแล** - โครงสร้างเป็นระเบียบ

---

<div align="center">
  <p><a href="../README.md">← กลับไป README</a> | <a href="02-DATABASE.md">ถัดไป: ฐานข้อมูล →</a></p>
</div>
