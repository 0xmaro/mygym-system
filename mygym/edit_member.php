<?php
include 'config.php';
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header("HTTP/1.1 403 Forbidden");
    exit(json_encode(['status' => 'error', 'message' => 'Access Denied']));
}


$data = $_POST;


if (!isset($data['id']) || !isset($data['gender'])) {
    echo json_encode(['status' => 'error', 'message' => 'بيانات غير كاملة']);
    exit();
}

$id = $data['id'];
$gender = $data['gender'];


$table = ($gender === 'male') ? 'members' : 'womembers';


$allowedFields = [
    'first_name',
    'middle_name',
    'last_name',
    'phone',
    'age',
    'subscription_type',
    'subscription_duration',
    'start_date',
    'end_date',
    'coach_name',
    'notes',
    'renewed_by'
];


$updateFields = [];
$values = [];
$types = '';

foreach ($data as $key => $value) {
    if (in_array($key, $allowedFields)) {
        $updateFields[] = "`$key` = ?";
        $values[] = $value;
        $types .= 's';
    }
}

if (empty($updateFields)) {
    echo json_encode(['status' => 'error', 'message' => 'لا توجد بيانات للتحديث']);
    exit();
}


$values[] = $id;
$types .= 'i';


$query = "UPDATE `$table` SET " . implode(', ', $updateFields) . " WHERE `id` = ?";

$stmt = $conn->prepare($query);
if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'تحضير الاستعلام فشل: ' . $conn->error]);
    exit();
}

$stmt->bind_param($types, ...$values);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'تم تحديث بيانات العضو بنجاح']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'فشل في تحديث البيانات: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>