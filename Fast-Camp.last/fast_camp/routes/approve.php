<?php
declare(strict_types=1);

require_once DATABASES_DIR . '/registrations.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /home');
    exit;
}

$regId = (int)($_POST['reg_id'] ?? 0);
$action = $_POST['action'] ?? '';

if (!$regId || !in_array($action, ['approve', 'reject'])) {
    header('Location: /home');
    exit;
}

$status = $action === 'approve' ? 'approved' : 'rejected';
$success = updateRegistrationStatus($regId, $status, $_SESSION['user_id']);

$registration = getRegistrationById($regId);
if ($registration) {
    if ($success) {
        header('Location: /activity?id=' . $registration['activity_id'] . '&' . $action . '=1');
    } else {
        header('Location: /activity?id=' . $registration['activity_id'] . '&error=' . $action);
    }
} else {
    header('Location: /home');
}
exit;
