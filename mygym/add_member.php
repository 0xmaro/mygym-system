<?php
include 'config.php';
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header("HTTP/1.1 403 Forbidden");
    exit(json_encode(['status' => 'error', 'message' => 'Access Denied']));
}

$data = $_POST;
$gender = $data['gender'];
unset($data['gender']);


$startDate = new DateTime($data['start_date']);
$duration = $data['subscription_duration'];

switch ($duration) {
    case 'شهر':
        $interval = new DateInterval('P1M');
        break;
    case '3 شهور':
        $interval = new DateInterval('P3M');
        break;
    case '6 شهور':
        $interval = new DateInterval('P6M');
        break;
    case 'سنة':
        $interval = new DateInterval('P1Y');
        break;
    default:
        $interval = new DateInterval('P1M');
}

$endDate = $startDate->add($interval);
$data['end_date'] = $endDate->format('Y-m-d');


$table = $gender === 'male' ? 'members' : 'womembers';


$columns = implode(', ', array_keys($data));
$placeholders = implode(', ', array_fill(0, count($data), '?'));
$values = array_values($data);


$stmt = $conn->prepare("INSERT INTO $table ($columns) VALUES ($placeholders)");
$types = str_repeat('s', count($data));
$stmt->bind_param($types, ...$values);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to add member']);
}

$stmt->close();
$conn->close();
?>