<?php
include 'auth.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'wcoach') {
  header("Location: index.php");
  exit();
}

$conn = new mysqli("localhost", "root", "root", "mygym");
if ($conn->connect_error) {
  die("فشل الاتصال بقاعدة البيانات: " . $conn->connect_error);
}

$today = date("Y-m-d");
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

$query = "SELECT * FROM womembers WHERE end_date <= DATE_ADD('$today', INTERVAL 2 DAY)";
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
  <title>لوحة المدربة - MY GYM (نسائي)</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap');

    :root {
      --primary: #831843;
      --secondary: #9d174d;
      --accent: #f472b6;
      --dark: #500724;
      --light: #fdf2f8;
      --danger: #e11d48;
      --gold: #facc15;
    }

    body {
      font-family: 'Tajawal', sans-serif;
      background: linear-gradient(135deg, var(--light) 0%, #fce7f3 100%);
      min-height: 100vh;
    }

    .whatsapp-icon {
      animation: pulse 2s ease-in-out infinite;
      filter: drop-shadow(0 0 8px rgba(236, 72, 153, 0.7));
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
      background: rgba(255, 255, 255, 0.9);
      border-left: 4px solid var(--accent);
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      backdrop-filter: blur(6px);
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
      border-radius: 16px;
    }

    .member-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
      border-left-color: var(--primary);
    }

    .expired-card {
      border-left: 4px solid var(--danger);
      background: linear-gradient(90deg, rgba(255, 228, 230, 0.7) 0%, rgba(255, 255, 255, 0.9) 70%);
    }

    .nav-gradient {
      background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
      border-radius: 0 0 20px 20px;
    }

    .btn-primary {
      background: linear-gradient(135deg, var(--accent) 0%, var(--primary) 100%);
      transition: all 0.3s ease;
      box-shadow: 0 2px 5px rgba(244, 114, 182, 0.3);
      color: white;
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(244, 114, 182, 0.4);
      background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
    }

    .search-container {
      position: relative;
      width: 100%;
      max-width: 400px;
    }

    .search-input {
      background: rgba(255, 255, 255, 0.8);
      transition: all 0.3s ease;
      padding-right: 40px;
      border: 1px solid rgba(244, 114, 182, 0.3);
    }

    .search-input:focus {
      background: rgba(255, 255, 255, 0.95);
      box-shadow: 0 0 0 3px rgba(244, 114, 182, 0.3);
      border-color: var(--accent);
    }

    .search-btn {
      position: absolute;
      left: 10px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--primary);
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
      background-color: rgba(16, 185, 129, 0.2);
      color: #10b981;
    }

    .expired-badge {
      background-color: rgba(225, 29, 72, 0.2);
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
      color: rgba(131, 24, 67, 0.5);
      font-size: 12px;
      transition: all 0.3s ease;
    }

    .copyright:hover {
      color: var(--primary);
      text-shadow: 0 0 5px rgba(244, 114, 182, 0.3);
    }

    .notification-bell {
      position: relative;
      color: white;
      font-size: 20px;
      transition: all 0.3s ease;
    }

    .notification-bell:hover {
      color: var(--gold);
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

    .logo-container {
      background: linear-gradient(135deg, #fce7f3 0%, var(--accent) 100%);
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .empty-state {
      background: rgba(255, 255, 255, 0.8);
      backdrop-filter: blur(5px);
    }

    .phone-mask {
      direction: ltr;
      display: inline-block;
    }

    .fade-in {
      animation: fadeIn 0.5s ease-in;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(10px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>
</head>

<body class="text-gray-800">


<nav class="nav-gradient p-4 flex items-center justify-between">
    <div class="flex items-center space-x-3">
      <div class="logo-container h-12 w-12 rounded-full flex items-center justify-center">
        <span class="text-xl font-bold text-primary">MG</span>
      </div>
      <span class="text-2xl font-bold text-white">MY<span class="text-gold"> - GYM</span></span>
    </div>

    <h1 class="text-2xl md:text-3xl font-bold text-white text-center">
      لوحة <span class="text-gold">المدربة</span>
    </h1>

    <div class="flex items-center space-x-4">
      <div class="text-right hidden md:block">
        <div id="datetime" class="text-sm text-gold mb-1"></div>
        <div class="text-white">
          المشتركات المطلوب تجديدهن:
          <span class="font-bold text-gold"><?php echo $result->num_rows; ?></span>
        </div>
      </div>

      <a href="logout.php"
        class="bg-rose-600 hover:bg-rose-700 text-white px-3 py-1 rounded-lg transition-all duration-200 shadow">
        <i class="fas fa-sign-out-alt mr-1"></i> خروج
      </a>
    </div>
  </nav>


  <div class="container mx-auto p-4 md:p-6 max-w-6xl">

  <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
      <div class="flex gap-3 w-full md:w-auto">
        <a href="add_wmember.php" class="btn-primary font-bold px-5 py-2 rounded-lg flex items-center">
          <i class="fas fa-user-plus mr-2"></i> إضافة مشتركة
        </a>
        <a href="wadd_session.php"
          class="bg-fuchsia-600 hover:bg-fuchsia-700 text-white px-5 py-2 rounded-lg flex items-center transition-all">
          <i class="fas fa-calendar-check mr-2"></i> تسجيل حصة
        </a>
      </div>

      <form method="GET" class="search-container w-full md:w-auto">
        <button type="submit" class="search-btn">
          <i class="fas fa-search"></i>
        </button>
        <input type="text" name="search" placeholder="ابحث باسم المشتركة..."
          value="<?php echo htmlspecialchars($search); ?>"
          class="search-input w-full px-4 pl-10 py-2 rounded-lg focus:outline-none">
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
            '<i class="fas fa-dumbbell subscription-icon text-primary"></i>' :
            '<i class="fas fa-fire subscription-icon text-danger"></i>';
          ?>
          <div
            class="member-card <?php echo $is_expired ? 'expired-card' : ''; ?> rounded-lg p-4 flex flex-col md:flex-row justify-between items-start md:items-center gap-3 fade-in">
            <div class="flex-1">
              <h3 class="text-xl font-bold text-primary mb-1"><?php echo $full_name; ?></h3>
              <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600">
                <span><i class="fas fa-phone mr-1 text-secondary"></i> <span
                    class="phone-mask"><?php echo $masked_phone; ?></span></span>
                <span><?php echo $subscription_icon; ?>     <?php echo $subscription_type; ?></span>
                <span class="flex items-center">
                  <i class="fas fa-calendar-day mr-1 text-secondary"></i> <?php echo $row['end_date']; ?>
                  <span class="status-badge <?php echo $is_expired ? 'expired-badge' : 'active-badge'; ?> ml-2">
                    <?php echo $is_expired ? 'منتهي' : 'نشط'; ?>
                  </span>
                </span>
              </div>
            </div>
            <a href="reneww.php?id=<?php echo $row['id']; ?>"
              class="btn-primary font-bold px-4 py-1 rounded-lg flex items-center">
              <i class="fas fa-sync-alt mr-2"></i> تجديد
            </a>
          </div>
        <?php endwhile; ?>
      </div>
    <?php else: ?>
      <div class="text-center py-16 fade-in">
        <div class="empty-state rounded-lg p-8 max-w-md mx-auto">
          <i class="fas fa-check-circle text-5xl text-green-400 mb-4"></i>
          <h3 class="text-xl font-bold text-primary mb-2">لا يوجد تجديدات حالية</h3>
          <p class="text-gray-600">جميع المشتركات لديهن اشتراكات نشطة</p>
        </div>
      </div>
    <?php endif; ?>
  </div>


  <a href="massagew.php" class="fixed bottom-6 left-6 z-50">
    <div class="relative">
      <i class="fab fa-whatsapp whatsapp-icon text-4xl" style="color: #ec4899;"></i>
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


    document.addEventListener('DOMContentLoaded', function () {
      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.classList.add('fade-in');
          }
        });
      }, { threshold: 0.1 });

      document.querySelectorAll('.member-card, .empty-state').forEach(card => {
        observer.observe(card);
      });
    });
  </script>

</body>

</html>