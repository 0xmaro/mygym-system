<?php
include 'config.php';
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header("HTTP/1.1 403 Forbidden");
    exit("Access Denied");
}

$firstName = $_GET['member_fullname'] ?? '';
$phone = $_GET['phone'] ?? '';


$query = "SELECT * FROM sessions WHERE ";
$conditions = [];
$params = [];
$types = '';

if (!empty($firstName)) {
    $conditions[] = "member_fullname LIKE ?";
    $params[] = "%$firstName%";
    $types .= 's';
}


if (!empty($phone)) {
    $conditions[] = "phone LIKE ?";
    $params[] = "%$phone%";
    $types .= 's';
}

if (empty($conditions)) {
    $query = "SELECT * FROM sessions ORDER BY created_at DESC LIMIT 50";
} else {
    $query .= implode(' AND ', $conditions) . " ORDER BY created_at DESC";
}

$stmt = $conn->prepare($query);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$output = '';
$counter = 1;

while ($row = $result->fetch_assoc()) {
    $fullName = $row['member_fullname'];
    
    $output .= '<tr>';
    $output .= '<td>' . $counter . '</td>';
    $output .= '<td>' . $fullName . '</td>';
    $output .= '<td>' . $row['phone'] . '</td>';
    $output .= '<td>' . $row['age'] . '</td>';
    $output .= '<td>' . $row['coach_name'] . '</td>';
    $output .= '<td>' . $row['created_at'] . '</td>';
    $output .= '</tr>';
    
    $counter++;
}

if ($output === '') {
    $output = '<tr><td colspan="6" class="text-center">لا توجد حصص مسجلة</td></tr>';
}

echo $output;

$stmt->close();
$conn->close();
?>