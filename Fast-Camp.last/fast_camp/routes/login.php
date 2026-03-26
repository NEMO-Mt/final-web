<?php
declare(strict_types=1);

require_once DATABASES_DIR . '/users.php';

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'กรุณากรอกอีเมลและรหัสผ่าน';
    } else {
        $user = authenticateUser($email, $password);
        if ($user) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['full_name'];
            header('Location: /home');
            exit;
        } else {
            $error = 'อีเมลหรือรหัสผ่านไม่ถูกต้อง';
        }
    }
}

if (isset($_SESSION['user_id'])) {
    header('Location: /home');
    exit;
}

renderView('login', ['error' => $error, 'email' => $email]);
