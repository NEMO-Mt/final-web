<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/database.php';

function generateOTP(int $userId, int $activityId): string
{
    $conn = getConnection();
    
    $otp = str_pad((string)random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
    $expiresAt = date('Y-m-d H:i:s', strtotime('+30 minutes'));
    
    $sql = 'INSERT INTO otps (otp_code, user_id, activity_id, expires_at) VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE otp_code = ?, expires_at = ?, is_used = 0, created_at = CURRENT_TIMESTAMP';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('siisss', $otp, $userId, $expiresAt, $activityId, $otp, $expiresAt);
    $stmt->execute();
    $stmt->close();
    $conn->close();
    
    return $otp;
}

function verifyOTP(string $otpCode, int $activityId): ?array
{
    $conn = getConnection();
    $sql = 'SELECT o.*, u.full_name, u.email
            FROM otps o
            JOIN users u ON o.user_id = u.user_id
            WHERE o.otp_code = ? AND o.activity_id = ? AND o.expires_at > NOW() AND o.is_used = 0';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $otpCode, $activityId);
    $stmt->execute();
    $result = $stmt->get_result();
    $otp = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
    return $otp ?: null;
}

function markOTPAsUsed(string $otpCode, int $activityId): bool
{
    $conn = getConnection();
    $sql = 'UPDATE otps SET is_used = 1 WHERE otp_code = ? AND activity_id = ?';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $otpCode, $activityId);
    $result = $stmt->execute();
    $affectedRows = $stmt->affected_rows;
    $stmt->close();
    $conn->close();
    return $result && $affectedRows > 0;
}

function getOTPForUser(int $userId, int $activityId): ?array
{
    $conn = getConnection();
    $sql = 'SELECT * FROM otps WHERE user_id = ? AND activity_id = ? AND expires_at > NOW() AND is_used = 0';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $userId, $activityId);
    $stmt->execute();
    $result = $stmt->get_result();
    $otp = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
    return $otp ?: null;
}

function cleanupExpiredOTPs(): void
{
    $conn = getConnection();
    $sql = 'DELETE FROM otps WHERE expires_at < NOW()';
    $conn->query($sql);
    $conn->close();
}
