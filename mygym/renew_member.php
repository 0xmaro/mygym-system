<?php
include 'config.php';
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header("HTTP/1.1 403 Forbidden");
    exit(json_encode(['status' => 'error', 'message' => 'Access Denied']));
}

$data = $_POST;
$id = $data['id'];
$gender = $data['gender'];
$duration = $data['subscription_duration'];
$renewedBy = $data['renewed_by'];
unset($data['id'], $data['gender']);


$table = $gender === 'male' ? 'members' : 'womembers';


$stmt = $conn->prepare("SELECT end_date FROM $table WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$member = $result->fetch_assoc();
$stmt->close();


$currentEndDate = new DateTime($member['end_date']);
$today = new DateTime();


// Otherwise, extend from current end date
$startDate = $currentEndDate < $today ? $today : $currentEndDate;

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

$newEndDate = $startDate->add($interval);


$stmt = $conn->prepare("UPDATE $table SET end_date = ?, renewed_by = ?, renewed_at = NOW(), subscription_duration = ?, notified_before_expiry = 0 WHERE id = ?");
$newEndDateStr = $newEndDate->format('Y-m-d');
$stmt->bind_param('sssi', $newEndDateStr, $renewedBy, $duration, $id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to renew subscription']);
}

$stmt->close();
$conn->close();
?>