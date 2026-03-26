<?php
declare(strict_types=1);

require_once DATABASES_DIR . '/activities.php';
require_once DATABASES_DIR . '/registrations.php';
require_once DATABASES_DIR . '/helpers.php';

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
if (!$activity) {
    header('Location: /home');
    exit;
}

$isOwner = $activity['owner_id'] == $_SESSION['user_id'];

$registration = isUserRegistered($activityId, $_SESSION['user_id']);

$images = getActivityImages($activityId);

$registrations = $isOwner ? getRegistrationsByActivity($activityId) : [];
$stats = $isOwner ? getRegistrationStats($activityId) : [];

$created = isset($_GET['created']);
$registered = isset($_GET['registered']);

renderView('activity_detail', [
    'activity' => $activity,
    'isOwner' => $isOwner,
    'registration' => $registration,
    'images' => $images,
    'registrations' => $registrations,
    'stats' => $stats,
    'created' => $created,
    'registered' => $registered,
    'userId' => $_SESSION['user_id'],
    'userName' => $_SESSION['user_name'],
    'userEmail' => $_SESSION['user_email']
]);
