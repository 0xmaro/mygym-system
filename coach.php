<?php
session_start();
include 'auth.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'coach') {
    header("Location: index.php");
    exit();
}

$conn = new mysqli("localhost", "root", "root", "mygym");
if ($conn->connect_error) {
    die("فشل الاتصال بقاعدة البيانات: " . $conn->connect_error);
}

$today = date("Y-m-d");
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

$query = "SELECT * FROM members WHERE end_date <= DATE_ADD('$today', INTERVAL 2 DAY)";

if (!empty($search)) {
    $search_terms = explode(' ', $search);
    foreach ($search_terms as $term) {
        $term = trim($term);
        $query .= " AND (
            first_name LIKE '%$term%' OR 
            middle_name LIKE '%$term%' OR 
            last_name LIKE '%$term%'
        )";
    }
}

$query .= " ORDER BY end_date ASC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8">
    <title>لوحة المدرب - MY GYM </title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap');

        :root {
            --primary: #1a365d;
            --secondary: #2c5282;
            --accent: #e2b979;
            --dark: #1a202c;
            --light: #f7fafc;
            --danger: #e53e3e;
        }

        body {
            font-family: 'Tajawal', sans-serif;
            background: linear-gradient(135deg, var(--dark) 0%, var(--secondary) 100%);
            min-height: 100vh;
        }

        .whatsapp-icon {
            animation: pulse 2s ease-in-out infinite;
            filter: drop-shadow(0 0 8px #25D366);
            width: 50px;
            height: 50px;
            transition: transform 0.3s ease;
        }

        .whatsapp-icon:hover {
            transform: scale(1.1);
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }

            100% {
                transform: scale(1);
            }
        }

        .member-card {
            background: rgba(26, 32, 44, 0.85);
            border-left: 4px solid var(--accent);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(6px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .member-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.2);
            border-left-color: #d69e2e;
        }

        .expired-card {
            border-left: 4px solid var(--danger);
            background: linear-gradient(90deg, rgba(75, 0, 0, 0.2) 0%, rgba(26, 32, 44, 0.85) 70%);
        }

        .nav-gradient {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--accent) 0%, #d69e2e 100%);
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(214, 158, 46, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(214, 158, 46, 0.4);
        }

        .search-container {
            position: relative;
            width: 100%;
            max-width: 400px;
        }

        .search-input {
            background: rgba(247, 250, 252, 0.12);
            transition: all 0.3s ease;
            padding-right: 40px;
        }

        .search-input:focus {
            background: rgba(247, 250, 252, 0.18);
            box-shadow: 0 0 0 3px rgba(226, 185, 121, 0.3);
        }

        .search-btn {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--accent);
            background: none;
            border: none;
            cursor: pointer;
        }

        .status-badge {
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .active-badge {
            background-color: rgba(72, 187, 120, 0.2);
            color: #48bb78;
        }

        .expired-badge {
            background-color: rgba(229, 62, 62, 0.2);
            color: var(--danger);
        }

        .subscription-icon {
            width: 24px;
            height: 24px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-left: 4px;
        }

        .copyright {
            position: fixed;
            bottom: 10px;
            right: 10px;
            color: rgba(255, 255, 255, 0.5);
            font-size: 12px;
            transition: all 0.3s ease;
        }

        .copyright:hover {
            color: var(--accent);
            text-shadow: 0 0 5px rgba(226, 185, 121, 0.5);
        }

        .notification-bell {
            position: relative;
            color: white;
            font-size: 20px;
            transition: all 0.3s ease;
        }

        .notification-bell:hover {
            color: var(--accent);
            transform: scale(1.1);
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: var(--danger);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
        }
    </style>
</head>

<body class="text-gray-100">


<nav class="nav-gradient p-4 flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <div
                class="h-12 w-12 rounded-full bg-gradient-to-br from-accent to-yellow-700 shadow-lg flex items-center justify-center">
                <span class="text-xl font-bold text-dark">MG</span>
            </div>
            <span class="text-2xl font-bold text-accent">MY<span class="text-white"> - GYM</span></span>
        </div>

        <h1 class="text-2xl md:text-3xl font-bold text-white text-center">
            لوحة <span class="text-accent">المدرب</span>
        </h1>

        <div class="flex items-center space-x-4">
            <div class="text-right hidden md:block">
                <div id="datetime" class="text-sm text-accent mb-1"></div>
                <div class="text-white">
                    المشتركين المطلوب تجديدهم:
                    <span class="font-bold text-accent"><?php echo $result->num_rows; ?></span>
                </div>
            </div>

            <a href="logout.php"
                class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-lg transition-all duration-200 shadow">
                <i class="fas fa-sign-out-alt mr-1"></i> خروج
            </a>
        </div>
    </nav>


    <div class="container mx-auto p-4 md:p-6 max-w-6xl">

    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
            <div class="flex gap-3 w-full md:w-auto">
                <a href="add_member.php" class="btn-primary text-dark font-bold px-5 py-2 rounded-lg flex items-center">
                    <i class="fas fa-user-plus mr-2"></i> إضافة مشترك
                </a>
                <a href="add_session.php"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg flex items-center transition-all">
                    <i class="fas fa-calendar-check mr-2"></i> تسجيل حصة
                </a>
            </div>

            <form method="GET" class="search-container w-full md:w-auto">
                <button type="submit" class="search-btn">
                    <i class="fas fa-search"></i>
                </button>
                <input type="text" name="search" placeholder="ابحث باسم المشترك..."
                    value="<?php echo htmlspecialchars($search); ?>"
                    class="search-input w-full px-4 pl-10 py-2 rounded-lg border border-gray-600 focus:outline-none focus:border-accent">
            </form>
        </div>


        <?php if ($result->num_rows > 0): ?>
            <div class="grid gap-4">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <?php
                    $full_name = $row['first_name'] . ' ' . $row['middle_name'] . ' ' . $row['last_name'];
                    $is_expired = (strtotime($row['end_date']) < strtotime($today));


                    $phone = $row['phone'];
                    $masked_phone = substr($phone, 0, 4) . '•••' . substr($phone, -3);


                    $subscription_type = $row['subscription_type'];
                    $subscription_icon = str_contains($subscription_type, "شاملة") ?
                        '<i class="fas fa-dumbbell subscription-icon"></i>' :
                        '<i class="fas fa-fire subscription-icon"></i>';
                    ?>
                    <div
                        class="member-card <?php echo $is_expired ? 'expired-card' : ''; ?> rounded-lg p-4 flex flex-col md:flex-row justify-between items-start md:items-center gap-3">
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-accent mb-1"><?php echo $full_name; ?></h3>
                            <div class="flex flex-wrap items-center gap-4 text-sm text-gray-300">
                                <span><i class="fas fa-phone mr-1"></i> <?php echo $masked_phone; ?></span>
                                <span><?php echo $subscription_icon; ?>         <?php echo $subscription_type; ?></span>
                                <span class="flex items-center">
                                    <i class="fas fa-calendar-day mr-1"></i> <?php echo $row['end_date']; ?>
                                    <span
                                        class="status-badge <?php echo $is_expired ? 'expired-badge' : 'active-badge'; ?> ml-2">
                                        <?php echo $is_expired ? 'منتهي' : 'نشط'; ?>
                                    </span>
                                </span>
                            </div>
                        </div>
                        <a href="renew.php?id=<?php echo $row['id']; ?>"
                            class="btn-primary text-dark font-bold px-4 py-1 rounded-lg flex items-center">
                            <i class="fas fa-sync-alt mr-2"></i> تجديد
                        </a>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-16">
                <div class="bg-gray-800 bg-opacity-50 rounded-lg p-8 max-w-md mx-auto">
                    <i class="fas fa-check-circle text-5xl text-green-400 mb-4"></i>
                    <h3 class="text-xl font-bold text-gray-200 mb-2">لا يوجد تجديدات حالية</h3>
                    <p class="text-gray-400">جميع المشتركين لديهم اشتراكات نشطة</p>
                </div>
            </div>
        <?php endif; ?>
    </div>


    <a href="massagemen.php" class="fixed bottom-6 left-6 z-50">
        <div class="relative">
            <i class="fab fa-whatsapp whatsapp-icon text-4xl"></i>

        </div>
    </a>


    <a href="https://www.youtube.com/@0xmaro" target="_blank" class="copyright">
        &copy; 0xmaro
    </a>


    <script>
        function updateDateTime() {
            const now = new Date();
            const options = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: true
            };
            document.getElementById('datetime').textContent = now.toLocaleDateString('ar-EG', options);
        }
        setInterval(updateDateTime, 1000);
        updateDateTime();
    </script>

</body>

</html>