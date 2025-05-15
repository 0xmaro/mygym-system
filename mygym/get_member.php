<?php
include 'config.php';
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header("HTTP/1.1 403 Forbidden");
    exit(json_encode(['error' => 'Access Denied']));
}

$id = $_GET['id'] ?? null;
$gender = $_GET['gender'] ?? null;

if (!$id || !$gender) {
    echo json_encode(['error' => 'بيانات غير كاملة']);
    exit();
}

$table = ($gender === 'male') ? 'members' : 'womembers';

$stmt = $conn->prepare("SELECT * FROM `$table` WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $member = $result->fetch_assoc();
    echo json_encode($member);
} else {
    echo json_encode(['error' => 'العضو غير موجود']);
}

$stmt->close();
$conn->close();
?>