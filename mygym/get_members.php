<?php
include 'config.php';



$search = $_GET['search'] ?? '';
$gender = $_GET['gender'] ?? 'all';
$status = $_GET['status'] ?? 'all';


$maleQuery = "SELECT *, 'male' as gender FROM members WHERE 1=1";

$femaleQuery = "SELECT *, 'female' as gender FROM womembers WHERE 1=1";


if (!empty($search)) {
    $searchTerm = "%$search%";
    $maleQuery .= " AND (first_name LIKE ? OR middle_name LIKE ? OR last_name LIKE ?)";
    $femaleQuery .= " AND (first_name LIKE ? OR middle_name LIKE ? OR last_name LIKE ?)";
}


$today = date('Y-m-d');
if ($status !== 'all') {
    if ($status === 'active') {
        $maleQuery .= " AND end_date >= '$today'";
        $femaleQuery .= " AND end_date >= '$today'";
    } elseif ($status === 'expired') {
        $maleQuery .= " AND end_date < '$today'";
        $femaleQuery .= " AND end_date < '$today'";
    } elseif ($status === 'expiring') {
        $nextWeek = date('Y-m-d', strtotime('+7 days'));
        $maleQuery .= " AND end_date BETWEEN '$today' AND '$nextWeek'";
        $femaleQuery .= " AND end_date BETWEEN '$today' AND '$nextWeek'";
    }
}


if ($gender === 'male') {
    $query = $maleQuery;
} elseif ($gender === 'female') {
    $query = $femaleQuery;
} else {
    $query = "($maleQuery) UNION ALL ($femaleQuery)";
}

$query .= " ORDER BY end_date ASC";


$stmt = $conn->prepare($query);

if (!empty($search)) {
    if ($gender !== 'all') {
        $stmt->bind_param('sss', $searchTerm, $searchTerm, $searchTerm);
    } else {

        $stmt->bind_param(
            'ssssss',
            $searchTerm,
            $searchTerm,
            $searchTerm,
            $searchTerm,
            $searchTerm,
            $searchTerm
        );
    }
}

if (!$stmt->execute()) {
    die("حدث خطأ في الاستعلام: " . $stmt->error);
}

$result = $stmt->get_result();

$output = '';
$counter = 1;

while ($row = $result->fetch_assoc()) {
    $today = date('Y-m-d');
    $endDate = $row['end_date'];
    $statusBadge = '';

    if ($endDate >= $today) {
        $daysLeft = floor((strtotime($endDate) - strtotime($today)) / (60 * 60 * 24));
        $statusBadge = $daysLeft <= 7 ?
            '<span class="badge bg-warning text-dark">تنتهي قريباً</span>' :
            '<span class="badge bg-success">نشط</span>';
    } else {
        $statusBadge = '<span class="badge bg-danger">منتهي</span>';
    }

    $fullName = $row['first_name'] . ' ' . ($row['middle_name'] ? $row['middle_name'] . ' ' : '') . $row['last_name'];
    $genderBadge = $row['gender'] === 'male' ?
        '<span class="badge bg-primary">ذكر</span>' :
        '<span class="badge bg-info">أنثى</span>';

    $output .= '<tr>';
    $output .= '<td>' . $counter . '</td>';
    $output .= '<td>' . $fullName . ' ' . $genderBadge . '</td>';
    $output .= '<td>' . $row['phone'] . '</td>';
    $output .= '<td>' . $row['age'] . '</td>';
    $output .= '<td>' . $row['subscription_type'] . '</td>';
    $output .= '<td>' . $row['subscription_duration'] . '</td>';
    $output .= '<td>' . $row['start_date'] . '</td>';
    $output .= '<td>' . $row['end_date'] . '</td>';
    $output .= '<td>' . $statusBadge . '</td>';
    $output .= '<td>' . $row['coach_name'] . '</td>';
    $output .= '<td>';
    $output .= '<button class="btn btn-sm btn-info me-1" onclick="viewSessions(\'' . htmlspecialchars($row['first_name']) . '\', \'' . htmlspecialchars($row['middle_name']) . '\', \'' . htmlspecialchars($row['last_name']) . '\', \'' . htmlspecialchars($row['phone']) . '\')"><i class="fas fa-eye"></i></button>';
    $output .= '<button class="btn btn-sm btn-warning me-1" onclick="editMember(' . $row['id'] . ', \'' . $row['gender'] . '\')"><i class="fas fa-edit"></i></button>';
    $output .= '<button class="btn btn-sm btn-success me-1" onclick="renewMember(' . $row['id'] . ', \'' . $row['gender'] . '\')"><i class="fas fa-sync-alt"></i></button>';
    $output .= '<button class="btn btn-sm btn-danger" onclick="deleteMember(' . $row['id'] . ', \'' . $row['gender'] . '\')"><i class="fas fa-trash"></i></button>';
    $output .= '</td>';
    $output .= '</tr>';

    $counter++;
}

echo $output ?: '<tr><td colspan="11" class="text-center">لا توجد بيانات</td></tr>';

$stmt->close();
$conn->close();
?>