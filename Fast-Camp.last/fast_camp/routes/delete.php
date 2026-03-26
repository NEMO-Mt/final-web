<?php
declare(strict_types=1);

require_once DATABASES_DIR . '/activities.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}

$activityId = (int)($_POST['id'] ?? 0);
if (!$activityId) {
    header('Location: /home');
    exit;
}

$activity = getActivityById($activityId);
if (!$activity || $activity['owner_id'] != $_SESSION['user_id']) {
    header('Location: /home');
    exit;
}

$success = deleteActivity($activityId, $_SESSION['user_id']);
if ($success) {
    header('Location: /home?deleted=1');
} else {
    header('Location: /activity?id=' . $activityId . '&error=delete');
}
exit;
