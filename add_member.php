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

  $stmt = $pdo->prepare("SELECT COUNT(*) FROM members WHERE first_name=? AND middle_name=? AND last_name=? AND phone=?");
  $stmt->execute([$first, $middle, $last, $phone]);

  if ($stmt->fetchColumn() > 0) {
    $message = "<div class='error-message animate-pulse flex items-center justify-center p-3 rounded-lg'>
                  <i class='fas fa-exclamation-circle mr-2'></i>
                  هذا المشترك مسجل بالفعل
                </div>";
  } else {

    $duration_map = [
      'شهر' => '1 month',
      '3 شهور' => '3 months',
      '6 شهور' => '6 months',
      'سنة' => '1 year'
    ];
    $end_date = date('Y-m-d', strtotime("$start_date +" . $duration_map[$duration]));

    $insert = $pdo->prepare("INSERT INTO members (first_name, middle_name, last_name, phone, subscription_type, subscription_duration, age, start_date, end_date, coach_name, notes, renewed_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $insert->execute([$first, $middle, $last, $phone, $type, $duration, $age, $start_date, $end_date, $coach, $notes, $renewed_at]);

    $msg = "مرحبًا بك في MyGym! تم تسجيلك بتاريخ $renewed_at. نتمنى لك رحلة تدريبية ممتعة وصحية معنا!";
    $whatsapp_link = "https://wa.me/2$phone?text=" . urlencode($msg);

    echo "<script>
            window.open('$whatsapp_link', '_blank');
            setTimeout(() => window.location.href = 'coach.php', 3000);
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
  <title>إضافة مشترك - MyGym</title>
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: '#1a365d',
            secondary: '#2c5282',
            accent: '#e2b979',
            dark: '#1a202c',
            light: '#f7fafc',
            danger: '#e53e3e'
          },
          fontFamily: {
            'tajawal': ['Tajawal', 'sans-serif']
          },
          animation: {
            'float': 'float 3s ease-in-out infinite',
            'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
            'bounce-slow': 'bounce 2s infinite'
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
      background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
      color: #f7fafc;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 1rem;
    }

    .card {
      background: rgba(26, 32, 44, 0.95);
      backdrop-filter: blur(12px);
      border: 1px solid rgba(226, 185, 121, 0.15);
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .card:hover {
      box-shadow: 0 15px 40px rgba(0, 0, 0, 0.5);
      transform: translateY(-5px);
    }

    .input-field {
      background: rgba(247, 250, 252, 0.08);
      border: 1px solid rgba(226, 185, 121, 0.2);
      transition: all 0.3s ease;
    }

    .input-field:focus {
      background: rgba(247, 250, 252, 0.12);
      border-color: #e2b979;
      box-shadow: 0 0 0 3px rgba(226, 185, 121, 0.2);
    }

    .input-field:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .btn-primary {
      background: linear-gradient(135deg, #e2b979 0%, #d69e2e 100%);
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      box-shadow: 0 4px 15px rgba(214, 158, 46, 0.3);
    }

    .btn-primary:hover {
      transform: translateY(-3px) scale(1.02);
      box-shadow: 0 6px 20px rgba(214, 158, 46, 0.4);
    }

    .btn-secondary {
      background: rgba(247, 250, 252, 0.08);
      transition: all 0.3s ease;
    }

    .btn-secondary:hover {
      background: rgba(247, 250, 252, 0.15);
    }

    .error-message {
      background: rgba(229, 62, 62, 0.15);
      border: 1px solid rgba(229, 62, 62, 0.3);
      color: #fc8181;
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
      background: linear-gradient(90deg, #e2b979, transparent);
      border-radius: 3px;
    }

    .dumbbell-icon {
      filter: drop-shadow(0 0 8px rgba(226, 185, 121, 0.5));
    }
  </style>
</head>

<body>
  <div class="card rounded-2xl p-8 w-full max-w-4xl animate-fade-in">
    <div class="text-center mb-8">
      <div class="flex items-center justify-center mb-4">
        <i class="fas fa-dumbbell text-4xl text-accent mr-3 dumbbell-icon animate-float"></i>
        <h1 class="text-4xl font-bold text-accent title">MY - <span class="text-light"> GYM </span></h1>
      </div>
      <h2 class="text-2xl font-semibold text-light">إضافة مشترك جديد</h2>
    </div>

    <?php echo $message; ?>

    <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">

    <div class="relative group">
        <label class="block mb-2 font-medium text-accent">
          <i class="fas fa-user ml-2"></i>
          الاسم الأول
        </label>
        <input type="text" name="first_name" placeholder="أدخل الاسم الأول" required
          class="input-field w-full p-3 rounded-lg text-light placeholder-gray-500">
      </div>

      <div class="relative group">
        <label class="block mb-2 font-medium text-accent">
          <i class="fas fa-user ml-2"></i>
          الاسم الأوسط
        </label>
        <input type="text" name="middle_name" placeholder="أدخل الاسم الأوسط"
          class="input-field w-full p-3 rounded-lg text-light placeholder-gray-500">
      </div>


      <div class="relative group">
        <label class="block mb-2 font-medium text-accent">
          <i class="fas fa-user ml-2"></i>
          الاسم الأخير
        </label>
        <input type="text" name="last_name" placeholder="أدخل الاسم الأخير"
          class="input-field w-full p-3 rounded-lg text-light placeholder-gray-500">
      </div>

      <div class="relative group">
        <label class="block mb-2 font-medium text-accent">
          <i class="fas fa-phone ml-2"></i>
          رقم الهاتف
        </label>
        <input type="text" name="phone" placeholder="أدخل رقم الهاتف" maxlength="11" required
          class="input-field w-full p-3 rounded-lg text-light placeholder-gray-500">
      </div>


      <div class="relative group">
        <label class="block mb-2 font-medium text-accent">
          <i class="fas fa-tag ml-2"></i>
          نوع الاشتراك
        </label>
        <select name="subscription_type" class="input-field w-full p-3 rounded-lg text-light">
          <option value="حديد">حديد</option>
          <option value="اجهزه">أجهزة</option>
          <option value="private">Private</option>
        </select>
      </div>

      <div class="relative group">
        <label class="block mb-2 font-medium text-accent">
          <i class="fas fa-calendar-alt ml-2"></i>
          مدة الاشتراك
        </label>
        <select name="subscription_duration" class="input-field w-full p-3 rounded-lg text-light">
          <option value="شهر">شهر</option>
          <option value="3 شهور">3 شهور</option>
          <option value="6 شهور">6 شهور</option>
          <option value="سنة">سنة</option>
        </select>
      </div>


      <div class="relative group">
        <label class="block mb-2 font-medium text-accent">
          <i class="fas fa-birthday-cake ml-2"></i>
          العمر
        </label>
        <input type="number" name="age" placeholder="أدخل العمر"
          class="input-field w-full p-3 rounded-lg text-light placeholder-gray-500">
      </div>

      <div class="relative group">
        <label class="block mb-2 font-medium text-accent">
          <i class="fas fa-user-tie ml-2"></i>
          اسم المدرب
        </label>
        <input type="text" name="coach_name" placeholder="أدخل اسم المدرب"
          class="input-field w-full p-3 rounded-lg text-light placeholder-gray-500">
      </div>


      <div class="relative group">
        <label class="block mb-2 font-medium text-accent">
          <i class="fas fa-calendar-day ml-2"></i>
          تاريخ البدء
        </label>
        <input type="date" name="start_date" value="<?php echo $today; ?>"
          class="input-field w-full p-3 rounded-lg text-light">
      </div>


      <div class="md:col-span-2 relative group">
        <label class="block mb-2 font-medium text-accent">
          <i class="fas fa-sticky-note ml-2"></i>
          ملاحظات
        </label>
        <textarea name="notes" placeholder="أدخل أي ملاحظات"
          class="input-field w-full p-3 rounded-lg text-light placeholder-gray-500 h-24"></textarea>
      </div>


      <div class="md:col-span-2 flex justify-between items-center mt-8">
        <a href="coach.php" class="btn-secondary px-6 py-3 rounded-lg font-medium text-light flex items-center">
          <i class="fas fa-arrow-right ml-2"></i>
          رجوع
        </a>
        <button type="submit" class="btn-primary px-8 py-3 rounded-lg font-bold text-dark flex items-center">
          <i class="fas fa-user-plus ml-2"></i>
          إضافة مشترك
        </button>
      </div>
    </form>

    <div class="text-center mt-12 pt-6 border-t border-gray-700">
      <p class="text-sm text-gray-400">
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