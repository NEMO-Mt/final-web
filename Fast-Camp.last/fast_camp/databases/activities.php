<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/database.php';

function createActivity(array $activity): int
{
    $conn = getConnection();
    $sql = 'INSERT INTO activities (title, detail, start_date, end_date, location, owner_id) VALUES (?, ?, ?, ?, ?, ?)';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sssssi', $activity['title'], $activity['detail'], $activity['start_date'], $activity['end_date'], $activity['location'], $activity['owner_id']);
    $stmt->execute();
    $activityId = $stmt->insert_id;
    $stmt->close();
    $conn->close();
    return $activityId;
}

function getActivityById(int $activityId): ?array
{
    $conn = getConnection();
    $sql = 'SELECT a.*, u.full_name as owner_name, u.email as owner_email 
            FROM activities a 
            JOIN users u ON a.owner_id = u.user_id 
            WHERE a.activity_id = ?';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $activityId);
    $stmt->execute();
    $result = $stmt->get_result();
    $activity = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
    return $activity ?: null;
}

function getActivitiesByOwner(int $ownerId): array
{
    $conn = getConnection();
    $sql = 'SELECT * FROM activities WHERE owner_id = ? ORDER BY created_at DESC';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $ownerId);
    $stmt->execute();
    $result = $stmt->get_result();
    $activities = [];
    while ($row = $result->fetch_assoc()) {
        $activities[] = $row;
    }
    $stmt->close();
    $conn->close();
    return $activities;
}

function searchActivities(?string $keyword = null, ?string $startDate = null, ?string $endDate = null): array
{
    $conn = getConnection();
    $sql = 'SELECT a.*, u.full_name as owner_name FROM activities a JOIN users u ON a.owner_id = u.user_id WHERE 1=1';
    $params = [];
    $types = '';
    
    if ($keyword) {
        $sql .= ' AND (a.title LIKE ? OR a.detail LIKE ? OR a.location LIKE ?)';
        $keyword = '%' . $keyword . '%';
        $params[] = $keyword;
        $params[] = $keyword;
        $params[] = $keyword;
        $types .= 'sss';
    }
    
    if ($startDate) {
        $sql .= ' AND a.start_date >= ?';
        $params[] = $startDate;
        $types .= 's';
    }
    
    if ($endDate) {
        $sql .= ' AND a.end_date <= ?';
        $params[] = $endDate;
        $types .= 's';
    }
    
    $sql .= ' ORDER BY a.start_date DESC';
    
    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $activities = [];
    while ($row = $result->fetch_assoc()) {
        $activities[] = $row;
    }
    $stmt->close();
    $conn->close();
    return $activities;
}

function updateActivity(int $activityId, array $activity): bool
{
    $conn = getConnection();
    $sql = 'UPDATE activities SET title = ?, detail = ?, start_date = ?, end_date = ?, location = ? WHERE activity_id = ? AND owner_id = ?';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sssssii', $activity['title'], $activity['detail'], $activity['start_date'], $activity['end_date'], $activity['location'], $activityId, $activity['owner_id']);
    $result = $stmt->execute();
    $affectedRows = $stmt->affected_rows;
    $stmt->close();
    $conn->close();
    return $result && $affectedRows > 0;
}

function deleteActivity(int $activityId, int $ownerId): bool
{
    $conn = getConnection();
    $sql = 'DELETE FROM activities WHERE activity_id = ? AND owner_id = ?';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $activityId, $ownerId);
    $result = $stmt->execute();
    $affectedRows = $stmt->affected_rows;
    $stmt->close();
    $conn->close();
    return $result && $affectedRows > 0;
}

function addActivityImage(int $activityId, string $imagePath): bool
{
    $conn = getConnection();
    $sql = 'INSERT INTO activity_images (image_path, activity_id) VALUES (?, ?)';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $imagePath, $activityId);
    $result = $stmt->execute();
    $stmt->close();
    $conn->close();
    return $result;
}

function addActivityImages(int $activityId, array $imagePaths): int
{
    if (empty($imagePaths)) {
        return 0;
    }
    
    $conn = getConnection();
    $sql = 'INSERT INTO activity_images (image_path, activity_id) VALUES (?, ?)';
    $stmt = $conn->prepare($sql);
    
    $successCount = 0;
    foreach ($imagePaths as $imagePath) {
        $stmt->bind_param('si', $imagePath, $activityId);
        if ($stmt->execute()) {
            $successCount++;
        }
    }
    
    $stmt->close();
    $conn->close();
    return $successCount;
}

function getActivityImages(int $activityId): array
{
    $conn = getConnection();
    $sql = 'SELECT * FROM activity_images WHERE activity_id = ?';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $activityId);
    $stmt->execute();
    $result = $stmt->get_result();
    $images = [];
    while ($row = $result->fetch_assoc()) {
        $images[] = $row;
    }
    $stmt->close();
    $conn->close();
    return $images;
}

function deleteActivityImage(int $imageId, int $activityId): bool
{
    $conn = getConnection();
    $sql = 'DELETE FROM activity_images WHERE image_id = ? AND activity_id = ?';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $imageId, $activityId);
    $result = $stmt->execute();
    $affectedRows = $stmt->affected_rows;
    $stmt->close();
    $conn->close();
    return $result && $affectedRows > 0;
}
