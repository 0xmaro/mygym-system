<?php
include 'auth.php';

$conn = new mysqli("localhost", "root", "root", "mygym");
if ($conn->connect_error) {
    die("فشل الاتصال بقاعدة البيانات: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['notify_id'])) {
    $id = intval($_POST['notify_id']);
    $conn->query("UPDATE members SET notified_before_expiry = 1 WHERE id = $id");
    exit();
}

$today = new DateTime();
$min = clone $today;
$max = clone $today;
$min->modify('+1 day');
$max->modify('+4 days');

$min_date = $min->format('Y-m-d');
$max_date = $max->format('Y-m-d');
$today_date = $today->format('Y-m-d');

$sql = "SELECT *, DATE_FORMAT(end_date, '%Y-%m-%d') as formatted_end_date FROM members WHERE (end_date BETWEEN '$min_date' AND '$max_date' OR end_date < '$today_date') AND notified_before_expiry = 0";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>تنبيه المشتركين | MY GYM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #1a202c;
            --secondary: #2d3748;
            --accent: #d69e2e;
            --accent-dark: #b7791f;
            --danger: #e53e3e;
            --success: #38a169;
            --dark: #171923;
            --light: #f7fafc;
        }

        body {
            font-family: 'Tajawal', sans-serif;
            background: linear-gradient(135deg, var(--dark) 0%, var(--primary) 100%);
            min-height: 100vh;
        }

        .header-gradient {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }

        .card {
            background: rgba(26, 32, 44, 0.85);
            border-left: 4px solid var(--accent);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(6px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            animation: cardEntrance 0.8s ease-out both;
        }

        .card:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 15px 20px rgba(0, 0, 0, 0.2);
            border-left-color: var(--accent-dark);
        }

        .expired-card {
            border-left: 4px solid var(--danger);
            background: linear-gradient(90deg, rgba(75, 0, 0, 0.2) 0%, rgba(26, 32, 44, 0.85) 70%);
        }

        .btn-whatsapp {
            background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(37, 211, 102, 0.3);
        }

        .btn-whatsapp:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(37, 211, 102, 0.4);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-dark) 100%);
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(214, 158, 46, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(214, 158, 46, 0.4);
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
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(214, 158, 46, 0.7);
            }

            70% {
                box-shadow: 0 0 0 15px rgba(214, 158, 46, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(214, 158, 46, 0);
            }
        }

        @keyframes cardEntrance {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .status-badge {
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .active-badge {
            background-color: rgba(56, 161, 105, 0.2);
            color: var(--success);
        }

        .expired-badge {
            background-color: rgba(229, 62, 62, 0.2);
            color: var(--danger);
        }

        .masked-phone {
            font-family: monospace;
            letter-spacing: 1px;
        }

        .copyright {
            text-align: center;
            padding: 10px;
            color: rgba(255, 255, 255, 0.6);
            font-size: 14px;
            margin-top: 20px;
        }
    </style>
</head>

<body class="text-gray-100">


<header class="header-gradient p-6 shadow-lg">
        <div class="container mx-auto flex flex-col items-center">
            <div class="flex items-center mb-4">
                <div
                    class="h-16 w-16 rounded-full bg-gradient-to-br from-accent to-accent-dark shadow-lg flex items-center justify-center mr-3">
                    <i class="fas fa-dumbbell text-2xl text-dark"></i>
                </div>
                <h1 class="text-4xl font-extrabold text-accent">MY<span class="text-white"> - GYM</span></h1>
            </div>
            <h2 class="text-2xl font-bold text-center text-white">
                <i class="fas fa-bell text-accent mr-2"></i> تنبيهات تجديد الاشتراكات
            </h2>
        </div>
    </header>


    <main class="flex-grow p-6 container mx-auto">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <?php
                    $is_expired = (strtotime($row['formatted_end_date']) < strtotime($today_date));
                    $phone = $row['phone'];
                    $masked_phone = substr($phone, 0, 4) . '•••' . substr($phone, -3);

                    $msg = "🔔 تنبيه من MY GYM\n\nعزيزي " . $row['first_name'] . "،\nاشتراكك سينتهي قريباً بتاريخ " . $row['formatted_end_date'] . "\n\nالرجاء تجديد الاشتراك للحفاظ على عضوية النادي\n\nشكراً لثقتك بنا 💪";
                    $wa_link = "https://wa.me/2" . $phone . "?text=" . urlencode($msg);
                    ?>
                    <div id="card-<?= $row['id'] ?>" class="card <?= $is_expired ? 'expired-card' : '' ?> rounded-lg p-5">
                        <div class="flex justify-between items-start mb-3">
                            <h3 class="text-xl font-bold text-accent">
                                <?= $row['first_name'] . ' ' . $row['middle_name'] . ' ' . $row['last_name'] ?></h3>
                            <span class="status-badge <?= $is_expired ? 'expired-badge' : 'active-badge' ?>">
                                <?= $is_expired ? 'منتهي' : 'قريب' ?>
                            </span>
                        </div>

                        <div class="space-y-2 text-gray-300 mb-4">
                            <div class="flex items-center">
                                <i class="fas fa-phone text-accent mr-2"></i>
                                <span class="masked-phone"><?= $masked_phone ?></span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-calendar-day text-accent mr-2"></i>
                                <span>ينتهي في: <span
                                        class="font-bold <?= $is_expired ? 'text-red-400' : 'text-yellow-400' ?>"><?= $row['formatted_end_date'] ?></span></span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-dumbbell text-accent mr-2"></i>
                                <span><?= $row['subscription_type'] ?></span>
                            </div>
                        </div>

                        <div class="flex justify-between">
                            <a href="<?= $wa_link ?>" target="_blank" onclick="hideCard(<?= $row['id'] ?>)"
                                class="btn-whatsapp text-white px-4 py-2 rounded-lg flex items-center">
                                <i class="fab fa-whatsapp mr-2"></i> إرسال تنبيه
                            </a>

                            <a href="renew.php?id=<?= $row['id'] ?>"
                                class="btn-primary text-dark font-bold px-4 py-2 rounded-lg flex items-center">
                                <i class="fas fa-sync-alt mr-2"></i> تجديد
                            </a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-span-full text-center py-12">
                    <div class="bg-gray-800 bg-opacity-50 rounded-xl p-8 max-w-md mx-auto">
                        <i class="fas fa-check-circle text-5xl text-green-400 mb-4"></i>
                        <h3 class="text-xl font-bold text-gray-200 mb-2">لا يوجد تنبيهات حالية</h3>
                        <p class="text-gray-400">جميع المشتركين لديهم اشتراكات نشطة أو تم تنبيههم مسبقاً</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>


    <div class="copyright">
        © 2025 0xmaro - MY GYM
    </div>


    <a href="coach.php" class="floating-btn bg-accent hover:bg-accent-dark text-white shadow-lg transition">
        <i class="fas fa-arrow-left text-xl"></i>
    </a>

    <script>

function hideCard(id) {
            const card = document.getElementById('card-' + id);
            if (card) {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';

                fetch("", {
                    method: "POST",
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: "notify_id=" + id
                }).then(() => {
                    setTimeout(() => {
                        card.style.display = "none";
                    }, 300);
                });
            }
        }
    </script>
</body>

</html>