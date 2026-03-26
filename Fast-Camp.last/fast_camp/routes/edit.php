<?php
declare(strict_types=1);

require_once DATABASES_DIR . '/activities.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}

$activityId = (int)($_GET['id'] ?? 0);
if (!$activityId) {
    header('Location: /home');
    exit;
}

$activity = getActivityById($activityId);
if (!$activity || $activity['owner_id'] != $_SESSION['user_id']) {
    header('Location: /home');
    exit;
}

$error = '';

function uploadImages(array $files, int $activityId): array
{
    $uploaded = [];
    $imagePaths = [];
    $uploadDir = __DIR__ . '/../public/uploads/';
    
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $maxSize = 5 * 1024 * 1024;
    
    foreach ($files['tmp_name'] as $key => $tmpName) {
        if (empty($tmpName)) continue;
        
        $fileType = $files['type'][$key];
        $fileSize = $files['size'][$key];
        
        if (!in_array($fileType, $allowedTypes)) continue;
        if ($fileSize > $maxSize) continue;
        
        $ext = pathinfo($files['name'][$key], PATHINFO_EXTENSION);
        $filename = 'activity_' . $activityId . '_' . bin2hex(random_bytes(4)) . '_' . time() . '_' . $key . '.' . $ext;
        $filepath = $uploadDir . $filename;
        
        if (move_uploaded_file($tmpName, $filepath)) {
            $imagePaths[] = 'uploads/' . $filename;
            $uploaded[] = $filename;
        }
    }
    
    // Add all images in a single database transaction
    if (!empty($imagePaths)) {
        addActivityImages($activityId, $imagePaths);
    }
    
    return $uploaded;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $detail = trim($_POST['detail'] ?? '');
    $startDate = $_POST['start_date'] ?? '';
    $endDate = $_POST['end_date'] ?? '';
    $location = trim($_POST['location'] ?? '');
    
    if (empty($title) || empty($detail) || empty($startDate) || empty($endDate) || empty($location)) {
        $error = 'กรุณากรอกข้อมูลให้ครบทุกช่อง';
    } elseif ($startDate > $endDate) {
        $error = 'วันที่เริ่มต้องไม่มากกว่าวันที่สิ้นสุด';
    } else {
        $success = updateActivity($activityId, [
            'title' => $title,
            'detail' => $detail,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'location' => $location,
            'owner_id' => $_SESSION['user_id']
        ]);
        
        if ($success) {
            if (!empty($_FILES['images']) && is_array($_FILES['images']['tmp_name'])) {
                uploadImages($_FILES['images'], $activityId);
            }
            
            header('Location: /activity/' . $activityId . '?updated=1');
            exit;
        } else {
            $error = 'เกิดข้อผิดพลาดในการแก้ไขกิจกรรม';
        }
    }
}

$images = getActivityImages($activityId);

renderView('edit_activity', [
    'activity' => $activity,
    'images' => $images,
    'error' => $error
]);
