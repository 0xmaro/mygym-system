<?php
include 'config.php';
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header("HTTP/1.1 403 Forbidden");
    exit("Access Denied");
}

$search = $_GET['search'] ?? '';
$date = $_GET['date'] ?? '';
$coach = $_GET['coach'] ?? 'all';


$query = "SELECT * FROM sessions WHERE 1=1";
$params = [];
$types = '';

if (!empty($search)) {
    $searchTerm = "%$search%";
    $query .= " AND (member_fullname LIKE ?)";
    $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm]);
    $types .= 'sss';
}

if (!empty($date)) {
    $query .= " AND DATE(created_at) = ?";
    $params[] = $date;
    $types .= 's';
}

if ($coach !== 'all') {
    $query .= " AND coach_name = ?";
    $params[] = $coach;
    $types .= 's';
}

$query .= " ORDER BY created_at DESC";

$stmt = $conn->prepare($query);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$output = '';
$counter = 1;

while ($row = $result->fetch_assoc()) {
    $fullName = $row['member_fullname'] ;
    
    $output .= '<tr>';
    $output .= '<td>' . $counter . '</td>';
    $output .= '<td>' . $fullName . '</td>';
    $output .= '<td>' . $row['phone'] . '</td>';
    $output .= '<td>' . $row['age'] . '</td>';
    $output .= '<td>' . $row['coach_name'] . '</td>';
    $output .= '<td>' . $row['created_at'] . '</td>';
    $output .= '<td>';
    $output .= '<button class="btn btn-sm btn-danger" onclick="deleteSession(' . $row['id'] . ')"><i class="fas fa-trash"></i></button>';
    $output .= '</td>';
    $output .= '</tr>';
    
    $counter++;
}

if ($output === '') {
    $output = '<tr><td colspan="7" class="text-center">لا توجد حصص مسجلة</td></tr>';
}

echo $output;

$stmt->close();
$conn->close();
?>