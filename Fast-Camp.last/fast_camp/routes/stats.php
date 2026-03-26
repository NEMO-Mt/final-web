<?php
declare(strict_types=1);

require_once DATABASES_DIR . '/activities.php';
require_once DATABASES_DIR . '/registrations.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}

$activityId = (int)($_GET['activity_id'] ?? 0);
if (!$activityId) {
    header('Location: /home');
    exit;
}

$activity = getActivityById($activityId);
if (!$activity || $activity['owner_id'] != $_SESSION['user_id']) {
    header('Location: /home');
    exit;
}

$stats = getRegistrationStats($activityId);
$registrations = getRegistrationsByActivity($activityId);

renderView('stats', [
    'activity' => $activity,
    'stats' => $stats,
    'registrations' => $registrations
]);
