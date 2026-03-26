<?php
declare(strict_types=1);

require_once DATABASES_DIR . '/activities.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}

$keyword = trim($_GET['keyword'] ?? '');
$startDate = $_GET['start_date'] ?? '';
$endDate = $_GET['end_date'] ?? '';

$activities = searchActivities(
    $keyword ?: null,
    $startDate ?: null,
    $endDate ?: null
);

renderView('home', [
    'activities' => $activities,
    'keyword' => $keyword,
    'startDate' => $startDate,
    'endDate' => $endDate,
    'userName' => $_SESSION['user_name'] ?? ''
]);