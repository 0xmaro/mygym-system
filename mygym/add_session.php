<?php
include 'config.php';
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header("HTTP/1.1 403 Forbidden");
    exit(json_encode(['status' => 'error', 'message' => 'Access Denied']));
}

$data = $_POST;

$stmt = $conn->prepare("INSERT INTO sessions (member_fullname, phone, age, coach_name, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
$stmt->bind_param('ssssis', $data['member_first'], $data['member_middle'], $data['member_last'], $data['phone'], $data['age'], $data['coach_name']);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to add session']);
}

$stmt->close();
$conn->close();
?>