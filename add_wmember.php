<?php
include 'auth.php';
?>
<?php

try {
    $pdo = new PDO("mysql:host=localhost;dbname=mygym;charset=utf8", "root", "root");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("فشل الاتصال: " . $e->getMessage());
}

$today = (new DateTime())->format('Y-m-d');
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first = $_POST['first_name'];
    $middle = $_POST['middle_name'];
    $last = $_POST['last_name'];
    $phone = $_POST['phone'];
    $type = $_POST['subscription_type'];
    $duration = $_POST['subscription_duration'];
    $age = $_POST['age'];
    $start_date = $_POST['start_date'];
    $coach = $_POST['coach_name'];
    $notes = $_POST['notes'];
    $renewed_at = (new DateTime())->format('Y-m-d H:i:s');

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM womembers WHERE first_name=? AND middle_name=? AND last_name=? AND phone=?");
    $stmt->execute([$first, $middle, $last, $phone]);

    if ($stmt->fetchColumn() > 0) {
        $message = "<div class='error-message animate-pulse flex items-center justify-center p-3 rounded-lg'>
                      <i class='fas fa-exclamation-circle mr-2'></i>
                      هذه المشتركة مسجلة بالفعل
                    </div>";
    } else {

        $duration_map = [
            'شهر' => '1 month',
            '3 شهور' => '3 months',
            '6 شهور' => '6 months',
            'سنة' => '1 year'
        ];
        $end_date = date('Y-m-d', strtotime("$start_date +" . $duration_map[$duration]));

        $insert = $pdo->prepare("INSERT INTO womembers (first_name, middle_name, last_name, phone, subscription_type, subscription_duration, age, start_date, end_date, coach_name, notes, renewed_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $insert->execute([$first, $middle, $last, $phone, $type, $duration, $age, $start_date, $end_date, $coach, $notes, $renewed_at]);


        echo '<audio id="successSound" src="success.mp3" preload="auto"></audio>';
        echo "<script>
                document.getElementById('successSound').play();
                setTimeout(() => {
                    const msg = 'مرحبًا بكِ في MyGym! تم تسجيلك بتاريخ $renewed_at. نتمنى لكِ رحلة تدريبية ممتعة وصحية معنا!';
                    const whatsapp_link = 'https://wa.me/2$phone?text=' + encodeURIComponent(msg);
                    window.open(whatsapp_link, '_blank');
                    setTimeout(() => window.location.href = 'wcoach.php', 3000);
                }, 1000);
            </script>";
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إضافة مشتركة - MyGym</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#831843',
                        secondary: '#9d174d',
                        accent: '#ec4899',
                        dark: '#500724',
                        light: '#fdf2f8',
                        danger: '#e53e3e'
                    },
                    fontFamily: {
                        'tajawal': ['Tajawal', 'sans-serif']
                    },
                    animation: {
                        'float': 'float 3s ease-in-out infinite',
                        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        'bounce-slow': 'bounce 2s infinite',
                        'heartbeat': 'heartbeat 1.5s ease-in-out infinite'
                    }
                }
            }
        }
    </script>
    <style>
        @keyframes float {
            0% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-8px);
            }

            100% {
                transform: translateY(0px);
            }
        }

        @keyframes heartbeat {
            0% {
                transform: scale(1);
            }

            25% {
                transform: scale(1.1);
            }

            50% {
                transform: scale(1);
            }

            75% {
                transform: scale(1.1);
            }

            100% {
                transform: scale(1);
            }
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

        .animate-float {
            animation: float 3s ease-in-out infinite;
        }

        .animate-fade-in {
            animation: fadeIn 0.8s ease-out;
        }

        body {
            font-family: 'Tajawal', sans-serif;
            background: linear-gradient(135deg, #fce7f3 0%, #fbcfe8 100%);
            color: #831843;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 1rem;
        }

        .card {
            background: rgba(253, 242, 248, 0.95);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(236, 72, 153, 0.15);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .card:hover {
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
            transform: translateY(-5px);
        }

        .input-field {
            background: rgba(255, 255, 255, 0.8);
            border: 1px solid rgba(236, 72, 153, 0.2);
            transition: all 0.3s ease;
        }

        .input-field:focus {
            background: rgba(255, 255, 255, 0.9);
            border-color: #ec4899;
            box-shadow: 0 0 0 3px rgba(236, 72, 153, 0.2);
        }

        .input-field:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background: linear-gradient(135deg, #ec4899 0%, #d946ef 100%);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 15px rgba(236, 72, 153, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 6px 20px rgba(236, 72, 153, 0.4);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.8);
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.9);
        }

        .error-message {
            background: rgba(229, 62, 62, 0.15);
            border: 1px solid rgba(229, 62, 62, 0.3);
            color: #e53e3e;
        }

        .title {
            position: relative;
            display: inline-block;
        }

        .title::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            width: 60%;
            height: 3px;
            background: linear-gradient(90deg, #ec4899, transparent);
            border-radius: 3px;
        }

        .heart-icon {
            filter: drop-shadow(0 0 8px rgba(236, 72, 153, 0.5));
        }

        .floral-pattern {
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ec4899' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h6v-2H6zM6 4V0H4v4H0v2h4v4h2V6h6V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
    </style>
</head>

<body>
    <div class="card rounded-2xl p-8 w-full max-w-4xl animate-fade-in floral-pattern">
        <div class="text-center mb-8">
            <div class="flex items-center justify-center mb-4">
                <i class="fas fa-heart text-4xl text-accent mr-3 heart-icon animate-float"></i>
                <h1 class="text-4xl font-bold text-accent title">MY - <span class="text-primary"> GYM </span></h1>
            </div>
            <h2 class="text-2xl font-semibold text-primary">إضافة مشتركة جديدة</h2>
        </div>

        <?php echo $message; ?>

        <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6"
            onsubmit="return confirm('هل أنت متأكد من إضافة هذه المشتركة؟')">

            <div class="relative group">
                <label class="block mb-2 font-medium text-primary">
                    <i class="fas fa-user ml-2"></i>
                    الاسم الأول
                </label>
                <input type="text" name="first_name" placeholder="أدخل الاسم الأول" required
                    class="input-field w-full p-3 rounded-lg text-dark placeholder-pink-300">
            </div>

            <div class="relative group">
                <label class="block mb-2 font-medium text-primary">
                    <i class="fas fa-user ml-2"></i>
                    الاسم الأوسط
                </label>
                <input type="text" name="middle_name" placeholder="أدخل الاسم الأوسط"
                    class="input-field w-full p-3 rounded-lg text-dark placeholder-pink-300">
            </div>


            <div class="relative group">
                <label class="block mb-2 font-medium text-primary">
                    <i class="fas fa-user ml-2"></i>
                    الاسم الأخير
                </label>
                <input type="text" name="last_name" placeholder="أدخل الاسم الأخير"
                    class="input-field w-full p-3 rounded-lg text-dark placeholder-pink-300">
            </div>

            <div class="relative group">
                <label class="block mb-2 font-medium text-primary">
                    <i class="fas fa-phone ml-2"></i>
                    رقم الهاتف
                </label>
                <input type="text" name="phone" placeholder="أدخل رقم الهاتف" maxlength="11" required
                    class="input-field w-full p-3 rounded-lg text-dark placeholder-pink-300">
            </div>


            <div class="relative group">
                <label class="block mb-2 font-medium text-primary">
                    <i class="fas fa-tag ml-2"></i>
                    نوع الاشتراك
                </label>
                <select name="subscription_type" class="input-field w-full p-3 rounded-lg text-dark">
                    <option value="حديد">حديد</option>
                    <option value="اجهزه">أجهزة</option>
                    <option value="private">Private</option>
                </select>
            </div>

            <div class="relative group">
                <label class="block mb-2 font-medium text-primary">
                    <i class="fas fa-calendar-alt ml-2"></i>
                    مدة الاشتراك
                </label>
                <select name="subscription_duration" class="input-field w-full p-3 rounded-lg text-dark">
                    <option value="شهر">شهر</option>
                    <option value="3 شهور">3 شهور</option>
                    <option value="6 شهور">6 شهور</option>
                    <option value="سنة">سنة</option>
                </select>
            </div>


            <div class="relative group">
                <label class="block mb-2 font-medium text-primary">
                    <i class="fas fa-birthday-cake ml-2"></i>
                    العمر
                </label>
                <input type="number" name="age" placeholder="أدخل العمر"
                    class="input-field w-full p-3 rounded-lg text-dark placeholder-pink-300">
            </div>

            <div class="relative group">
                <label class="block mb-2 font-medium text-primary">
                    <i class="fas fa-user-tie ml-2"></i>
                    اسم المدربة
                </label>
                <input type="text" name="coach_name" placeholder="أدخل اسم المدربة"
                    class="input-field w-full p-3 rounded-lg text-dark placeholder-pink-300">
            </div>


            <div class="relative group">
                <label class="block mb-2 font-medium text-primary">
                    <i class="fas fa-calendar-day ml-2"></i>
                    تاريخ البدء
                </label>
                <input type="date" name="start_date" value="<?php echo $today; ?>"
                    class="input-field w-full p-3 rounded-lg text-dark">
            </div>


            <div class="md:col-span-2 relative group">
                <label class="block mb-2 font-medium text-primary">
                    <i class="fas fa-sticky-note ml-2"></i>
                    ملاحظات
                </label>
                <textarea name="notes" placeholder="أدخل أي ملاحظات"
                    class="input-field w-full p-3 rounded-lg text-dark placeholder-pink-300 h-24"></textarea>
            </div>


            <div class="md:col-span-2 flex justify-between items-center mt-8">
                <a href="wcoach.php"
                    class="btn-secondary px-6 py-3 rounded-lg font-medium text-primary flex items-center">
                    <i class="fas fa-arrow-right ml-2"></i>
                    رجوع
                </a>
                <button type="submit" class="btn-primary px-8 py-3 rounded-lg font-bold text-light flex items-center">
                    <i class="fas fa-user-plus ml-2"></i>
                    إضافة مشتركة
                </button>
            </div>
        </form>

        <div class="text-center mt-12 pt-6 border-t border-pink-200">
            <p class="text-sm text-pink-500">
                <a href="https://www.facebook.com/0xmaro" target="_blank" class="hover:text-accent transition-colors">
                    <i class="fas fa-copyright mr-1"></i> 0xmaro - MyGym 2025
                </a>
            </p>
        </div>
    </div>

    <script>

        document.addEventListener('DOMContentLoaded', () => {
            const inputs = document.querySelectorAll('input, select, textarea');
            inputs.forEach((input, index) => {
                input.style.animationDelay = `${index * 0.1}s`;
            });


            inputs.forEach(input => {
                input.addEventListener('focus', () => {
                    input.parentElement.classList.add('animate-pulse');
                });

                input.addEventListener('blur', () => {
                    input.parentElement.classList.remove('animate-pulse');
                });
            });
        });
    </script>
</body>

</html>