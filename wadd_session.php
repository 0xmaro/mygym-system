<?php
include 'auth.php';
?>
<?php
$dsn = "mysql:host=localhost;dbname=mygym;charset=utf8";
$username = "root";
$password = "root";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    die("فشل الاتصال: " . $e->getMessage());
}

$showToast = false;
$errorMsg = '';
$whatsapp_link = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = $_POST['member_fullname'];
    $session_type = $_POST['session_type'];
    $phone = $_POST['phone'];
    $age = $_POST['age'];
    $coach = $_POST['coach_name'];
    $created_at = (new DateTime())->format('Y-m-d H:i:s');
    $renewed_at = date('Y-m-d');

    try {
        if (!preg_match('/^\d{11}$/', $phone)) {
            throw new Exception("رقم الهاتف يجب أن يكون 11 رقم بالضبط.");
        }

        $stmt = $pdo->prepare("INSERT INTO sessions (member_fullname, session_type, gender, phone, age, coach_name, created_at) VALUES (?, ?, 'female', ?, ?, ?, ?)");
        $stmt->execute([$fullname, $session_type, $phone, $age, $coach, $created_at]);

        // whatsapp massage
        $session_type_arabic = ($session_type == 'normal') ? 'حصة عادية' : 'حصة أجهزة';
        $msg = "🚀 *مرحبًا بكِ في MyGym* 🚀
        🔹 *الاسم:* $fullname
        🔹 *نوع الحصة:* $session_type_arabic
        🔹 *تاريخ الحصة:* $renewed_at
        🔹 *المدربة:* $coach
        🎉 *تم تسجيل حضورك بنجاح اليوم* 
        نتمنى لكِ حصة تدريبية ممتعة ومفيدة 🌸
        ننتظركِ دائمًا في MyGym";

        $whatsapp_link = 'https://wa.me/2' . $phone . '?text=' . urlencode($msg);
        $showToast = true;
    } catch (Exception $e) {
        $errorMsg = $e->getMessage();
    } catch (PDOException $e) {
        $errorMsg = "حدث خطأ أثناء حفظ البيانات: " . $e->getMessage();
    }
}

$recentStmt = $pdo->query("SELECT member_fullname, session_type, created_at FROM sessions WHERE gender = 'female' ORDER BY created_at DESC LIMIT 3");
$recentMembers = $recentStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>تسجيل الحصص - MyGym للسيدات</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #831843;
            --secondary: #be185d;
            --accent: #f472b6;
            --light-accent: #fce7f3;
            --dark: #500724;
            --light: #fdf2f8;
            --danger: #e11d48;
            --success: #10b981;
        }

        body {
            font-family: 'Tajawal', sans-serif;
            background: linear-gradient(135deg, var(--light) 0%, var(--light-accent) 100%);
            min-height: 100vh;
        }

        .form-container {
            background: rgba(253, 242, 248, 0.95);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(244, 114, 182, 0.3);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }

        .input-field {
            background: rgba(255, 255, 255, 0.8);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(244, 114, 182, 0.3);
        }

        .input-field:focus {
            background: rgba(255, 255, 255, 0.95);
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(244, 114, 182, 0.3);
        }

        .input-field:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(190, 24, 93, 0.15);
        }

        .submit-btn {
            background: linear-gradient(135deg, var(--accent) 0%, var(--secondary) 100%);
            box-shadow: 0 4px 15px rgba(244, 114, 182, 0.4);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .submit-btn:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 6px 20px rgba(244, 114, 182, 0.6);
        }

        .submit-btn:active {
            transform: translateY(1px);
        }

        .member-card {
            background: rgba(255, 255, 255, 0.9);
            border-left: 3px solid var(--accent);
            transition: all 0.3s ease;
        }

        .member-card:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 1);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .toast {
            background: linear-gradient(135deg, var(--success) 0%, #059669 100%);
            box-shadow: 0 5px 15px rgba(16, 185, 129, 0.4);
        }

        .error-toast {
            background: linear-gradient(135deg, var(--danger) 0%, #9f1239 100%);
            box-shadow: 0 5px 15px rgba(225, 29, 72, 0.4);
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

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(244, 114, 182, 0.4);
            }

            70% {
                box-shadow: 0 0 0 10px rgba(244, 114, 182, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(244, 114, 182, 0);
            }
        }

        @keyframes float {
            0% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-5px);
            }

            100% {
                transform: translateY(0px);
            }
        }

        .fade-in {
            animation: fadeIn 0.8s ease-out;
        }

        .pulse-effect {
            animation: pulse 2s infinite;
        }

        .float-effect {
            animation: float 3s ease-in-out infinite;
        }

        .return-btn {
            background: rgba(252, 231, 243, 0.9);
            transition: all 0.3s ease;
        }

        .return-btn:hover {
            background: var(--accent);
            color: white;
            transform: rotate(-10deg) scale(1.1);
        }

        .title-text {
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .title-text::after {
            content: '';
            position: absolute;
            bottom: -8px;
            right: 0;
            width: 60%;
            height: 3px;
            background: var(--accent);
            border-radius: 3px;
        }

        .heart-beat {
            animation: heartBeat 1.5s ease infinite;
        }

        @keyframes heartBeat {
            0% {
                transform: scale(1);
            }

            14% {
                transform: scale(1.1);
            }

            28% {
                transform: scale(1);
            }

            42% {
                transform: scale(1.1);
            }

            70% {
                transform: scale(1);
            }
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-4 text-pink-900 relative overflow-x-hidden">

    <?php if ($showToast): ?>
        <div
            class="toast fixed top-6 right-6 text-white font-bold py-4 px-6 rounded-xl z-50 flex items-center animate-slideInRight">
            <i class="fas fa-check-circle text-xl mr-2"></i>
            <span>تم تسجيل الحصة بنجاح!</span>
        </div>
        <audio autoplay>
            <source src="https://assets.mixkit.co/sfx/preview/mixkit-correct-answer-tone-2870.mp3" type="audio/mp3">
        </audio>
        <script>
            setTimeout(() => {
                window.open('<?= $whatsapp_link ?>', '_blank');
                setTimeout(() => window.location.href = 'wcoach.php', 3000);
            }, 1500);
        </script>
    <?php elseif ($errorMsg): ?>
        <div
            class="error-toast fixed top-6 right-6 text-white font-bold py-4 px-6 rounded-xl z-50 flex items-center animate-slideInRight">
            <i class="fas fa-exclamation-circle text-xl mr-2"></i>
            <span><?= $errorMsg ?></span>
        </div>
    <?php endif; ?>

    <form method="post" class="form-container max-w-5xl w-full rounded-2xl p-8 md:p-10 space-y-8 fade-in relative">
        <a href="wcoach.php" class="return-btn absolute top-4 left-4 text-pink-700 p-3 rounded-full shadow-lg"
            title="رجوع">
            <i class="fas fa-arrow-left text-lg"></i>
        </a>

        <div class="text-center">
            <h1 class="title-text text-3xl md:text-4xl font-extrabold text-pink-600 mb-2">
                <i class="fas fa-heartbeat heart-beat mr-2"></i>تسجيل الحصص للسيدات
            </h1>
            <p class="text-pink-500">نظام إدارة الجيم النسائي - MyGym</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="relative group">
                <label class='block mb-2 font-bold text-pink-700 flex items-center'>
                    <i class='fas fa-user ml-2'></i>
                    الاسم الكامل
                </label>
                <input name='member_fullname' required type='text'
                    class='input-field w-full p-3 text-pink-900 rounded-lg transition-all duration-300 group-hover:shadow-lg'>
            </div>

            <div class="relative group">
                <label class='block mb-2 font-bold text-pink-700 flex items-center'>
                    <i class='fas fa-dumbbell ml-2'></i>
                    نوع الحصة
                </label>
                <select name='session_type' required
                    class='input-field w-full p-3 text-pink-900 rounded-lg transition-all duration-300 group-hover:shadow-lg'>
                    <option value="normal">عادي</option>
                    <option value="equipment">أجهزة</option>
                </select>
            </div>

            <div class="relative group">
                <label class='block mb-2 font-bold text-pink-700 flex items-center'>
                    <i class='fas fa-phone ml-2'></i>
                    رقم الهاتف
                </label>
                <input name='phone' required type='text' title='رقم الهاتف يجب أن يكون 11 رقم' maxlength='11'
                    class='input-field w-full p-3 text-pink-900 rounded-lg transition-all duration-300 group-hover:shadow-lg'>
            </div>

            <div class="relative group">
                <label class='block mb-2 font-bold text-pink-700 flex items-center'>
                    <i class='fas fa-calendar ml-2'></i>
                    العمر
                </label>
                <input name='age' required type='number'
                    class='input-field w-full p-3 text-pink-900 rounded-lg transition-all duration-300 group-hover:shadow-lg'>
            </div>

            <div class="relative group">
                <label class='block mb-2 font-bold text-pink-700 flex items-center'>
                    <i class='fas fa-user-tie ml-2'></i>
                    اسم المدربة
                </label>
                <input name='coach_name' required type='text'
                    class='input-field w-full p-3 text-pink-900 rounded-lg transition-all duration-300 group-hover:shadow-lg'>
            </div>
        </div>

        <div class="text-center pt-4">
            <button type="submit"
                class="submit-btn text-white font-bold py-3 px-12 rounded-full text-lg relative overflow-hidden">
                <span class="relative z-10 flex items-center justify-center">
                    <i class="fas fa-check-circle ml-2"></i>
                    تسجيل الحصة
                </span>
                <span class="absolute inset-0 bg-white opacity-0 hover:opacity-10 transition-opacity"></span>
            </button>
        </div>

        <div class="mt-10">
            <h2 class="text-center text-xl font-bold text-pink-600 mb-6 flex items-center justify-center">
                <i class="fas fa-history ml-2"></i>
                آخر المشتركات المسجلات
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <?php foreach ($recentMembers as $member): ?>
                    <div class="member-card rounded-xl p-4 fade-in">
                        <div class="flex items-center">
                            <div
                                class="bg-pink-500 text-white rounded-full w-10 h-10 flex items-center justify-center mr-3 float-effect">
                                <i class="fas fa-user"></i>
                            </div>
                            <div>
                                <p class="font-bold text-pink-800">
                                    <?= $member['member_fullname'] ?>
                                </p>
                                <p class="text-sm text-pink-500">
                                    <i class="fas fa-dumbbell ml-1"></i>
                                    <?= $member['session_type'] == 'normal' ? 'عادي' : 'أجهزة' ?>
                                </p>
                                <p class="text-sm text-pink-500 mt-1">
                                    <i class="far fa-clock ml-1"></i>
                                    <?= date("Y-m-d H:i", strtotime($member['created_at'])) ?>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="text-center pt-8">
            <p class="text-pink-500 text-sm">
                <a href="https://www.facebook.com/0xmaro" class="hover:text-pink-700 transition-colors">
                    <i class="fas fa-copyright mr-1"></i> 0xmaro - MyGym 2025
                </a>
            </p>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const inputs = document.querySelectorAll('input, select');
            inputs.forEach((input, index) => {
                input.style.animationDelay = `${index * 0.1}s`;
            });

            inputs.forEach(input => {
                input.addEventListener('focus', () => {
                    input.parentElement.classList.add('pulse-effect');
                });

                input.addEventListener('blur', () => {
                    input.parentElement.classList.remove('pulse-effect');
                });
            });

            const submitBtn = document.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.addEventListener('click', () => {
                    const audio = new Audio('https://assets.mixkit.co/sfx/preview/mixkit-arcade-game-jump-coin-216.mp3');
                    audio.volume = 0.3;
                    audio.play();
                });
            }
        });
    </script>
</body>

</html>