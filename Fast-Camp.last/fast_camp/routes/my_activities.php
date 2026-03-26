<?php
declare(strict_types=1);

require_once DATABASES_DIR . '/activities.php';
require_once DATABASES_DIR . '/registrations.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}

$myActivities = getActivitiesByOwner($_SESSION['user_id']);
$myRegistrations = getRegistrationsByUser($_SESSION['user_id']);

renderView('my_activities', [
    'myActivities' => $myActivities,
    'myRegistrations' => $myRegistrations
]);
