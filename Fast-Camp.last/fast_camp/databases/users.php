<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/database.php';

function createUser(array $user): bool
{
    $conn = getConnection();
    $sql = 'INSERT INTO users (email, password, full_name, birthday, gender, occupation, phone) VALUES (?, ?, ?, ?, ?, ?, ?)';
    $stmt = $conn->prepare($sql);
    $hashedPassword = password_hash($user['password'], PASSWORD_DEFAULT);
    $stmt->bind_param('sssssss', $user['email'], $hashedPassword, $user['full_name'], $user['birthday'], $user['gender'], $user['occupation'], $user['phone']);
    $result = $stmt->execute();
    $stmt->close();
    $conn->close();
    return $result;
}

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

function updateUser(int $userId, array $user): bool
{
    $conn = getConnection();
    
    if (isset($user['profile_image'])) {
        $sql = 'UPDATE users SET full_name = ?, birthday = ?, gender = ?, occupation = ?, phone = ?, profile_image = ? WHERE user_id = ?';
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            $conn->close();
            return false;
        }
        $stmt->bind_param('ssssssi', $user['full_name'], $user['birthday'], $user['gender'], $user['occupation'], $user['phone'], $user['profile_image'], $userId);
    } else {
        $sql = 'UPDATE users SET full_name = ?, birthday = ?, gender = ?, occupation = ?, phone = ? WHERE user_id = ?';
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            $conn->close();
            return false;
        }
        $stmt->bind_param('sssssi', $user['full_name'], $user['birthday'], $user['gender'], $user['occupation'], $user['phone'], $userId);
    }
    
    $result = $stmt->execute();
    $stmt->close();
    $conn->close();
    return $result;
}

function authenticateUser(string $email, string $password): ?array
{
    $user = getUserByEmail($email);
    if ($user && password_verify($password, $user['password'])) {
        unset($user['password']);
        return $user;
    }
    return null;
}
