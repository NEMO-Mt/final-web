<?php
declare(strict_types=1);

require_once DATABASES_DIR . '/users.php';

$error = '';
$formData = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData = [
        'email' => trim($_POST['email'] ?? ''),
        'password' => $_POST['password'] ?? '',
        'confirm_password' => $_POST['confirm_password'] ?? '',
        'full_name' => trim($_POST['full_name'] ?? ''),
        'birthday' => $_POST['birthday'] ?? '',
        'gender' => $_POST['gender'] ?? '',
        'occupation' => trim($_POST['occupation'] ?? ''),
        'phone' => trim($_POST['phone'] ?? '')
    ];
    
    if (empty($formData['email']) || empty($formData['password']) || empty($formData['full_name']) ||
        empty($formData['birthday']) || empty($formData['gender']) || empty($formData['occupation']) || empty($formData['phone'])) {
        $error = 'กรุณากรอกข้อมูลให้ครบทุกช่อง';
    } elseif ($formData['password'] !== $formData['confirm_password']) {
        $error = 'รหัสผ่านและยืนยันรหัสผ่านไม่ตรงกัน';
    } elseif (strlen($formData['password']) < 6) {
        $error = 'รหัสผ่านต้องมีความยาวอย่างน้อย 6 ตัวอักษร';
    } elseif (getUserByEmail($formData['email'])) {
        $error = 'อีเมลนี้ถูกใช้งานแล้ว';
    } else {
        unset($formData['confirm_password']);
        if (createUser($formData)) {
            header('Location: /login?registered=1');
            exit;
        } else {
            $error = 'เกิดข้อผิดพลาดในการสมัครสมาชิก กรุณาลองใหม่';
        }
    }
}

if (isset($_SESSION['user_id'])) {
    header('Location: /home');
    exit;
}

renderView('register', ['error' => $error, 'formData' => $formData]);
