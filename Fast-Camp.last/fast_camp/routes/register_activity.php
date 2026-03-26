<?php
declare(strict_types=1);

require_once DATABASES_DIR . '/activities.php';
require_once DATABASES_DIR . '/registrations.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}

$activityId = (int)($_POST['activity_id'] ?? 0);
if (!$activityId) {
    header('Location: /home');
    exit;
}

$activity = getActivityById($activityId);
if (!$activity) {
    header('Location: /home');
    exit;
}

if ($activity['owner_id'] == $_SESSION['user_id']) {
    header('Location: /activity?id=' . $activityId . '&error=self_register');
    exit;
}

$existing = isUserRegistered($activityId, $_SESSION['user_id']);
if ($existing) {
    header('Location: /activity?id=' . $activityId . '&error=already_registered');
    exit;
}

$success = createRegistration($activityId, $_SESSION['user_id']);
if ($success) {
    header('Location: /activity?id=' . $activityId . '&registered=1');
} else {
    header('Location: /activity?id=' . $activityId . '&error=register');
}
exit;
