<?php
session_start();

function redirect_user($role)
{
    switch ($role) {
        case 'admin':
            header("Location: mygym/login.php");
            break;
        case 'coach':
            header("Location: coach.php");
            break;
        case 'wcoach':
            header("Location: wcoach.php");
            break;
        default:
            echo "نوع المستخدم غير معروف.";
            exit();
    }
    exit();
}

if (isset($_SESSION['role'])) {
    redirect_user($_SESSION['role']);
}

$message = "";
$max_attempts = 15;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = 0;
    }

    if ($_SESSION['login_attempts'] >= $max_attempts) {
        $message = "تم تجاوز عدد المحاولات المسموح بها. الرجاء المحاولة لاحقاً.";
    } else {
        $conn = new mysqli("localhost", "root", "2565919amar1", "mygym");

        if ($conn->connect_error) {
            die("فشل الاتصال: " . $conn->connect_error);
        }

        $username = $_POST['username'];
        $password = $_POST['password'];

        $stmt = $conn->prepare("SELECT username, password, role FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if ($password === $user['password']) {
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['login_attempts'] = 0;
                redirect_user($user['role']);
            } else {
                $_SESSION['login_attempts']++;
                $message = "كلمة المرور غير صحيحة.";
            }
        } else {
            $_SESSION['login_attempts']++;
            $message = "اسم المستخدم غير صحيح.";
        }

        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8" />
    <title>تسجيل الدخول - MY GYM</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#fef2f2',
                            100: '#fee2e2',
                            200: '#fecaca',
                            300: '#fca5a5',
                            400: '#f87171',
                            500: '#ef4444',
                            600: '#dc2626',
                            700: '#b91c1c',
                            800: '#991b1b',
                            900: '#7f1d1d',
                        },
                        dark: {
                            800: '#1e293b',
                            900: '#0f172a',
                        }
                    },
                    fontFamily: {
                        'tajawal': ['Tajawal', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Tajawal', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
            overflow-x: hidden;
        }

        .login-container {
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            transition: all 0.3s ease;
        }

        .login-container:hover {
            box-shadow: 0 8px 32px 0 rgba(239, 68, 68, 0.2);
        }

        .input-field {
            transition: all 0.3s ease;
            background: rgba(30, 41, 59, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .input-field:focus {
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.2);
        }

        .login-btn {
            background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(239, 68, 68, 0.3);
        }

        .login-btn:active {
            transform: translateY(0);
        }

        .gym-icon {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }

            100% {
                transform: scale(1);
            }
        }

        .toast {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 0.5rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            opacity: 0;
            transition: all 0.4s ease;
            z-index: 9999;
            transform: translateX(100%);
        }

        .toast.show {
            opacity: 1;
            transform: translateX(0);
        }

        .dumbbell-animation {
            position: absolute;
            width: 100px;
            height: 100px;
            opacity: 0.1;
            z-index: -1;
        }

        .dumbbell-1 {
            top: 10%;
            left: 10%;
            animation: float 6s ease-in-out infinite;
        }

        .dumbbell-2 {
            bottom: 10%;
            right: 10%;
            animation: float 8s ease-in-out infinite 2s;
        }

        @keyframes float {
            0% {
                transform: translateY(0) rotate(0deg);
            }

            50% {
                transform: translateY(-20px) rotate(5deg);
            }

            100% {
                transform: translateY(0) rotate(0deg);
            }
        }

        .footer {
            transition: all 0.3s ease;
        }

        .footer:hover {
            transform: scale(1.05);
            cursor: pointer;
        }
    </style>
</head>

<body class="flex items-center justify-center min-h-screen p-4">

<i class="dumbbell-animation dumbbell-1 fas fa-dumbbell text-red-500 text-5xl"></i>
    <i class="dumbbell-animation dumbbell-2 fas fa-dumbbell text-red-500 text-5xl"></i>

    <div class="login-container w-full max-w-md p-8 rounded-xl animate__animated animate__fadeIn">
        <div class="flex flex-col items-center mb-8">
            <div class="gym-icon mb-4">
                <i class="fas fa-dumbbell text-red-500 text-6xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-center text-white mb-2">
                <span class="text-red-500">MY GYM</span> نظام إدارة الجيم
            </h1>
            <p class="text-gray-400 text-sm">من فضلك قم بتسجيل الدخول للوصول إلى حسابك</p>
        </div>

        <form method="POST" action="" class="space-y-6">
            <div>
                <label for="username" class="block text-sm font-medium text-gray-300 mb-2">اسم المستخدم</label>
                <div class="relative">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <i class="fas fa-user text-gray-500"></i>
                    </div>
                    <input type="text" id="username" name="username" required
                        class="input-field w-full pr-10 pl-4 py-3 rounded-lg text-white placeholder-gray-400 focus:outline-none"
                        placeholder="أدخل اسم المستخدم">
                </div>
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-300 mb-2">كلمة المرور</label>
                <div class="relative">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <i class="fas fa-lock text-gray-500"></i>
                    </div>
                    <input type="password" id="password" name="password" required
                        class="input-field w-full pr-10 pl-4 py-3 rounded-lg text-white placeholder-gray-400 focus:outline-none"
                        placeholder="أدخل كلمة المرور">
                </div>
            </div>

            <button type="submit"
                class="login-btn w-full py-3 px-4 rounded-lg font-bold text-white flex items-center justify-center">
                <i class="fas fa-sign-in-alt ml-2"></i>
                تسجيل الدخول
            </button>
        </form>




    <?php if (!empty($message)): ?>
        <div id="toast" class="toast">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <?php echo $message; ?>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const toast = document.getElementById('toast');
                toast.classList.add('show');

                setTimeout(() => {
                    toast.classList.remove('show');
                    setTimeout(() => {
                        toast.remove();
                    }, 400);
                }, 4000);
            });
        </script>
    <?php endif; ?>

    <script>
        document.querySelectorAll('.input-field').forEach(input => {
            input.addEventListener('focus', function () {
                this.parentElement.querySelector('i').classList.add('text-red-500');
                this.parentElement.querySelector('i').classList.remove('text-gray-500');
            });

            input.addEventListener('blur', function () {
                this.parentElement.querySelector('i').classList.remove('text-red-500');
                this.parentElement.querySelector('i').classList.add('text-gray-500');
            });
        });
    </script>
</body>

</html>