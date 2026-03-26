<?php
declare(strict_types=1);

require_once DATABASES_DIR . '/users.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}

$user = getUserById($_SESSION['user_id']);
if (!$user) {
    session_destroy();
    header('Location: /login');
    exit;
}

$error = '';
$success = '';

function uploadProfileImage(array $file, int $userId): ?string
{
    if (empty($file['tmp_name'])) {
        return null;
    }
    
    $uploadDir = __DIR__ . '/../public/uploads/avatars/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $maxSize = 5 * 1024 * 1024;
    
    $fileType = $file['type'];
    $fileSize = $file['size'];
    
    if (!in_array($fileType, $allowedTypes)) {
        return null;
    }
    
    if ($fileSize > $maxSize) {
        return null;
    }
    
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'user_' . $userId . '_' . time() . '.' . $ext;
    $filepath = $uploadDir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return 'uploads/avatars/' . $filename;
    }
    
    return null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name'] ?? '');
    $birthday = $_POST['birthday'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $occupation = trim($_POST['occupation'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    
    if (empty($fullName) || empty($birthday) || empty($gender) || empty($occupation) || empty($phone)) {
        $error = 'กรุณากรอกข้อมูลให้ครบทุกช่อง';
    } else {
        $updateData = [
            'full_name' => $fullName,
            'birthday' => $birthday,
            'gender' => $gender,
            'occupation' => $occupation,
            'phone' => $phone
        ];
        
        // Skip profile image upload - column doesn't exist in DB
        // Just update user info without profile_image
        if (updateUser($_SESSION['user_id'], $updateData)) {
            $_SESSION['user_name'] = $fullName;
            header('Location: /profile?updated=1');
            exit;
        } else {
            $error = 'เกิดข้อผิดพลาดในการแก้ไขโปรไฟล์';
        }
    }
}

renderView('edit_profile', [
    'user' => $user,
    'error' => $error,
    'success' => $success
]);
