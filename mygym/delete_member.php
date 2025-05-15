<?php
include 'config.php';
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header("HTTP/1.1 403 Forbidden");
    exit(json_encode(['status' => 'error', 'message' => 'Access Denied']));
}

$id = $_POST['id'];
$gender = $_POST['gender'];


$table = $gender === 'male' ? 'members' : 'womembers';


$stmt = $conn->prepare("DELETE FROM $table WHERE id = ?");
$stmt->bind_param('i', $id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to delete member']);
}

$stmt->close();
$conn->close();
?>