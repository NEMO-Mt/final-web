<?php
declare(strict_types=1);

require_once DATABASES_DIR . '/registrations.php';
require_once DATABASES_DIR . '/helpers.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otpCode = trim($_POST['otp'] ?? '');
    $activityId = (int)($_GET['activity_id'] ?? 0);
    
    if (empty($otpCode)) {
        $error = 'กรุณากรอกรหัส OTP';
    } else {
      
        $allRegistrations = getRegistrationsByActivity($activityId);
        $foundReg = null;
        
        foreach ($allRegistrations as $reg) {
            if ($reg['status'] === 'approved' && !$reg['is_checkin']) {
             
                if (verifyStatelessOtp($otpCode, $reg['user_id'], $activityId)) {
                    $foundReg = $reg;
                    break;
                }
            }
        }
        
        if ($foundReg) {
            $checkInSuccess = checkInRegistration($foundReg['reg_id'], $_SESSION['user_id']);
            if ($checkInSuccess) {
                $success = 'เช็คชื่อ ' . htmlspecialchars($foundReg['full_name']) . ' สำเร็จ';
            } else {
                $error = 'เกิดข้อผิดพลาดในการเช็คชื่อ';
            }
        } else {
            $error = 'รหัส OTP ไม่ถูกต้องหรือหมดอายุแล้ว';
        }
    }
}

$activityId = (int)($_GET['activity_id'] ?? 0);
$pendingCheckIns = [];

if ($activityId) {
    $allRegistrations = getRegistrationsByActivity($activityId);
    foreach ($allRegistrations as $reg) {
        if ($reg['status'] === 'approved' && !$reg['is_checkin']) {
            $pendingCheckIns[] = $reg;
        }
    }
}

renderView('checkin', [
    'error' => $error,
    'success' => $success,
    'activityId' => $activityId,
    'pendingCheckIns' => $pendingCheckIns
]);
