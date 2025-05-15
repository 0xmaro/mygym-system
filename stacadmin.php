<?php
session_start();

include 'auth.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'config.php';


try {
    $conn->query("SELECT 1");
} catch (PDOException $e) {
    die("فشل الاتصال بقاعدة البيانات: " . $e->getMessage());
}


function getNewMembersCount($interval, $genderTable)
{
    global $conn;
    $query = "";

    switch ($interval) {
        case 'اليوم':
            $query = "SELECT COUNT(*) as count FROM $genderTable WHERE DATE(start_date) = CURDATE()";
            break;
        case 'أسبوع':
            $query = "SELECT COUNT(*) as count FROM $genderTable WHERE start_date >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK)";
            break;
        case 'شهر':
            $query = "SELECT COUNT(*) as count FROM $genderTable WHERE start_date >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
            break;
        case '3 شهور':
            $query = "SELECT COUNT(*) as count FROM $genderTable WHERE start_date >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)";
            break;
        case '6 شهور':
            $query = "SELECT COUNT(*) as count FROM $genderTable WHERE start_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)";
            break;
        case 'سنة':
            $query = "SELECT COUNT(*) as count FROM $genderTable WHERE start_date >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)";
            break;
    }

    $result = $conn->query($query);
    return $result ? $result->fetch(PDO::FETCH_ASSOC)['count'] : 0;
}

$intervals = ['اليوم', 'أسبوع', 'شهر', '3 شهور', '6 شهور', 'سنة'];
$newMembers = [];
foreach ($intervals as $interval) {
    $newMembers[$interval] = getNewMembersCount($interval, 'members') + getNewMembersCount($interval, 'womembers');
}


$maleCount = $conn->query("SELECT COUNT(*) as c FROM members")->fetch(PDO::FETCH_ASSOC)['c'] ?? 0;
$femaleCount = $conn->query("SELECT COUNT(*) as c FROM womembers")->fetch(PDO::FETCH_ASSOC)['c'] ?? 0;


$subTypes = ['حديد', 'اجهزه', 'private'];
$subStats = [];
foreach ($subTypes as $type) {
    $male = $conn->query("SELECT COUNT(*) as c FROM members WHERE subscription_type='$type'")->fetch(PDO::FETCH_ASSOC)['c'] ?? 0;
    $female = $conn->query("SELECT COUNT(*) as c FROM womembers WHERE subscription_type='$type'")->fetch(PDO::FETCH_ASSOC)['c'] ?? 0;
    $subStats[$type] = $male + $female;
}


$durationStats = [
    'شهر' => $conn->query("SELECT COUNT(*) as c FROM members WHERE subscription_duration='شهر'")->fetch(PDO::FETCH_ASSOC)['c'] ?? 0 +
        $conn->query("SELECT COUNT(*) as c FROM womembers WHERE subscription_duration='شهر'")->fetch(PDO::FETCH_ASSOC)['c'] ?? 0,
    '3 شهور' => $conn->query("SELECT COUNT(*) as c FROM members WHERE subscription_duration='3 شهور'")->fetch(PDO::FETCH_ASSOC)['c'] ?? 0 +
        $conn->query("SELECT COUNT(*) as c FROM womembers WHERE subscription_duration='3 شهور'")->fetch(PDO::FETCH_ASSOC)['c'] ?? 0,
    '6 شهور' => $conn->query("SELECT COUNT(*) as c FROM members WHERE subscription_duration='6 شهور'")->fetch(PDO::FETCH_ASSOC)['c'] ?? 0 +
        $conn->query("SELECT COUNT(*) as c FROM womembers WHERE subscription_duration='6 شهور'")->fetch(PDO::FETCH_ASSOC)['c'] ?? 0,
    'سنة' => $conn->query("SELECT COUNT(*) as c FROM members WHERE subscription_duration='سنة'")->fetch(PDO::FETCH_ASSOC)['c'] ?? 0 +
        $conn->query("SELECT COUNT(*) as c FROM womembers WHERE subscription_duration='سنة'")->fetch(PDO::FETCH_ASSOC)['c'] ?? 0,
];


$expiredThisMonth = $conn->query("SELECT COUNT(*) as c FROM members WHERE end_date BETWEEN DATE_SUB(CURDATE(), INTERVAL 1 MONTH) AND CURDATE()")->fetch(PDO::FETCH_ASSOC)['c'] ?? 0 +
    $conn->query("SELECT COUNT(*) as c FROM womembers WHERE end_date BETWEEN DATE_SUB(CURDATE(), INTERVAL 1 MONTH) AND CURDATE()")->fetch(PDO::FETCH_ASSOC)['c'] ?? 0;

$coachSessionsMonthly = $conn->query("SELECT coach_name, COUNT(*) as c FROM sessions WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH) GROUP BY coach_name ORDER BY c DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC) ?? [];
$coachSessions3Months = $conn->query("SELECT coach_name, COUNT(*) as c FROM sessions WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH) GROUP BY coach_name ORDER BY c DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC) ?? [];
$coachSessionsYearly = $conn->query("SELECT coach_name, COUNT(*) as c FROM sessions WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR) GROUP BY coach_name ORDER BY c DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC) ?? [];

$sessionTypesMonthly = $conn->query("SELECT CONCAT(member_fullname) as full_name, COUNT(*) as c FROM sessions WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH) GROUP BY full_name ORDER BY c DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC) ?? [];
$sessionTypes3Months = $conn->query("SELECT CONCAT(member_fullname) as full_name, COUNT(*) as c FROM sessions WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH) GROUP BY full_name ORDER BY c DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC) ?? [];
$sessionTypesYearly = $conn->query("SELECT CONCAT(member_fullname) as full_name, COUNT(*) as c FROM sessions WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR) GROUP BY full_name ORDER BY c DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC) ?? [];

$coachRenew = $conn->query("SELECT renewed_by, COUNT(*) as c FROM (
    SELECT renewed_by FROM members WHERE renewed_by IS NOT NULL
    UNION ALL
    SELECT renewed_by FROM womembers WHERE renewed_by IS NOT NULL
) as renews GROUP BY renewed_by")->fetchAll(PDO::FETCH_ASSOC) ?? [];

$monthlyStats = $conn->query("
    SELECT 
        MONTH(start_date) as month_num,
        MONTHNAME(start_date) as month_name,
        COUNT(*) as new_subs,
        (SELECT COUNT(*) FROM members WHERE renewed_at IS NOT NULL AND MONTH(renewed_at) = month_num) +
        (SELECT COUNT(*) FROM womembers WHERE renewed_at IS NOT NULL AND MONTH(renewed_at) = month_num) as renewals
    FROM (
        SELECT start_date FROM members
        UNION ALL
        SELECT start_date FROM womembers
    ) as all_members
    GROUP BY month_num, month_name
    ORDER BY new_subs DESC, renewals DESC
    LIMIT 1
")->fetch(PDO::FETCH_ASSOC) ?? ['month_name' => 'لا يوجد بيانات', 'new_subs' => 0, 'renewals' => 0];

$membersToRenew = $conn->query("
    SELECT 
        CONCAT(first_name, ' ', middle_name, ' ', last_name) as full_name,
        phone,
        end_date,
        subscription_type,
        subscription_duration,
        coach_name
    FROM members 
    WHERE end_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 1 MONTH)
    UNION ALL
    SELECT 
        CONCAT(first_name, ' ', middle_name, ' ', last_name) as full_name,
        phone,
        end_date,
        subscription_type,
        subscription_duration,
        coach_name
    FROM womembers 
    WHERE end_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 1 MONTH)
")->fetchAll(PDO::FETCH_ASSOC) ?? [];

$expiredLastMonth = $conn->query("
    SELECT 
        CONCAT(first_name, ' ', middle_name, ' ', last_name) as full_name,
        phone,
        end_date,
        subscription_type,
        subscription_duration,
        coach_name
    FROM members 
    WHERE end_date BETWEEN DATE_SUB(CURDATE(), INTERVAL 1 MONTH) AND CURDATE()
    UNION ALL
    SELECT 
        CONCAT(first_name, ' ', middle_name, ' ', last_name) as full_name,
        phone,
        end_date,
        subscription_type,
        subscription_duration,
        coach_name
    FROM womembers 
    WHERE end_date BETWEEN DATE_SUB(CURDATE(), INTERVAL 1 MONTH) AND CURDATE()
")->fetchAll(PDO::FETCH_ASSOC) ?? [];

function getSessionsCount($interval)
{
    global $conn;
    $query = "";

    switch ($interval) {
        case 'اليوم':
            $query = "SELECT COUNT(*) as c FROM sessions WHERE DATE(created_at) = CURDATE()";
            break;
        case 'أسبوع':
            $query = "SELECT COUNT(*) as c FROM sessions WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK)";
            break;
        case 'شهر':
            $query = "SELECT COUNT(*) as c FROM sessions WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
            break;
        case '3 شهور':
            $query = "SELECT COUNT(*) as c FROM sessions WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)";
            break;
        case '6 شهور':
            $query = "SELECT COUNT(*) as c FROM sessions WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)";
            break;
        case 'سنة':
            $query = "SELECT COUNT(*) as c FROM sessions WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)";
            break;
    }

    $result = $conn->query($query);
    return $result ? $result->fetch(PDO::FETCH_ASSOC)['c'] : 0;
}

$sessionsStats = [];
foreach ($intervals as $interval) {
    $sessionsStats[$interval] = getSessionsCount($interval);
}

$totalSessionsThisMonth = $sessionsStats['شهر'];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة تحكم الجيم</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/animate.css@4.1.1/animate.min.css">
    <style>
        :root {
            --primary: #4e73df;
            --primary-light: #9ab6ff;
            --secondary: #1cc88a;
            --success: #1cc88a;
            --info: #36b9cc;
            --warning: #f6c23e;
            --danger: #e74a3b;
            --light: #f8f9fc;
            --dark: #5a5c69;
            --bg-gradient: linear-gradient(180deg, #4e73df 10%, #224abe 100%);
            --bg-gradient-success: linear-gradient(180deg, #1cc88a 10%, #13855c 100%);
            --private-color: #9c27b0;
        }
        
        body {
            background-color: #f8f9fc;
            font-family: 'Tajawal', sans-serif;
        }
        
        .sidebar {
            width: 250px;
            background: var(--bg-gradient);
            min-height: 100vh;
            position: fixed;
            right: -250px;
            top: 0;
            transition: all 0.3s;
            z-index: 1000;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }
        
        .sidebar.active {
            right: 0;
        }
        
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 1rem;
            font-weight: 600;
            border-left: 3px solid transparent;
            transition: all 0.3s;
        }
        
        .sidebar .nav-link:hover {
            color: white;
            background: rgba(255, 255, 255, 0.1);
            border-left: 3px solid white;
        }
        
        .sidebar .nav-link i {
            margin-left: 0.5rem;
        }
        
        .sidebar .nav-link.active {
            color: white;
            background: rgba(255, 255, 255, 0.2);
            border-left: 3px solid white;
        }
        
        #content-wrapper {
            margin-right: 0;
            transition: all 0.3s;
            min-height: 100vh;
        }
        
        #content-wrapper.active {
            margin-right: 250px;
        }
        
        .topbar {
            height: 70px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            background: white;
        }
        
        .floating-btn {
            position: fixed;
            bottom: 30px;
            left: 30px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 100;
            animation: pulse 2s infinite;
            background: var(--primary);
            color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: all 0.3s;
        }
        
        .floating-btn:hover {
            transform: scale(1.1);
            background: var(--primary-light);
        }
        
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(78, 115, 223, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(78, 115, 223, 0); }
            100% { box-shadow: 0 0 0 0 rgba(78, 115, 223, 0); }
        }
        
        .card {
            border: none;
            border-radius: 0.35rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
            transition: all 0.3s;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1.75rem 0 rgba(58, 59, 69, 0.2);
        }
        
        .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
        }
        
        .stat-card {
            border-left: 0.25rem solid var(--primary);
            background: white;
        }
        
        .stat-card .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
        }
        
        .stat-card.success {
            border-left-color: var(--success);
        }
        
        .stat-card.info {
            border-left-color: var(--info);
        }
        
        .stat-card.warning {
            border-left-color: var(--warning);
        }
        
        .stat-card.danger {
            border-left-color: var(--danger);
        }
        
        .stat-card.private {
            border-left-color: var(--private-color);
        }
        
        .chart-area {
            position: relative;
            height: 250px;
            width: 100%;
        }
        
        .chart-pie {
            position: relative;
            height: 250px;
            width: 100%;
        }
        
        .progress-sm {
            height: 0.5rem;
        }
        
        .rotate-15 {
            transform: rotate(15deg);
        }
        
        .floating-count {
            position: absolute;
            top: -10px;
            right: -10px;
            background: var(--danger);
            color: white;
            width: 25px;
            height: 25px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
        
        .member-card {
            border-left: 4px solid var(--primary);
            transition: all 0.3s;
        }
        
        .member-card:hover {
            transform: translateX(5px);
        }
        
        .session-chart-container {
            position: relative;
            height: 300px;
        }
        
        .chart-tabs .nav-link {
            color: var(--dark);
            font-weight: 600;
        }
        
        .chart-tabs .nav-link.active {
            color: var(--primary);
            border-bottom: 2px solid var(--primary);
        }
        
        .badge-private {
            background-color: var(--private-color);
            color: white;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                right: -100%;
            }
            
            #content-wrapper.active {
                margin-right: 0;
            }
            
            .floating-btn {
                width: 50px;
                height: 50px;
                bottom: 20px;
                left: 20px;
            }
        }
    </style>
</head>
<body id="page-top">
    <div id="wrapper">
        <ul class="sidebar navbar-nav">
            <li class="nav-item text-center my-4">
                <h4 class="text-white">نظام إدارة الجيم</h4>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="admin.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>لوحة التحكم</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="members.php">
                    <i class="fas fa-fw fa-male"></i>
                    <span>إدارة الرجال</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="womembers.php">
                    <i class="fas fa-fw fa-female"></i>
                    <span>إدارة السيدات</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="sessions.php">
                    <i class="fas fa-fw fa-calendar-check"></i>
                    <span>إدارة الحصص</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php">
                    <i class="fas fa-fw fa-sign-out-alt"></i>
                    <span>تسجيل الخروج</span>
                </a>
            </li>
        </ul>

        <div id="content-wrapper">
            <nav class="topbar navbar navbar-expand navbar-light bg-white mb-4 static-top shadow">
                <button id="sidebarToggle" class="btn btn-link d-md-none">
                    <i class="fa fa-bars"></i>
                </button>
                
                <div class="d-flex align-items-center">
                    <h5 class="mb-0 text-gray-800"><i class="fas fa-chart-line text-primary mx-2"></i>لوحة الإحصائيات</h5>
                </div>
            </nav>

            <div class="container-fluid">
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            إجمالي الأعضاء</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $maleCount + $femaleCount ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-users fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card success h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                            أعضاء جدد (الشهر)</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $newMembers['شهر'] ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-user-plus fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card warning h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            اشتراكات منتهية</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $expiredThisMonth ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card info h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                            إجمالي الحصص (الشهر)</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalSessionsThisMonth ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="row">

                <div class="col-xl-6 col-lg-6">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-chart-bar mr-2"></i>الأعضاء الجدد</h6>
                            </div>
                            <div class="card-body">
                                <div class="chart-area">
                                    <canvas id="newMembersChart"></canvas>
                                </div>
                                <div class="mt-4 text-center small">
                                    <?php foreach ($newMembers as $interval => $count): ?>
                                            <span class="mr-2">
                                                <i class="fas fa-circle text-primary"></i> <?= $interval ?>: <?= $count ?>
                                            </span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="col-xl-6 col-lg-6">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-venus-mars mr-2"></i>نسبة الجنسين</h6>
                            </div>
                            <div class="card-body">
                                <div class="chart-pie pt-4 pb-2">
                                    <canvas id="genderRatioChart"></canvas>
                                </div>
                                <div class="mt-4 text-center small">
                                    <span class="mr-2">
                                        <i class="fas fa-circle text-primary"></i> رجال: <?= $maleCount ?>
                                    </span>
                                    <span class="mr-2">
                                        <i class="fas fa-circle text-danger"></i> نساء: <?= $femaleCount ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="row">

                
                    <div class="col-xl-4 col-lg-4">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-dumbbell mr-2"></i>أنواع الاشتراكات</h6>
                            </div>
                            <div class="card-body">
                                <div class="chart-pie pt-4 pb-2">
                                    <canvas id="subscriptionTypeChart"></canvas>
                                </div>
                                <div class="mt-4 text-center small">
                                    <?php foreach ($subStats as $type => $count): ?>
                                            <span class="mr-2">
                                                <i class="fas fa-circle <?=
                                                    $type == 'حديد' ? 'text-warning' :
                                                    ($type == 'اجهزه' ? 'text-info' : 'text-purple')
                                                    ?>"></i> <?= $type ?>: <?= $count ?>
                                            </span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="col-xl-4 col-lg-4">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-clock mr-2"></i>مدة الاشتراكات</h6>
                            </div>
                            <div class="card-body">
                                <div class="chart-pie pt-4 pb-2">
                                    <canvas id="subscriptionDurationChart"></canvas>
                                </div>
                                <div class="mt-4 text-center small">
                                    <?php foreach ($durationStats as $duration => $count): ?>
                                            <span class="mr-2">
                                                <i class="fas fa-circle <?=
                                                    $duration == 'شهر' ? 'text-success' :
                                                    ($duration == '3 شهور' ? 'text-primary' :
                                                        ($duration == '6 شهور' ? 'text-warning' : 'text-danger'))
                                                    ?>"></i> <?= $duration ?>: <?= $count ?>
                                            </span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="col-xl-4 col-lg-4">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-sync-alt mr-2"></i>نسبة تجديد الاشتراكات</h6>
                            </div>
                            <div class="card-body">
                                <div class="chart-pie pt-4 pb-2">
                                    <canvas id="coachRenewChart"></canvas>
                                </div>
                                <div class="mt-4 text-center small">
                                    <?php foreach ($coachRenew as $coach): ?>
                                            <span class="mr-2">
                                                <i class="fas fa-circle text-info"></i> <?= $coach['renewed_by'] ?>: <?= $coach['c'] ?>
                                            </span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card shadow">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-chart-line mr-2"></i>إحصائيات الحصص</h6>
                            </div>
                            <div class="card-body">
                                <div class="session-chart-container">
                                    <canvas id="sessionsChart"></canvas>
                                </div>
                                <div class="mt-4 text-center small">
                                    <?php foreach ($sessionsStats as $interval => $count): ?>
                                            <span class="mr-2">
                                                <i class="fas fa-circle <?=
                                                    $interval == 'اليوم' ? 'text-primary' :
                                                    ($interval == 'أسبوع' ? 'text-success' :
                                                        ($interval == 'شهر' ? 'text-info' :
                                                            ($interval == '3 شهور' ? 'text-warning' :
                                                                ($interval == '6 شهور' ? 'text-danger' : 'text-purple'))))
                                                    ?>"></i> <?= $interval ?>: <?= $count ?>
                                            </span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-lg-6 mb-4">
                        <div class="card shadow position-relative">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-bell mr-2"></i>الأعضاء الذين يحتاجون للتجديد</h6>
                                <?php if (count($membersToRenew) > 0): ?>
                                        <span class="floating-count"><?= count($membersToRenew) ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                                <?php if (count($membersToRenew) > 0): ?>
                                        <?php foreach ($membersToRenew as $member): ?>
                                                <div class="card mb-3 member-card">
                                                    <div class="card-body p-3">
                                                        <div class="row no-gutters align-items-center">
                                                            <div class="col mr-2">
                                                                <div class="h6 mb-0 font-weight-bold text-gray-800"><?= $member['full_name'] ?></div>
                                                                <div class="text-xs text-muted"><i class="fas fa-phone mr-1"></i> <?= $member['phone'] ?></div>
                                                                <div class="text-xs text-muted"><i class="fas fa-calendar-times mr-1"></i> ينتهي في: <?= $member['end_date'] ?></div>
                                                                <div class="text-xs">
                                                                    <span class="badge <?= $member['subscription_type'] == 'private' ? 'badge-private' : ($member['subscription_type'] == 'حديد' ? 'badge-warning' : 'badge-info') ?>">
                                                                        <?= $member['subscription_type'] ?>
                                                                    </span>
                                                                    <span class="badge badge-secondary ml-1">
                                                                        <?= $member['subscription_duration'] ?>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <div class="col-auto">
                                                                <i class="fas fa-user-tie text-gray-300"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                        <?php endforeach; ?>
                                <?php else: ?>
                                        <div class="text-center py-4 text-muted">
                                            <i class="fas fa-check-circle fa-2x mb-3 text-success"></i>
                                            <p>لا يوجد أعضاء يحتاجون للتجديد هذا الشهر</p>
                                        </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>


                    <div class="col-lg-6 mb-4">
                        <div class="card shadow position-relative">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-history mr-2"></i>الاشتراكات المنتهية الشهر الماضي</h6>
                                <?php if (count($expiredLastMonth) > 0): ?>
                                        <span class="floating-count"><?= count($expiredLastMonth) ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                                <?php if (count($expiredLastMonth) > 0): ?>
                                        <?php foreach ($expiredLastMonth as $member): ?>
                                                <div class="card mb-3 member-card">
                                                    <div class="card-body p-3">
                                                        <div class="row no-gutters align-items-center">
                                                            <div class="col mr-2">
                                                                <div class="h6 mb-0 font-weight-bold text-gray-800"><?= $member['full_name'] ?></div>
                                                                <div class="text-xs text-muted"><i class="fas fa-phone mr-1"></i> <?= $member['phone'] ?></div>
                                                                <div class="text-xs text-muted"><i class="fas fa-calendar-times mr-1"></i> انتهى في: <?= $member['end_date'] ?></div>
                                                                <div class="text-xs">
                                                                    <span class="badge <?= $member['subscription_type'] == 'private' ? 'badge-private' : ($member['subscription_type'] == 'حديد' ? 'badge-warning' : 'badge-info') ?>">
                                                                        <?= $member['subscription_type'] ?>
                                                                    </span>
                                                                    <span class="badge badge-secondary ml-1">
                                                                        <?= $member['subscription_duration'] ?>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <div class="col-auto">
                                                                <i class="fas fa-user-tie text-gray-300"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                        <?php endforeach; ?>
                                <?php else: ?>
                                        <div class="text-center py-4 text-muted">
                                            <i class="fas fa-check-circle fa-2x mb-3 text-success"></i>
                                            <p>لا يوجد اشتراكات منتهية الشهر الماضي</p>
                                        </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-lg-6 mb-4">
                        <div class="card shadow">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-trophy mr-2"></i>الكباتن الأكثر نشاطاً</h6>
                            </div>
                            <div class="card-body">
                                <ul class="nav nav-tabs chart-tabs" id="coachTabs" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="monthly-coach-tab" data-toggle="tab" href="#monthly-coach" role="tab">شهري</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="3months-coach-tab" data-toggle="tab" href="#3months-coach" role="tab">3 شهور</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="yearly-coach-tab" data-toggle="tab" href="#yearly-coach" role="tab">سنوي</a>
                                    </li>
                                </ul>
                                <div class="tab-content mt-3" id="coachTabContent">
                                    <div class="tab-pane fade show active" id="monthly-coach" role="tabpanel">
                                        <?php if (count($coachSessionsMonthly) > 0): ?>
                                                <?php foreach ($coachSessionsMonthly as $coach): ?>
                                                        <div class="mb-3">
                                                            <div class="d-flex justify-content-between">
                                                                <span><?= $coach['coach_name'] ?></span>
                                                                <span class="badge badge-primary"><?= $coach['c'] ?> جلسة</span>
                                                            </div>
                                                            <div class="progress progress-sm mt-1">
                                                                <div class="progress-bar bg-primary" role="progressbar" style="width: <?= ($coach['c'] / max(array_column($coachSessionsMonthly, 'c'))) * 100 ?>%"></div>
                                                            </div>
                                                        </div>
                                                <?php endforeach; ?>
                                        <?php else: ?>
                                                <div class="text-center py-4 text-muted">
                                                    <i class="fas fa-info-circle fa-2x mb-3 text-info"></i>
                                                    <p>لا يوجد بيانات متاحة</p>
                                                </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="tab-pane fade" id="3months-coach" role="tabpanel">
                                        <?php if (count($coachSessions3Months) > 0): ?>
                                                <?php foreach ($coachSessions3Months as $coach): ?>
                                                        <div class="mb-3">
                                                            <div class="d-flex justify-content-between">
                                                                <span><?= $coach['coach_name'] ?></span>
                                                                <span class="badge badge-primary"><?= $coach['c'] ?> جلسة</span>
                                                            </div>
                                                            <div class="progress progress-sm mt-1">
                                                                <div class="progress-bar bg-primary" role="progressbar" style="width: <?= ($coach['c'] / max(array_column($coachSessions3Months, 'c'))) * 100 ?>%"></div>
                                                            </div>
                                                        </div>
                                                <?php endforeach; ?>
                                        <?php else: ?>
                                                <div class="text-center py-4 text-muted">
                                                    <i class="fas fa-info-circle fa-2x mb-3 text-info"></i>
                                                    <p>لا يوجد بيانات متاحة</p>
                                                </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="tab-pane fade" id="yearly-coach" role="tabpanel">
                                        <?php if (count($coachSessionsYearly) > 0): ?>
                                                <?php foreach ($coachSessionsYearly as $coach): ?>
                                                        <div class="mb-3">
                                                            <div class="d-flex justify-content-between">
                                                                <span><?= $coach['coach_name'] ?></span>
                                                                <span class="badge badge-primary"><?= $coach['c'] ?> جلسة</span>
                                                            </div>
                                                            <div class="progress progress-sm mt-1">
                                                                <div class="progress-bar bg-primary" role="progressbar" style="width: <?= ($coach['c'] / max(array_column($coachSessionsYearly, 'c'))) * 100 ?>%"></div>
                                                            </div>
                                                        </div>
                                                <?php endforeach; ?>
                                        <?php else: ?>
                                                <div class="text-center py-4 text-muted">
                                                    <i class="fas fa-info-circle fa-2x mb-3 text-info"></i>
                                                    <p>لا يوجد بيانات متاحة</p>
                                                </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="col-lg-6 mb-4">
                        <div class="card shadow">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-list-ol mr-2"></i>الأكثر تسجيلاً للحصص</h6>
                            </div>
                            <div class="card-body">
                                <ul class="nav nav-tabs chart-tabs" id="sessionsTabs" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="monthly-sessions-tab" data-toggle="tab" href="#monthly-sessions" role="tab">شهري</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="3months-sessions-tab" data-toggle="tab" href="#3months-sessions" role="tab">3 شهور</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="yearly-sessions-tab" data-toggle="tab" href="#yearly-sessions" role="tab">سنوي</a>
                                    </li>
                                </ul>
                                <div class="tab-content mt-3" id="sessionsTabContent">
                                    <div class="tab-pane fade show active" id="monthly-sessions" role="tabpanel">
                                        <?php if (count($sessionTypesMonthly) > 0): ?>
                                                <?php foreach ($sessionTypesMonthly as $s): ?>
                                                        <div class="mb-3">
                                                            <div class="d-flex justify-content-between">
                                                                <span><?= $s['full_name'] ?></span>
                                                                <span class="badge badge-primary"><?= $s['c'] ?> جلسة</span>
                                                            </div>
                                                            <div class="progress progress-sm mt-1">
                                                                <div class="progress-bar bg-primary" role="progressbar" style="width: <?= ($s['c'] / max(array_column($sessionTypesMonthly, 'c'))) * 100 ?>%"></div>
                                                            </div>
                                                        </div>
                                                <?php endforeach; ?>
                                        <?php else: ?>
                                                <div class="text-center py-4 text-muted">
                                                    <i class="fas fa-info-circle fa-2x mb-3 text-info"></i>
                                                    <p>لا يوجد بيانات متاحة</p>
                                                </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="tab-pane fade" id="3months-sessions" role="tabpanel">
                                        <?php if (count($sessionTypes3Months) > 0): ?>
                                                <?php foreach ($sessionTypes3Months as $s): ?>
                                                        <div class="mb-3">
                                                            <div class="d-flex justify-content-between">
                                                                <span><?= $s['full_name'] ?></span>
                                                                <span class="badge badge-primary"><?= $s['c'] ?> جلسة</span>
                                                            </div>
                                                            <div class="progress progress-sm mt-1">
                                                                <div class="progress-bar bg-primary" role="progressbar" style="width: <?= ($s['c'] / max(array_column($sessionTypes3Months, 'c'))) * 100 ?>%"></div>
                                                            </div>
                                                        </div>
                                                <?php endforeach; ?>
                                        <?php else: ?>
                                                <div class="text-center py-4 text-muted">
                                                    <i class="fas fa-info-circle fa-2x mb-3 text-info"></i>
                                                    <p>لا يوجد بيانات متاحة</p>
                                                </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="tab-pane fade" id="yearly-sessions" role="tabpanel">
                                        <?php if (count($sessionTypesYearly) > 0): ?>
                                                <?php foreach ($sessionTypesYearly as $s): ?>
                                                        <div class="mb-3">
                                                            <div class="d-flex justify-content-between">
                                                                <span><?= $s['full_name'] ?></span>
                                                                <span class="badge badge-primary"><?= $s['c'] ?> جلسة</span>
                                                            </div>
                                                            <div class="progress progress-sm mt-1">
                                                                <div class="progress-bar bg-primary" role="progressbar" style="width: <?= ($s['c'] / max(array_column($sessionTypesYearly, 'c'))) * 100 ?>%"></div>
                                                            </div>
                                                        </div>
                                                <?php endforeach; ?>
                                        <?php else: ?>
                                                <div class="text-center py-4 text-muted">
                                                    <i class="fas fa-info-circle fa-2x mb-3 text-info"></i>
                                                    <p>لا يوجد بيانات متاحة</p>
                                                </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    

    <a href="mygym/index.php" class="floating-btn animate__animated animate__fadeInLeft">
        <i class="fas fa-home fa-lg"></i>
    </a>


    


    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-easing@1.4.1/jquery.easing.min.js"></script>

    <script>

$('#sidebarToggle').click(function() {
        $('.sidebar').toggleClass('active');
        $('#content-wrapper').toggleClass('active');
    });


    const newMembersData = {
        labels: <?= json_encode(array_keys($newMembers)) ?>,
        datasets: [{
            label: 'أعضاء جدد',
            data: <?= json_encode(array_values($newMembers)) ?>,
            backgroundColor: 'rgba(78, 115, 223, 0.5)',
            borderColor: 'rgba(78, 115, 223, 1)',
            borderWidth: 1
        }]
    };

    const genderRatioData = {
        labels: ['رجال', 'نساء'],
        datasets: [{
            data: [<?= $maleCount ?>, <?= $femaleCount ?>],
            backgroundColor: ['#4e73df', '#e74a3b'],
            hoverBackgroundColor: ['#2e59d9', '#be2617'],
            hoverBorderColor: "rgba(234, 236, 244, 1)",
        }]
    };

    const subscriptionTypeData = {
        labels: <?= json_encode(array_keys($subStats)) ?>,
        datasets: [{
            data: <?= json_encode(array_values($subStats)) ?>,
            backgroundColor: ['#f6c23e', '#36b9cc', '#9c27b0'],
            hoverBackgroundColor: ['#dda20a', '#258391', '#7b1fa2'],
            hoverBorderColor: "rgba(234, 236, 244, 1)",
        }]
    };

    const subscriptionDurationData = {
        labels: <?= json_encode(array_keys($durationStats)) ?>,
        datasets: [{
            data: <?= json_encode(array_values($durationStats)) ?>,
            backgroundColor: ['#1cc88a', '#4e73df', '#f6c23e', '#e74a3b'],
            hoverBackgroundColor: ['#13855c', '#2e59d9', '#dda20a', '#be2617'],
            hoverBorderColor: "rgba(234, 236, 244, 1)",
        }]
    };

    const coachRenewData = {
        labels: <?= json_encode(array_column($coachRenew, 'renewed_by')) ?>,
        datasets: [{
            data: <?= json_encode(array_column($coachRenew, 'c')) ?>,
            backgroundColor: ['#4e73df', '#1cc88a', '#f6c23e', '#e74a3b', '#36b9cc'],
            hoverBackgroundColor: ['#2e59d9', '#13855c', '#dda20a', '#be2617', '#258391'],
            hoverBorderColor: "rgba(234, 236, 244, 1)",
        }]
    };

    const sessionsData = {
        labels: <?= json_encode(array_keys($sessionsStats)) ?>,
        datasets: [{
            label: 'عدد الحصص',
            data: <?= json_encode(array_values($sessionsStats)) ?>,
            backgroundColor: 'rgba(54, 185, 204, 0.5)',
            borderColor: 'rgba(54, 185, 204, 1)',
            borderWidth: 2,
            tension: 0.4,
            fill: true
        }]
    };


    document.addEventListener('DOMContentLoaded', function() {

        const newMembersCtx = document.getElementById('newMembersChart').getContext('2d');
        new Chart(newMembersCtx, {
            type: 'bar',
            data: newMembersData,
            options: {
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });


        const genderRatioCtx = document.getElementById('genderRatioChart').getContext('2d');
        new Chart(genderRatioCtx, {
            type: 'doughnut',
            data: genderRatioData,
            options: {
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });


        const subscriptionTypeCtx = document.getElementById('subscriptionTypeChart').getContext('2d');
        new Chart(subscriptionTypeCtx, {
            type: 'pie',
            data: subscriptionTypeData,
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });


        const subscriptionDurationCtx = document.getElementById('subscriptionDurationChart').getContext('2d');
        new Chart(subscriptionDurationCtx, {
            type: 'pie',
            data: subscriptionDurationData,
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });


        const coachRenewCtx = document.getElementById('coachRenewChart').getContext('2d');
        new Chart(coachRenewCtx, {
            type: 'polarArea',
            data: coachRenewData,
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
                scale: {
                    ticks: {
                        beginAtZero: true
                    }
                }
            }
        });


        const sessionsCtx = document.getElementById('sessionsChart').getContext('2d');
        new Chart(sessionsCtx, {
            type: 'line',
            data: sessionsData,
            options: {
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                },
                elements: {
                    point: {
                        radius: 5,
                        hoverRadius: 7
                    }
                }
            }
        });
    });


    $(window).scroll(function() {
        if ($(this).scrollTop() > 100) {
            $('.scroll-to-top').fadeIn();
        } else {
            $('.scroll-to-top').fadeOut();
        }
    });

    $('.scroll-to-top').click(function() {
        $('html, body').animate({scrollTop: 0}, 800);
        return false;
    });
    </script>
</body>
</html>