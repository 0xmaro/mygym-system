<?php
session_start();

if (isset($_SESSION['admin_logged_in'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    

    if ($password === 'root') {
        $_SESSION['admin_logged_in'] = true;
        header("Location: index.php");
        exit();
    } else {
        $error = "كلمة المرور غير صحيحة";
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - My Gym</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Tajawal', sans-serif;
            background-color: #f8f9fc;
            height: 100vh;
            display: flex;
            align-items: center;
        }
        
        .login-card {
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        
        .login-card .card-header {
            background-color: #4e73df;
            color: white;
            text-align: center;
            font-weight: bold;
            border-radius: 0.5rem 0.5rem 0 0 !important;
        }
        
        .login-card .card-body {
            padding: 2rem;
        }
        
        .btn-primary {
            background-color: #4e73df;
            border-color: #4e73df;
        }
        
        .form-control {
            padding: 0.75rem 1rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card login-card animate__animated animate__fadeIn">
                    <div class="card-header">
                        <h4><i class="fas fa-dumbbell me-2"></i> My Gym</h4>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title text-center mb-4">تسجيل الدخول</h5>
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        <form method="POST">
                            <div class="mb-3">
                                <label for="password" class="form-label">كلمة المرور</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">دخول</button>
                        </form>
                        
                    </div>
                </div>
            </div>
        </div>
        
    </div>

    <a href="../stacadmin.php" class="floating-btn stats-btn" title="عرض الإحصائيات">
    <i class="fas fa-chart-bar"></i>
</a>


<a href="../logout.php" class="floating-btn logout-btn" title="تسجيل الخروج">
    <i class="fas fa-sign-out-alt"></i>
</a>

<style>
    .floating-btn {
        position: fixed;
        left: 20px;
        padding: 14px 16px;
        border-radius: 50%;
        text-align: center;
        font-size: 20px;
        color: white;
        z-index: 999;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        transition: all 0.3s ease;
        text-decoration: none;
    }

    .floating-btn:hover {
        transform: scale(1.1);
        text-decoration: none;
        color: white;
    }

    .logout-btn {
        bottom: 20px;
        background-color: #dc3545;
    }

    .logout-btn:hover {
        background-color: #bd2130;
    }

    .stats-btn {
        bottom: 80px;
        background-color: #007bff;
    }

    .stats-btn:hover {
        background-color: #0056b3;
    }
</style>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>