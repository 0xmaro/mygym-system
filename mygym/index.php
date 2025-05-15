<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

include 'config.php';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Gym - لوحة التحكم</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/animate.css@4.1.1/animate.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #1cc88a;
            --danger-color: #e74a3b;
            --warning-color: #f6c23e;
            --dark-color: #5a5c69;
        }
        
        body {
            font-family: 'Tajawal', sans-serif;
            background-color: #f8f9fc;
        }
        
        .sidebar {
            background: linear-gradient(180deg, var(--primary-color) 0%, #224abe 100%);
            min-height: 100vh;
            transition: all 0.3s;
        }
        
        .sidebar-brand {
            height: 4.375rem;
            text-decoration: none;
            font-size: 1.2rem;
            font-weight: 800;
            padding: 1.5rem 1rem;
            text-align: center;
            letter-spacing: 0.05rem;
            z-index: 1;
            color: white;
        }
        
        .nav-item .nav-link {
            position: relative;
            color: rgba(255, 255, 255, 0.8);
            padding: 1rem;
            font-weight: 700;
            font-size: 0.85rem;
            transition: all 0.3s;
        }
        
        .nav-item .nav-link:hover {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .nav-item .nav-link i {
            margin-left: 0.25rem;
            font-size: 0.85rem;
        }
        
        .card {
            border: none;
            border-radius: 0.35rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }
        
        .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
            padding: 1rem 1.35rem;
            font-weight: bold;
        }
        
        .bg-primary {
            background-color: var(--primary-color) !important;
        }
        
        .bg-success {
            background-color: var(--secondary-color) !important;
        }
        
        .bg-danger {
            background-color: var(--danger-color) !important;
        }
        
        .bg-warning {
            background-color: var(--warning-color) !important;
        }
        
        .stat-card {
            border-left: 0.25rem solid;
            transition: transform 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-card .card-body {
            padding: 1rem;
        }
        
        .stat-card .text-xs {
            font-size: 0.7rem;
        }
        
        .table-responsive {
            overflow-x: auto;
        }
        
        .table {
            width: 100%;
            margin-bottom: 1rem;
            color: #858796;
        }
        
        .table th {
            font-weight: bold;
            padding: 1rem;
            vertical-align: top;
            border-top: 1px solid #e3e6f0;
        }
        
        .table td {
            padding: 0.75rem;
            vertical-align: top;
            border-top: 1px solid #e3e6f0;
        }
        
        .badge {
            font-size: 0.75em;
            font-weight: 700;
            padding: 0.35em 0.65em;
        }
        
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            line-height: 1.5;
            border-radius: 0.2rem;
        }
        
        .floating-btn {
            position: fixed;
            bottom: 20px;
            left: 20px;
            z-index: 1000;
        }
        
        .animate__animated {
            animation-duration: 0.5s;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .fadeInUp {
            animation-name: fadeInUp;
        }
        
        .dropdown-menu {
            text-align: right;
            left: auto !important;
            right: 0 !important;
        }
        
        .form-control, .custom-select {
            text-align: right;
        }
        
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1050;
            overflow-y: auto;
        }

        .modal-dialog {
            margin: 1.75rem auto;
            position: relative;
            width: auto;
            max-width: 90%;
            z-index: 1051;
        }

        .modal-content {
            position: relative;
            display: flex;
            flex-direction: column;
            width: 100%;
            pointer-events: auto;
            background-color: #fff;
            border: 1px solid rgba(0, 0, 0, 0.2);
            border-radius: 0.3rem;
            outline: 0;
        }

        .modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1040;
            width: 100vw;
            height: 100vh;
            background-color: #000;
            opacity: 0.5;
        }

        body.modal-open {
            overflow: hidden;
            padding-right: 0 !important;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">

        <div class="col-md-3 col-lg-2 d-md-block sidebar collapse bg-primary">
                <div class="position-sticky pt-3">
                    <a class="sidebar-brand d-flex align-items-center justify-content-center mb-4" href="#">
                        <i class="fas fa-dumbbell me-2"></i>
                        <span>My Gym</span>
                    </a>
                    
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="#">
                                <i class="fas fa-fw fa-tachometer-alt"></i>
                                <span>لوحة التحكم</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../stacadmin.php">
                                <i class="fas fa-fw fa-chart-bar"></i>
                                <span>لوحة الإحصائيات</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" id="showMaleMembers">
                                <i class="fas fa-fw fa-male"></i>
                                <span>الأعضاء الرجال</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" id="showFemaleMembers">
                                <i class="fas fa-fw fa-female"></i>
                                <span>الأعضاء السيدات</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" id="showAllSessions">
                                <i class="fas fa-fw fa-calendar-check"></i>
                                <span>سجل الحصص</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">لوحة تحكم الجيم</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="refreshBtn">
                            <i class="fas fa-sync-alt"></i> تحديث
                        </button>
                    </div>
                </div>
                

                <div class="row mb-4 animate__animated animate__fadeInUp">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col me-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            إجمالي الأعضاء</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?php
                                            $total_members = 0;
                                            $male_count = $conn->query("SELECT COUNT(*) as count FROM members")->fetch_assoc()['count'];
                                            $female_count = $conn->query("SELECT COUNT(*) as count FROM womembers")->fetch_assoc()['count'];
                                            $total_members = $male_count + $female_count;
                                            echo $total_members;
                                            ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-users fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col me-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                            الأعضاء الرجال</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?php echo $male_count; ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-male fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card border-left-info shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col me-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                            الأعضاء السيدات</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?php echo $female_count; ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-female fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col me-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            الحصص هذا الشهر</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?php
                                            $current_month = date('m');
                                            $sessions_count = $conn->query("SELECT COUNT(*) as count FROM sessions WHERE MONTH(created_at) = $current_month")->fetch_assoc()['count'];
                                            echo $sessions_count;
                                            ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                

                <div class="card shadow mb-4 animate__animated animate__fadeInUp">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">قائمة الأعضاء</h6>
                        <div class="dropdown no-arrow">
                            <button type="button" class="btn btn-sm btn-primary" id="addMemberBtn">
                                <i class="fas fa-plus"></i> إضافة عضو جديد
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="searchInput" placeholder="بحث بالاسم...">
                                    <button type="button" class="btn btn-outline-primary" id="searchBtn">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" id="genderFilter">
                                    <option value="all">الكل</option>
                                    <option value="male">رجال</option>
                                    <option value="female">سيدات</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" id="subscriptionFilter">
                                    <option value="all">كل الاشتراكات</option>
                                    <option value="active">نشطة فقط</option>
                                    <option value="expired">منتهية</option>
                                    <option value="expiring">تنتهي قريباً</option>
                                </select>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="membersTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>الاسم</th>
                                        <th>رقم الهاتف</th>
                                        <th>العمر</th>
                                        <th>نوع الاشتراك</th>
                                        <th>مدة الاشتراك</th>
                                        <th>تاريخ البداية</th>
                                        <th>تاريخ النهاية</th>
                                        <th>الحالة</th>
                                        <th>الكابتن</th>
                                        <th>إجراءات</th>
                                    </tr>
                                </thead>
                                <tbody id="membersTableBody">

                            </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <div style="position: fixed; left: 20px; bottom: 20px; display: flex; flex-direction: column; gap: 15px; z-index: 1000;">
        <a href="logout.php" class="btn btn-danger animate__animated animate__fadeInLeft">
            <i class="fas fa-sign-out-alt"></i> 
        </a>
    </div>


    <div class="modal fade" id="addMemberModal" tabindex="-1" aria-labelledby="addMemberModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="addMemberModalLabel">إضافة عضو جديد</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addMemberForm">
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="first_name" class="form-label">الاسم الأول</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" required>
                            </div>
                            <div class="col-md-4">
                                <label for="middle_name" class="form-label">الاسم الأوسط</label>
                                <input type="text" class="form-control" id="middle_name" name="middle_name">
                            </div>
                            <div class="col-md-4">
                                <label for="last_name" class="form-label">الاسم الأخير</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="phone" class="form-label">رقم الهاتف</label>
                                <input type="text" class="form-control" id="phone" name="phone" required>
                            </div>
                            <div class="col-md-4">
                                <label for="age" class="form-label">العمر</label>
                                <input type="number" class="form-control" id="age" name="age" required>
                            </div>
                            <div class="col-md-4">
                                <label for="gender" class="form-label">النوع</label>
                                <select class="form-select" id="gender" name="gender" required>
                                    <option value="male">ذكر</option>
                                    <option value="female">أنثى</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="subscription_type" class="form-label">نوع الاشتراك</label>
                                <select class="form-select" id="subscription_type" name="subscription_type" required>
                                    <option value="حديد">حديد</option>
                                    <option value="اجهزه">أجهزة</option>
                                    <option value="private">خاص</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="subscription_duration" class="form-label">مدة الاشتراك</label>
                                <select class="form-select" id="subscription_duration" name="subscription_duration" required>
                                    <option value="شهر">شهر</option>
                                    <option value="3 شهور">3 شهور</option>
                                    <option value="6 شهور">6 شهور</option>
                                    <option value="سنة">سنة</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="coach_name" class="form-label">اسم الكابتن</label>
                                <input type="text" class="form-control" id="coach_name" name="coach_name" required>
                            </div>
                            <div class="col-md-6">
                                <label for="start_date" class="form-label">تاريخ البداية</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="notes" class="form-label">ملاحظات</label>
                            <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-primary">حفظ العضو</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    

    <div class="modal fade" id="editMemberModal" tabindex="-1" aria-labelledby="editMemberModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="editMemberModalLabel">تعديل بيانات العضو</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editMemberForm">
                    <input type="hidden" id="edit_id" name="id">
                    <input type="hidden" id="edit_gender" name="gender">
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="edit_first_name" class="form-label">الاسم الأول</label>
                                <input type="text" class="form-control" id="edit_first_name" name="first_name" required>
                            </div>
                            <div class="col-md-4">
                                <label for="edit_middle_name" class="form-label">الاسم الأوسط</label>
                                <input type="text" class="form-control" id="edit_middle_name" name="middle_name">
                            </div>
                            <div class="col-md-4">
                                <label for="edit_last_name" class="form-label">الاسم الأخير</label>
                                <input type="text" class="form-control" id="edit_last_name" name="last_name" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="edit_phone" class="form-label">رقم الهاتف</label>
                                <input type="text" class="form-control" id="edit_phone" name="phone" required>
                            </div>
                            <div class="col-md-4">
                                <label for="edit_age" class="form-label">العمر</label>
                                <input type="number" class="form-control" id="edit_age" name="age" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">النوع</label>
                                <input type="text" class="form-control" id="edit_gender_display" disabled>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="edit_subscription_type" class="form-label">نوع الاشتراك</label>
                                <select class="form-select" id="edit_subscription_type" name="subscription_type" required>
                                    <option value="حديد">حديد</option>
                                    <option value="اجهزه">أجهزة</option>
                                    <option value="private">خاص</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_subscription_duration" class="form-label">مدة الاشتراك</label>
                                <select class="form-select" id="edit_subscription_duration" name="subscription_duration" required>
                                    <option value="شهر">شهر</option>
                                    <option value="3 شهور">3 شهور</option>
                                    <option value="6 شهور">6 شهور</option>
                                    <option value="سنة">سنة</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="edit_coach_name" class="form-label">اسم الكابتن</label>
                                <input type="text" class="form-control" id="edit_coach_name" name="coach_name" required>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_start_date" class="form-label">تاريخ البداية</label>
                                <input type="date" class="form-control" id="edit_start_date" name="start_date" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="edit_end_date" class="form-label">تاريخ النهاية</label>
                                <input type="date" class="form-control" id="edit_end_date" name="end_date" required>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_renewed_by" class="form-label">تم التجديد بواسطة</label>
                                <input type="text" class="form-control" id="edit_renewed_by" name="renewed_by">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_notes" class="form-label">ملاحظات</label>
                            <textarea class="form-control" id="edit_notes" name="notes" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    

    <div class="modal fade" id="renewModal" tabindex="-1" aria-labelledby="renewModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="renewModalLabel">تجديد اشتراك العضو</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="renewForm">
                    <input type="hidden" id="renew_id" name="id">
                    <input type="hidden" id="renew_gender" name="gender">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="renew_subscription_duration" class="form-label">مدة التجديد</label>
                            <select class="form-select" id="renew_subscription_duration" name="subscription_duration" required>
                                <option value="شهر">شهر</option>
                                <option value="3 شهور">3 شهور</option>
                                <option value="6 شهور">6 شهور</option>
                                <option value="سنة">سنة</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="renew_renewed_by" class="form-label">تم التجديد بواسطة</label>
                            <input type="text" class="form-control" id="renew_renewed_by" name="renewed_by" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-success">تجديد الاشتراك</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    

    <div class="modal fade" id="sessionsModal" tabindex="-1" aria-labelledby="sessionsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="sessionsModalLabel">سجل الحصص للعضو</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="sessionsTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>الاسم</th>
                                    <th>رقم الهاتف</th>
                                    <th>العمر</th>
                                    <th>الكابتن</th>
                                    <th>تاريخ الحصة</th>
                                </tr>
                            </thead>
                            <tbody id="sessionsTableBody">

                        </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                </div>
            </div>
        </div>
    </div>
    

    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteModalLabel">تأكيد الحذف</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>هل أنت متأكد أنك تريد حذف هذا العضو؟ هذا الإجراء لا يمكن التراجع عنه.</p>
                    <input type="hidden" id="delete_id">
                    <input type="hidden" id="delete_gender">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">حذف</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>

$(document).ready(function() {

    $(document).on('shown.bs.modal', '.modal', function() {
                const $modal = $(this);
                $modal.attr('aria-hidden', 'false');
                

                setTimeout(() => {
                    $modal.find('input:visible:first, select:visible:first, button:visible:first').focus();
                }, 100);
            });

            $(document).on('hidden.bs.modal', '.modal', function() {
                $(this).attr('aria-hidden', 'true');
            });


            $('.modal').modal({
                backdrop: 'static',
                keyboard: false
            });


            $(document).on('keydown', function(e) {

                if (e.key === 'Escape' && $('.modal.show').length) {
                    $('.modal.show').modal('hide');
                }
                

                if (e.key === 'Tab' && $('.modal.show').length) {
                    const $modal = $('.modal.show');
                    const $focusable = $modal.find('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
                    
                    if (!$focusable.length) return;
                    
                    const $first = $focusable.first();
                    const $last = $focusable.last();
                    
                    if (e.shiftKey && $(document.activeElement).is($first)) {
                        $last.focus();
                        e.preventDefault();
                    } else if (!e.shiftKey && $(document.activeElement).is($last)) {
                        $first.focus();
                        e.preventDefault();
                    }
                }
            });


            loadMembers();
            

            const today = new Date().toISOString().split('T')[0];
            $('#start_date').val(today);
            
            // Add Member Form Submission
            $('#addMemberForm').submit(function(e) {
                e.preventDefault();
                
                const formData = $(this).serialize();
                
                $.ajax({
                    url: 'add_member.php',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        const result = JSON.parse(response);
                        if (result.status === 'success') {
                            $('#addMemberModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'تمت الإضافة',
                                text: 'تمت إضافة العضو بنجاح',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            loadMembers();
                            $('#addMemberForm')[0].reset();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'خطأ',
                                text: result.message
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ',
                            text: 'حدث خطأ أثناء إضافة العضو'
                        });
                    }
                });
            });
            
            $('#editMemberForm').submit(function(e) {
                e.preventDefault();
                
                const formData = $(this).serialize();
                
                $.ajax({
                    url: 'edit_member.php',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        const result = JSON.parse(response);
                        if (result.status === 'success') {
                            $('#editMemberModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'تم التعديل',
                                text: 'تم تعديل بيانات العضو بنجاح',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            loadMembers();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'خطأ',
                                text: result.message
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ',
                            text: 'حدث خطأ أثناء تعديل بيانات العضو'
                        });
                    }
                });
            });
            

            $('#renewForm').submit(function(e) {
                e.preventDefault();
                
                const formData = $(this).serialize();
                
                $.ajax({
                    url: 'renew_member.php',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        const result = JSON.parse(response);
                        if (result.status === 'success') {
                            $('#renewModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'تم التجديد',
                                text: 'تم تجديد اشتراك العضو بنجاح',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            loadMembers();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'خطأ',
                                text: result.message
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ',
                            text: 'حدث خطأ أثناء تجديد اشتراك العضو'
                        });
                    }
                });
            });
            
            $('#confirmDelete').click(function() {
                const id = $('#delete_id').val();
                const gender = $('#delete_gender').val();
                
                $.ajax({
                    url: 'delete_member.php',
                    type: 'POST',
                    data: { id: id, gender: gender },
                    success: function(response) {
                        const result = JSON.parse(response);
                        if (result.status === 'success') {
                            $('#deleteModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'تم الحذف',
                                text: 'تم حذف العضو بنجاح',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            loadMembers();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'خطأ',
                                text: result.message
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ',
                            text: 'حدث خطأ أثناء حذف العضو'
                        });
                    }
                });
            });
            

            $('#searchBtn').click(function() {
                loadMembers();
            });
            
            $('#searchInput').keypress(function(e) {
                if (e.which === 13) {
                    loadMembers();
                }
            });
            

            $('#genderFilter, #subscriptionFilter').change(function() {
                loadMembers();
            });
            

            $('#showMaleMembers').click(function() {
                $('#genderFilter').val('male').trigger('change');
            });
            

            $('#showFemaleMembers').click(function() {
                $('#genderFilter').val('female').trigger('change');
            });
            

            $('#showAllSessions').click(function() {
                window.location.href = 'sessions.php';
            });
            

            $('#refreshBtn').click(function() {
                loadMembers();
                Swal.fire({
                    icon: 'success',
                    title: 'تم التحديث',
                    timer: 1000,
                    showConfirmButton: false
                });
            });
            

            $('#addMemberBtn').click(function() {
                $('#addMemberModal').modal('show');
            });
            

            function loadMembers() {
                const searchTerm = $('#searchInput').val();
                const gender = $('#genderFilter').val();
                const subscriptionStatus = $('#subscriptionFilter').val();
                
                $.ajax({
                    url: 'get_members.php',
                    type: 'GET',
                    data: {
                        search: searchTerm,
                        gender: gender,
                        status: subscriptionStatus
                    },
                    success: function(response) {
                        $('#membersTableBody').html(response);
                    },
                    error: function() {
                        $('#membersTableBody').html('<tr><td colspan="11" class="text-center">حدث خطأ أثناء جلب البيانات</td></tr>');
                    }
                });
            }
            

            window.editMember = function(id, gender) {
                $.ajax({
                    url: 'get_member.php',
                    type: 'GET',
                    data: { 
                        id: id, 
                        gender: gender 
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'خطأ',
                                text: response.error
                            });
                            return;
                        }

                        $('#edit_id').val(response.id);
                        $('#edit_gender').val(gender);
                        $('#edit_first_name').val(response.first_name || '');
                        $('#edit_middle_name').val(response.middle_name || '');
                        $('#edit_last_name').val(response.last_name || '');
                        $('#edit_phone').val(response.phone || '');
                        $('#edit_age').val(response.age || '');
                        $('#edit_gender_display').val(gender === 'male' ? 'ذكر' : 'أنثى');
                        $('#edit_subscription_type').val(response.subscription_type || 'حديد');
                        $('#edit_subscription_duration').val(response.subscription_duration || 'شهر');
                        $('#edit_coach_name').val(response.coach_name || '');
                        $('#edit_start_date').val(response.start_date || '');
                        $('#edit_end_date').val(response.end_date || '');
                        $('#edit_renewed_by').val(response.renewed_by || '');
                        $('#edit_notes').val(response.notes || '');

                        $('#editMemberModal').modal('show');
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ',
                            text: 'حدث خطأ أثناء جلب بيانات العضو: ' + xhr.statusText
                        });
                    }
                });
            }
            
            window.renewMember = function(id, gender) {
                $('#renew_id').val(id);
                $('#renew_gender').val(gender);
                $('#renew_renewed_by').val('');
                $('#renewModal').modal('show');
            }
            
            window.deleteMember = function(id, gender) {
                $('#delete_id').val(id);
                $('#delete_gender').val(gender);
                $('#deleteModal').modal('show');
            }
            
            window.viewSessions = function(member_fullname, phone) {
                $.ajax({
                    url: 'get_sessions.php',
                    type: 'GET',
                    data: {
                        first_name: firstName,
                        phone: phone
                    },
                    success: function(response) {
                        $('#sessionsTableBody').html(response);
                        $('#sessionsModal').modal('show');
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ',
                            text: 'حدث خطأ أثناء جلب سجل الحصص'
                        });
                    }
                });
            }
        });
    </script>
</body>
</html>