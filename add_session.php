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

        $stmt = $pdo->prepare("INSERT INTO sessions (member_fullname, session_type, gender, phone, age, coach_name, created_at) VALUES (?, ?, 'male', ?, ?, ?, ?)");
        $stmt->execute([$fullname, $session_type, $phone, $age, $coach, $created_at]);

        $session_type_arabic = ($session_type == 'normal') ? 'حصة عادية' : 'حصة أجهزة';
        $msg = "🏋️ *مرحبًا بك في نادي MyGym* 🏋️

🔹 *نوع الحصة:* $session_type_arabic
🔹 *تاريخ الحصة:* $renewed_at
🔹 *المدرب:* $coach

✅ *تم تسجيل حضورك بنجاح اليوم*
نتمنى لك حصة تدريبية ممتعة ومفيدة 💪

ننتظرك دائمًا في نادي MyGym";

        $whatsapp_link = 'https://wa.me/2' . $phone . '?text=' . urlencode($msg);
        $showToast = true;
    } catch (Exception $e) {
        $errorMsg = $e->getMessage();
    } catch (PDOException $e) {
        $errorMsg = "حدث خطأ أثناء حفظ البيانات: " . $e->getMessage();
    }
}
$recentStmt = $pdo->query("SELECT member_fullname, session_type, created_at FROM sessions WHERE gender = 'male' ORDER BY created_at DESC LIMIT 3");
$recentMembers = $recentStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>تسجيل الحصص - MyGym للرجال</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-dark: #0F1A2B;
            --primary-light: #1C2B3E;
            --form-bg: #1E1E26;
            --input-bg: #2A2A35;
            --input-text: #E0E0E0;
            --btn-gradient-from: #e2b979;
            --btn-gradient-to: #D99122;
            --btn-glow: rgba(242, 176, 63, 0.5);
            --icon-color: #FFFFFF;
            --text-color: #F0F0F0;
            --date-color: #EEEEEE;
            --card-border: #F2B03F;
        }

        body {
            font-family: 'Tajawal', sans-serif;
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-light) 100%);
            min-height: 100vh;
            color: var(--text-color);
        }

        .form-container {
            background: var(--form-bg);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        }

        .input-field {
            background: var(--input-bg);
            color: var(--input-text);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .input-field:focus {
            background: var(--input-bg);
            border-color: var(--btn-gradient-from);
            box-shadow: 0 0 0 3px var(--btn-glow);
        }

        .input-field:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        .submit-btn {
            background: linear-gradient(135deg, var(--btn-gradient-from) 0%, var(--btn-gradient-to) 100%);
            box-shadow: 0 4px 15px var(--btn-glow);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            color: white;
        }

        .submit-btn:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 6px 20px var(--btn-glow);
        }

        .submit-btn:active {
            transform: translateY(1px);
        }

        .member-card {
            background: rgba(30, 30, 38, 0.8);
            border-right: 3px solid var(--card-border);
            transition: all 0.3s ease;
        }

        .member-card:hover {
            transform: translateY(-5px);
            background: rgba(40, 40, 50, 0.9);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
        }

        .toast {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            box-shadow: 0 5px 15px rgba(16, 185, 129, 0.4);
        }

        .error-toast {
            background: linear-gradient(135deg, #dc2626 0%, #9f1239 100%);
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
                box-shadow: 0 0 0 0 var(--btn-glow);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(242, 176, 63, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(242, 176, 63, 0);
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

        @keyframes glow {
            0% {
                box-shadow: 0 0 5px var(--btn-glow);
            }
            50% {
                box-shadow: 0 0 20px var(--btn-glow);
            }
            100% {
                box-shadow: 0 0 5px var(--btn-glow);
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

        .glow-effect {
            animation: glow 2s ease-in-out infinite;
        }

        .return-btn {
            background: rgba(42, 42, 53, 0.9);
            color: var(--icon-color);
            transition: all 0.3s ease;
        }

        .return-btn:hover {
            background: var(--btn-gradient-from);
            color: white;
            transform: rotate(-10deg) scale(1.1);
        }

        .title-text {
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
            position: relative;
        }

        .title-text::after {
            content: '';
            position: absolute;
            bottom: -8px;
            right: 0;
            width: 60%;
            height: 3px;
            background: var(--btn-gradient-from);
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

        @keyframes typing {
            from { width: 0 }
            to { width: 100% }
        }

        .typing-animation {
            overflow: hidden;
            white-space: nowrap;
            animation: typing 3.5s steps(40, end);
        }

        .hover-grow {
            transition: transform 0.3s ease;
        }
        .hover-grow:hover {
            transform: scale(1.05);
        }

        .transition-all {
            transition: all 0.3s ease;
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-4 relative overflow-x-hidden">

    <?php if ($showToast): ?>
        <div class="toast fixed top-6 right-6 text-white font-bold py-4 px-6 rounded-xl z-50 flex items-center animate-slideInRight">
            <i class="fas fa-check-circle text-xl mr-2"></i>
            <span>تم تسجيل الحصة بنجاح!</span>
        </div>
        <audio autoplay>
            <source src="https://assets.mixkit.co/sfx/preview/mixkit-correct-answer-tone-2870.mp3" type="audio/mp3">
        </audio>
        <script>
            setTimeout(() => {
                window.open('<?= $whatsapp_link ?>', '_blank');
                setTimeout(() => window.location.href = 'coach.php', 3000);
            }, 1500);
        </script>
    <?php elseif ($errorMsg): ?>
        <div class="error-toast fixed top-6 right-6 text-white font-bold py-4 px-6 rounded-xl z-50 flex items-center animate-slideInRight">
            <i class="fas fa-exclamation-circle text-xl mr-2"></i>
            <span><?= $errorMsg ?></span>
        </div>
    <?php endif; ?>

    <form method="post" class="form-container max-w-5xl w-full rounded-2xl p-8 md:p-10 space-y-8 fade-in relative">
        <a href="coach.php" class="return-btn absolute top-4 left-4 p-3 rounded-full shadow-lg" title="رجوع">
            <i class="fas fa-arrow-left text-lg"></i>
        </a>

        <div class="text-center">
            <h1 class="title-text text-3xl md:text-4xl font-extrabold mb-2 text-white">
                <i class="fas fa-dumbbell heart-beat mr-2"></i>تسجيل الحصص للرجال
            </h1>
            <p class="text-gray-300">نظام إدارة الجيم الرجالي - MyGym</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="relative group">
                <label class='block mb-2 font-bold flex items-center text-gray-300'>
                    <i class='fas fa-user ml-2'></i>
                    الاسم الكامل
                </label>
                <input name='member_fullname' required type='text'
                    class='input-field w-full p-3 rounded-lg transition-all duration-300 group-hover:shadow-lg'>
            </div>

            <div class="relative group">
                <label class='block mb-2 font-bold flex items-center text-gray-300'>
                    <i class='fas fa-dumbbell ml-2'></i>
                    نوع الحصة
                </label>
                <select name='session_type' required
                    class='input-field w-full p-3 rounded-lg transition-all duration-300 group-hover:shadow-lg'>
                    <option value="normal">عادي</option>
                    <option value="equipment">أجهزة</option>
                </select>
            </div>

            <div class="relative group">
                <label class='block mb-2 font-bold flex items-center text-gray-300'>
                    <i class='fas fa-phone ml-2'></i>
                    رقم الهاتف
                </label>
                <input name='phone' required type='text' title='رقم الهاتف يجب أن يكون 11 رقم' maxlength='11'
                    class='input-field w-full p-3 rounded-lg transition-all duration-300 group-hover:shadow-lg'>
            </div>

            <div class="relative group">
                <label class='block mb-2 font-bold flex items-center text-gray-300'>
                    <i class='fas fa-calendar ml-2'></i>
                    العمر
                </label>
                <input name='age' required type='number'
                    class='input-field w-full p-3 rounded-lg transition-all duration-300 group-hover:shadow-lg'>
            </div>

            <div class="relative group">
                <label class='block mb-2 font-bold flex items-center text-gray-300'>
                    <i class='fas fa-user-tie ml-2'></i>
                    اسم المدرب
                </label>
                <input name='coach_name' required type='text'
                    class='input-field w-full p-3 rounded-lg transition-all duration-300 group-hover:shadow-lg'>
            </div>
        </div>

        <div class="text-center pt-4">
            <button type="submit"
                class="submit-btn font-bold py-3 px-12 rounded-full text-lg relative overflow-hidden hover-glow">
                <span class="relative z-10 flex items-center justify-center">
                    <i class="fas fa-check-circle ml-2"></i>
                    تسجيل الحصة
                </span>
                <span class="absolute inset-0 bg-white opacity-0 hover:opacity-10 transition-opacity"></span>
            </button>
        </div>

        <div class="mt-10">
            <h2 class="text-center text-xl font-bold mb-6 flex items-center justify-center text-white">
                <i class="fas fa-history ml-2"></i>
                آخر المشتركين المسجلين
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <?php foreach ($recentMembers as $member): ?>
                    <div class="member-card rounded-xl p-4 fade-in hover-grow">
                        <div class="flex items-center">
                            <div class="bg-yellow-500 text-white rounded-full w-10 h-10 flex items-center justify-center mr-3 float-effect">
                                <i class="fas fa-user"></i>
                            </div>
                            <div>
                                <p class="font-bold text-white">
                                    <?= $member['member_fullname'] ?>
                                </p>
                                <p class="text-sm text-gray-300">
                                    <i class="fas fa-dumbbell ml-1"></i>
                                    <?= $member['session_type'] == 'normal' ? 'عادي' : 'أجهزة' ?>
                                </p>
                                <p class="text-sm text-gray-400 mt-1">
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
            <p class="text-gray-400 text-sm">
                <a href="https://www.facebook.com/0xmaro" class="hover:text-yellow-400 transition-colors">
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
                submitBtn.addEventListener('mouseenter', () => {
                    submitBtn.classList.add('glow-effect');
                });
                submitBtn.addEventListener('mouseleave', () => {
                    submitBtn.classList.remove('glow-effect');
                });
                
                submitBtn.addEventListener('click', () => {
                    const audio = new Audio('https://assets.mixkit.co/sfx/preview/mixkit-arcade-game-jump-coin-216.mp3');
                    audio.volume = 0.3;
                    audio.play();
                });
            }
            

            const title = document.querySelector('.title-text');
            if (title) {
                title.style.overflow = 'hidden';
                title.style.whiteSpace = 'nowrap';
                title.style.animation = 'typing 3.5s steps(40, end)';
            }
        });
    </script>
</body>

</html>