<?php

declare(strict_types=1);


const ALLOW_METHODS = ['GET', 'POST'];
const INDEX_URI = '';


const INDEX_ROUNTE = 'home';



function normalizeUri(string $uri): string
{
    // ตัด query string ออก
    $uri = strtok($uri, '?');
    
    // ลบ base path สำหรับ subdirectory access
    $basePath = dirname($_SERVER['SCRIPT_NAME']);
    if ($basePath !== '/' && strpos($uri, $basePath) === 0) {
        $uri = substr($uri, strlen($basePath));
    }
    
    // ลบเครื่องหมาย '/' ที่อยู่ข้างหน้าและข้างหลังออก และแปลงเป็นตัวพิมพ์เล็ก
    $uri = strtolower(trim($uri, '/'));

    // เช็คว่า URI ว่างหรือไม่ ถ้าว่างให้เปลี่ยนเป็น route เริ่มต้น
    return $uri == INDEX_URI ? INDEX_ROUNTE : $uri;
}

// ฟังชันสำหรับแสดงหน้า 404 Not Found
function notFound()
{
    http_response_code(404);
    // เรียกใช้ฟังก์ชัน renderView เพื่อแสดงหน้า 404
    renderView('404');
    exit;
}

// ฟังชันสำหรับการหาเส้นทางไฟล์ PHP ที่ตรงกับ URI ที่ร้องขอเข้ามา
function getFilePath(string $uri): string
{
    return ROUTE_DIR . '/' . normalizeUri($uri) . '.php';
}

// ฟังก์ชันหลักสำหรับการจัดการเส้นทาง (routing) ที่ถูกเรียกใช้จาก index.php
function dispatch(string $uri, string $method): void
{
    // ฟังชันสำหรับทำให้ URI ที่ร้องขอเข้ามาอยู่ในรูปแบบมาตรฐาน
    $uri = normalizeUri($uri);

    // ตรวจสอบว่าวิธีการร้องขอ (HTTP Method) ถูกอนุญาตหรือไม่
    if (!in_array(strtoupper($method), ALLOW_METHODS)) {
        notFound();
    }

    // ตรวจสอบ pattern /activity/{id}
    if (preg_match('/^activity\/(\d+)$/', $uri, $matches)) {
        $_GET['id'] = $matches[1];
        include(ROUTE_DIR . '/activity.php');
        return;
    }

    // ตรวจสอบ pattern /edit/{id}
    if (preg_match('/^edit\/(\d+)$/', $uri, $matches)) {
        $_GET['id'] = $matches[1];
        include(ROUTE_DIR . '/edit.php');
        return;
    }

    // ตรวจสอบ pattern /delete/{id}
    if (preg_match('/^delete\/(\d+)$/', $uri, $matches)) {
        $_GET['id'] = $matches[1];
        include(ROUTE_DIR . '/delete.php');
        return;
    }

    // ตรวจสอบ pattern /stats/{activity_id}
    if (preg_match('/^stats\/(\d+)$/', $uri, $matches)) {
        $_GET['activity_id'] = $matches[1];
        include(ROUTE_DIR . '/stats.php');
        return;
    }

    // ตรวจสอบ pattern /checkin/{activity_id}
    if (preg_match('/^checkin\/(\d+)$/', $uri, $matches)) {
        $_GET['activity_id'] = $matches[1];
        include(ROUTE_DIR . '/checkin.php');
        return;
    }

    // ฟังชันสำหรับการหาเส้นทางไฟล์ PHP ที่ตรงกับ URI ที่ร้องขอเข้ามา
    $filePath = getFilePath($uri);
    if (file_exists($filePath)) {
        include($filePath);
        return;
    } else {
        notFound();
    }
}
