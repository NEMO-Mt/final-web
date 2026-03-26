<?php
declare(strict_types=1);

require_once DATABASES_DIR . '/registrations.php';
require_once DATABASES_DIR . '/helpers.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

$activityId = (int)($_GET['activity_id'] ?? 0);
if (!$activityId) {
    echo json_encode(['success' => false, 'error' => 'Invalid activity']);
    exit;
}

$registration = isUserRegistered($activityId, $_SESSION['user_id']);
if (!$registration || $registration['status'] !== 'approved') {
    echo json_encode(['success' => false, 'error' => 'Not approved for this activity']);
    exit;
}

$otpData = getCurrentOtp($_SESSION['user_id'], $activityId);
echo json_encode([
    'success' => true,
    'otp' => $otpData['code'],
    'expires_in' => $otpData['expires_in']
]);
