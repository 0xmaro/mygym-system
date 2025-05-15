<?php
include 'auth.php';
?>
<?php
$conn = new mysqli("localhost", "root", "root", "mygym");
if ($conn->connect_error) {
    die("فشل الاتصال بقاعدة البيانات: " . $conn->connect_error);
}

if (!isset($_GET['id'])) {
    header("Location: coach.php");
    exit();
}

$id = intval($_GET['id']);
$member = $conn->query("SELECT * FROM members WHERE id = $id")->fetch_assoc();

if (!$member) {
    echo "المشترك غير موجود.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $last_name = $_POST['last_name'];
    $subscription_type = $_POST['subscription_type'];
    $subscription_duration = $_POST['subscription_duration'];
    $start_date = $_POST['start_date'];
    $notes = $conn->real_escape_string($_POST['notes']);
    $renewed_by = $conn->real_escape_string($_POST['renewed_by']);

    $duration_map = [
        'شهر' => '+1 month',
        '3 شهور' => '+3 months',
        '6 شهور' => '+6 months',
        'سنة' => '+1 year'
    ];

    $start = new DateTime($start_date);
    $end = clone $start;
    $end->modify($duration_map[$subscription_duration]);
    $end_date = $end->format('Y-m-d');

    $sql = "UPDATE members SET 
        last_name = '$last_name',
        previous_coach_name = coach_name,
        coach_name = '$renewed_by',
        renewed_by = '$renewed_by',
        renewed_at = NOW(),
        start_date = '$start_date',
        end_date = '$end_date',
        subscription_type = '$subscription_type',
        subscription_duration = '$subscription_duration',
        notes = '$notes',
        notified_before_expiry = 0
        WHERE id = $id";

    if ($conn->query($sql)) {
        $phone = preg_replace('/[^0-9]/', '', $member['phone']);
        $now = (new DateTime())->format('Y-m-d H:i:s');
        $msg = "أهلاً تم تجديد اشتراكك حتى $end_date 🗓️ في الساعة $now. نشوفك في التمرين 💪🏋️";
        $whatsapp_link = "https://wa.me/2$phone?text=" . urlencode($msg);

        echo "<script>
            window.open('$whatsapp_link', '_blank');
            setTimeout(() => window.location.href = 'coach.php', 3000);
        </script>";
        exit();
    } else {
        echo "حصل خطأ أثناء تحديث البيانات.";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>تجديد الاشتراك | MY GYM </title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #1a365d;
            --secondary: #2c5282;
            --accent: #e63946;
            --accent2: #457b9d;
            --accent3: #a8dadc;
            --dark: #1d3557;
            --light: #f1faee;
            --danger: #e63946;
        }

        body {
            font-family: 'Tajawal', sans-serif;
            background: linear-gradient(135deg, var(--dark) 0%, var(--primary) 100%);
            min-height: 100vh;
            color: var(--light);
        }

        .card-gradient {
            background: rgba(29, 53, 87, 0.9);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(168, 218, 220, 0.3);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
            border-radius: 15px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--accent) 0%, #c1121f 100%);
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(230, 57, 70, 0.4);
            color: white;
            font-weight: bold;
            border: none;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(230, 57, 70, 0.6);
            background: linear-gradient(135deg, #e63946 0%, #a4161a 100%);
        }

        .btn-secondary {
            background: rgba(241, 250, 238, 0.1);
            border: 1px solid var(--accent3);
            transition: all 0.3s ease;
            color: var(--accent3);
        }

        .btn-secondary:hover {
            background: rgba(168, 218, 220, 0.2);
            border-color: var(--accent3);
            color: white;
        }

        .input-field {
            background: rgba(241, 250, 238, 0.1);
            border: 1px solid rgba(168, 218, 220, 0.3);
            transition: all 0.3s ease;
            color: white;
        }

        .input-field:focus {
            background: rgba(241, 250, 238, 0.15);
            border-color: var(--accent3);
            box-shadow: 0 0 0 3px rgba(69, 123, 157, 0.3);
        }

        .input-field-disabled {
            background: rgba(241, 250, 238, 0.05);
            border: 1px solid rgba(168, 218, 220, 0.1);
            color: rgba(241, 250, 238, 0.6);
        }

        .header-gradient {
            background: linear-gradient(135deg, var(--dark) 0%, var(--primary) 100%);
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.4);
            border-bottom: 1px solid rgba(168, 218, 220, 0.2);
        }

        .animate-float {
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-15px);
            }

            100% {
                transform: translateY(0px);
            }
        }

        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.03);
            }
        }

        .fade-in {
            animation: fadeIn 0.8s ease-out forwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .slide-in-right {
            animation: slideInRight 0.8s ease-out forwards;
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(50px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .gym-icon {
            position: absolute;
            opacity: 0.08;
            z-index: 0;
            color: var(--accent3);
        }

        .gym-left {
            left: 5%;
            top: 20%;
            animation: rotate 25s linear infinite;
        }

        .gym-right {
            right: 5%;
            bottom: 20%;
            animation: rotateReverse 30s linear infinite;
        }

        @keyframes rotate {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        @keyframes rotateReverse {
            from {
                transform: rotate(360deg);
            }

            to {
                transform: rotate(0deg);
            }
        }

        .label-accent {
            color: var(--accent3);
            text-shadow: 0 0 8px rgba(168, 218, 220, 0.3);
        }

        .copyright {
            color: rgba(168, 218, 220, 0.6);
            transition: all 0.3s ease;
        }

        .copyright:hover {
            color: var(--accent3);
            text-shadow: 0 0 8px rgba(168, 218, 220, 0.5);
        }

        .divider {
            height: 2px;
            background: linear-gradient(90deg, transparent 0%, var(--accent3) 50%, transparent 100%);
            margin: 1.5rem 0;
            opacity: 0.3;
        }

        .glow-text {
            text-shadow: 0 0 10px rgba(168, 218, 220, 0.7);
        }

        .select-arrow {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%23a8dadc' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 0.75rem center;
            background-repeat: no-repeat;
            background-size: 1.5em 1.5em;
        }
    </style>
</head>

<body class="relative overflow-x-hidden">


<i class="fas fa-dumbbell gym-icon gym-left text-9xl"></i>
    <i class="fas fa-fire gym-icon gym-right text-8xl"></i>


    <header class="header-gradient p-6 shadow-lg">
        <div class="container mx-auto flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center space-x-3">
                <div
                    class="h-12 w-12 rounded-full bg-gradient-to-br from-accent to-red-900 shadow-lg flex items-center justify-center">
                    <i class="fas fa-dumbbell text-white"></i>
                </div>
                <span class="text-2xl font-bold text-accent3">MY<span class="text-white"> - GYM </span><span
                        class="text-accent"></span></span>
            </div>

            <h1 class="text-3xl font-bold text-center slide-in-right">
                <span class="text-accent3">تجديد</span> <span class="text-white">الاشتراك</span>
            </h1>

            <a href="coach.php" class="btn-secondary px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-arrow-left ml-2"></i> رجوع للقائمة
            </a>
        </div>
    </header>


    <main class="container mx-auto p-4 md:p-6 max-w-6xl relative z-10">
        <div class="card-gradient p-6 md:p-8 lg:p-10 fade-in">
            <div class="text-center mb-8">
                <h2 class="text-3xl md:text-4xl font-extrabold mb-4">
                    <span class="text-accent3">تجديد اشتراك</span> <span class="text-white">المشترك</span>
                </h2>
                <div class="divider"></div>
                <p class="text-gray-300 max-w-2xl mx-auto">قم بتحديث بيانات الاشتراك وإرسال إشعار للمشترك عبر الواتساب
                </p>
            </div>

            <form method="POST" class="space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div class="space-y-6">

                <div>
                            <label class="block text-sm font-bold label-accent mb-2">الاسم الأول</label>
                            <input type="text" value="<?= $member['first_name'] ?>" disabled
                                class="input-field input-field-disabled w-full px-4 py-3 rounded-lg">
                        </div>


                        <div>
                            <label class="block text-sm font-bold label-accent mb-2">الاسم الأوسط</label>
                            <input type="text" value="<?= $member['middle_name'] ?>" disabled
                                class="input-field input-field-disabled w-full px-4 py-3 rounded-lg">
                        </div>


                        <div>
                            <label class="block text-sm font-bold label-accent mb-2">الاسم الثالث (قابل للتعديل)</label>
                            <input type="text" name="last_name" value="<?= $member['last_name'] ?>"
                                class="input-field w-full px-4 py-3 rounded-lg focus:outline-none">
                        </div>


                        <div>
                            <label class="block text-sm font-bold label-accent mb-2">رقم الهاتف</label>
                            <input type="text" value="<?= $member['phone'] ?>" disabled
                                class="input-field input-field-disabled w-full px-4 py-3 rounded-lg">
                        </div>
                    </div>


                    <div class="space-y-6">

                    <div>
                            <label class="block text-sm font-bold label-accent mb-2">العمر</label>
                            <input type="text" value="<?= $member['age'] ?>" disabled
                                class="input-field input-field-disabled w-full px-4 py-3 rounded-lg">
                        </div>


                        <div>
                            <label class="block text-sm font-bold label-accent mb-2">الكابتن السابق</label>
                            <input type="text" value="<?= $member['coach_name'] ?>" disabled
                                class="input-field input-field-disabled w-full px-4 py-3 rounded-lg font-bold">
                            <?php if ($member['renewed_at']): ?>
                                <p class="text-xs text-gray-400 mt-2">آخر تجديد: <?= $member['renewed_at'] ?></p>
                            <?php endif; ?>
                        </div>


                        <div>
                            <label class="block text-sm font-bold label-accent mb-2">الكابتن الحالي</label>
                            <input type="text" name="renewed_by" placeholder="اسم الكابتن الحالي" required
                                class="input-field w-full px-4 py-3 rounded-lg focus:outline-none">
                        </div>
                    </div>
                </div>


                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div>
                        <label class="block text-sm font-bold label-accent mb-2">نوع الاشتراك</label>
                        <select name="subscription_type"
                            class="input-field select-arrow w-full px-4 py-3 rounded-lg focus:outline-none appearance-none">
                            <option value="حديد" <?= $member['subscription_type'] === 'حديد' ? 'selected' : '' ?>>عادي
                            </option>
                            <option value="اجهزه" <?= $member['subscription_type'] === 'اجهزه' ? 'selected' : '' ?>>عضوية شاملة
                            </option>
                            <option value="private" <?= $member['subscription_type'] === 'private' ? 'selected' : '' ?>>
                                Private</option>
                        </select>
                    </div>


                    <div>
                        <label class="block text-sm font-bold label-accent mb-2">مدة الاشتراك</label>
                        <select name="subscription_duration"
                            class="input-field select-arrow w-full px-4 py-3 rounded-lg focus:outline-none appearance-none">
                            <option value="شهر" <?= $member['subscription_duration'] === 'شهر' ? 'selected' : '' ?>>شهر
                                واحد</option>
                            <option value="3 شهور" <?= $member['subscription_duration'] === '3 شهور' ? 'selected' : '' ?>>3
                                شهور</option>
                            <option value="6 شهور" <?= $member['subscription_duration'] === '6 شهور' ? 'selected' : '' ?>>6
                                شهور</option>
                            <option value="سنة" <?= $member['subscription_duration'] === 'سنة' ? 'selected' : '' ?>>سنة
                            </option>
                        </select>
                    </div>
                </div>


                <div>
                    <label class="block text-sm font-bold label-accent mb-2">تاريخ بداية الاشتراك</label>
                    <input type="date" name="start_date" value="<?= $member['end_date'] ?>" required
                        class="input-field w-full px-4 py-3 rounded-lg focus:outline-none">
                </div>


                <div>
                    <label class="block text-sm font-bold label-accent mb-2">ملاحظات</label>
                    <textarea name="notes" rows="3"
                        class="input-field w-full px-4 py-3 rounded-lg focus:outline-none"><?= $member['notes'] ?></textarea>
                </div>


                <div class="flex flex-col md:flex-row justify-between items-center pt-8 gap-4">
                    <button type="submit"
                        class="btn-primary px-8 py-3 rounded-lg text-lg font-bold animate-pulse hover:animate-none w-full md:w-auto">
                        <i class="fas fa-sync-alt ml-2"></i> تجديد الاشتراك
                    </button>

                    <a href="coach.php"
                        class="btn-secondary px-6 py-2 rounded-lg flex items-center justify-center w-full md:w-auto">
                        <i class="fas fa-times ml-2"></i> إلغاء
                    </a>
                </div>
            </form>
        </div>
    </main>


    <footer class="text-center p-6">
        <a href="https://www.youtube.com/@0xmaro" target="_blank" class="copyright text-sm">
            <i class="fas fa-copyright mr-1"></i> <span class="font-bold">MY GYM </span> 2025 | <span
                class="text-accent3">0xmaro</span>
        </a>
    </footer>


    <script>
        document.querySelector('form').addEventListener('submit', function (e) {
            if (!confirm('هل أنت متأكد من تجديد الاشتراك وإرسال الإشعار للمشترك؟')) {
                e.preventDefault();
            }
        });
    </script>
</body>

</html>