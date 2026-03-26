<?php
declare(strict_types=1);

require_once DATABASES_DIR . '/users.php';
require_once DATABASES_DIR . '/activities.php';
require_once DATABASES_DIR . '/registrations.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}

$user = getUserById($_SESSION['user_id']);
if (!$user) {
    session_destroy();
    header('Location: /login');
    exit;
}

$createdActivities = getActivitiesByOwner($_SESSION['user_id']);
$myRegistrations = getRegistrationsByUser($_SESSION['user_id']);

$age = null;
if ($user['birthday']) {
    $birthDate = new DateTime($user['birthday']);
    $today = new DateTime();
    $age = $today->diff($birthDate)->y;
}

renderView('profile', [
    'user' => $user,
    'age' => $age,
    'createdCount' => count($createdActivities),
    'registrationCount' => count($myRegistrations),
    'registrations' => $myRegistrations
]);
