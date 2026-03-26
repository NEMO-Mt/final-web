<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/database.php';
// บันทึกการสมัครสมาชิก 
function createRegistration(int $activityId, int $userId): bool
{
    $conn = getConnection();
    $sql = 'INSERT INTO registrations (activity_id, user_id, status) VALUES (?, ?, "pending")';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $activityId, $userId);
    // เป็นส่วนป้องกันการสมัครกิจกรรมเดิมซ้ำ
    try {
        $result = $stmt->execute();
    } catch (mysqli_sql_exception $e) {
        $result = false;
    }
    $stmt->close();
    $conn->close();
    return $result;
}
// ดึวข้อมูลรายละเอียดการลงทะเบียน และดึงข้อมูลที่เกี่ยวข้องของผู้สมัคร, ดุึงกิจกรรมมา
function getRegistrationById(int $regId): ?array
{
    $conn = getConnection();
    $sql = 'SELECT r.*, u.full_name, u.email, u.birthday, u.gender, u.occupation, u.phone,
            a.title as activity_title, a.start_date, a.end_date, a.location
            FROM registrations r 
            JOIN users u ON r.user_id = u.user_id 
            JOIN activities a ON r.activity_id = a.activity_id
            WHERE r.reg_id = ?';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $regId);
    $stmt->execute();
    $result = $stmt->get_result();
    $registration = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
    return $registration ?: null;
}
// ดึงรายชือผุ้สมัครกิจกรรม
function getRegistrationsByActivity(int $activityId): array
{
    $conn = getConnection();
    $sql = 'SELECT r.*, u.full_name, u.email, u.birthday, u.gender, u.occupation, u.phone
            FROM registrations r 
            JOIN users u ON r.user_id = u.user_id 
            WHERE r.activity_id = ? 
            ORDER BY r.created_at DESC';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $activityId);
    $stmt->execute();
    $result = $stmt->get_result();
    $registrations = [];
    while ($row = $result->fetch_assoc()) {
        $registrations[] = $row;
    }
    $stmt->close();
    $conn->close();
    return $registrations;
}
// ดึงรายการกิจกรรมทั้งหมดที่ลงทะเบียนไว้
function getRegistrationsByUser(int $userId): array
{
    $conn = getConnection();
    $sql = 'SELECT r.*, a.title, a.start_date, a.end_date, a.location, u.full_name as owner_name
            FROM registrations r 
            JOIN activities a ON r.activity_id = a.activity_id
            JOIN users u ON a.owner_id = u.user_id
            WHERE r.user_id = ? 
            ORDER BY r.created_at DESC';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $registrations = [];
    while ($row = $result->fetch_assoc()) {
        $registrations[] = $row;
    }
    $stmt->close();
    $conn->close();
    return $registrations;
}
// การกดยืนยัน/ปฏิเสธ การเข้าร่วมกิจกรรม
function updateRegistrationStatus(int $regId, string $status, int $ownerId): bool
{
    $conn = getConnection();
    $sql = 'UPDATE registrations r 
            JOIN activities a ON r.activity_id = a.activity_id 
            SET r.status = ? 
            WHERE r.reg_id = ? AND a.owner_id = ?';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sii', $status, $regId, $ownerId);
    $result = $stmt->execute();
    $affectedRows = $stmt->affected_rows;
    $stmt->close();
    $conn->close();
    return $result && $affectedRows > 0;
}
// การยืนยันว่าคนนี้มาเข้าร่วมแล้ว
function checkInRegistration(int $regId, int $ownerId): bool
{
    $conn = getConnection();
    $sql = 'UPDATE registrations r 
            JOIN activities a ON r.activity_id = a.activity_id 
            SET r.is_checkin = 1 
            WHERE r.reg_id = ? AND a.owner_id = ? AND r.status = "approved"';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $regId, $ownerId);
    $result = $stmt->execute();
    $affectedRows = $stmt->affected_rows;
    $stmt->close();
    $conn->close();
    return $result && $affectedRows > 0;
}
// คำนวณข้อมูลสรุปให้เจ้าของกิจกรรม
function getRegistrationStats(int $activityId): array
{
    $conn = getConnection();
    
    $stats = [];
    // จำนวนคนสมัคร
    $sql = 'SELECT COUNT(*) as total FROM registrations WHERE activity_id = ?';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $activityId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stats['total'] = $result->fetch_assoc()['total'];
    $stmt->close();
    // จำนวนคนรออนุมัติ
    $sql = 'SELECT COUNT(*) as pending FROM registrations WHERE activity_id = ? AND status = "pending"';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $activityId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stats['pending'] = $result->fetch_assoc()['pending'];
    $stmt->close();
    // จำนวนคนที่ถูกอนุมัติ
    $sql = 'SELECT COUNT(*) as approved FROM registrations WHERE activity_id = ? AND status = "approved"';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $activityId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stats['approved'] = $result->fetch_assoc()['approved'];
    $stmt->close();
    // จำนวนคนที่ถูกปฏิเสธ
    $sql = 'SELECT COUNT(*) as rejected FROM registrations WHERE activity_id = ? AND status = "rejected"';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $activityId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stats['rejected'] = $result->fetch_assoc()['rejected'];
    $stmt->close();
    // คนที่ทำการเช็คชื่อ/เช็คอินไปแล้ว
    $sql = 'SELECT COUNT(*) as checked_in FROM registrations WHERE activity_id = ? AND is_checkin = 1';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $activityId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stats['checked_in'] = $result->fetch_assoc()['checked_in'];
    $stmt->close();
    // วิเคราะห์แยกเพศ -> นับว่าแต่ละเพศมีกี่คน
    $sql = 'SELECT u.gender, COUNT(*) as count FROM registrations r 
            JOIN users u ON r.user_id = u.user_id 
            WHERE r.activity_id = ? AND r.status = "approved" 
            GROUP BY u.gender';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $activityId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stats['gender'] = [];
    while ($row = $result->fetch_assoc()) {
        $stats['gender'][$row['gender']] = $row['count'];
    }
    $stmt->close();
    // แยกอายุแล้วทำการนับว่ามีอายุเท่าไหร่กี่คนบ้าง
    $sql = 'SELECT 
            CASE 
                WHEN TIMESTAMPDIFF(YEAR, u.birthday, CURDATE()) < 18 THEN "under18"
                WHEN TIMESTAMPDIFF(YEAR, u.birthday, CURDATE()) BETWEEN 18 AND 25 THEN "18-25"
                WHEN TIMESTAMPDIFF(YEAR, u.birthday, CURDATE()) BETWEEN 26 AND 35 THEN "26-35"
                WHEN TIMESTAMPDIFF(YEAR, u.birthday, CURDATE()) BETWEEN 36 AND 50 THEN "36-50"
                ELSE "over50"
            END as age_group,
            COUNT(*) as count 
            FROM registrations r 
            JOIN users u ON r.user_id = u.user_id 
            WHERE r.activity_id = ? AND r.status = "approved" 
            GROUP BY age_group';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $activityId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stats['age_groups'] = [];
    while ($row = $result->fetch_assoc()) {
        $stats['age_groups'][$row['age_group']] = $row['count'];
    }
    $stmt->close();
    
    $conn->close();
    return $stats;
}
// เช็คว่าสมัครกิจกรรมนี้ไปหรือยัง
function isUserRegistered(int $activityId, int $userId): ?array
{
    $conn = getConnection();
    $sql = 'SELECT * FROM registrations WHERE activity_id = ? AND user_id = ?';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $activityId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $registration = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
    return $registration ?: null;
}
