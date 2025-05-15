<?php
session_start();


include 'config.php';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Gym - سجل الحصص</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>

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
                            <a class="nav-link" href="index.php">
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
                            <a class="nav-link" href="#" onclick="window.location.href='index.php?gender=male'">
                                <i class="fas fa-fw fa-male"></i>
                                <span>الأعضاء الرجال</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="window.location.href='index.php?gender=female'">
                                <i class="fas fa-fw fa-female"></i>
                                <span>الأعضاء السيدات</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="#">
                                <i class="fas fa-fw fa-calendar-check"></i>
                                <span>سجل الحصص</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">سجل الحصص</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <button class="btn btn-sm btn-outline-secondary" id="refreshBtn">
                            <i class="fas fa-sync-alt"></i> تحديث
                        </button>
                    </div>
                </div>
                

                <div class="card shadow mb-4 animate__animated animate__fadeInUp">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">قائمة الحصص</h6>
                        <div class="dropdown no-arrow">
                            <button class="btn btn-sm btn-primary" id="addSessionBtn">
                                <i class="fas fa-plus"></i> إضافة حصة جديدة
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="searchInput" placeholder="بحث بالاسم...">
                                    <button class="btn btn-outline-primary" type="button" id="searchBtn">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <input type="date" class="form-control" id="dateFilter">
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" id="coachFilter">
                                    <option value="all">كل الكباتن</option>
                                    <?php
                                    $coaches = $conn->query("SELECT DISTINCT coach_name FROM sessions UNION SELECT DISTINCT coach_name FROM members UNION SELECT DISTINCT coach_name FROM womembers ORDER BY coach_name");
                                    while ($coach = $coaches->fetch_assoc()) {
                                        echo '<option value="' . $coach['coach_name'] . '">' . $coach['coach_name'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
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
                                        <th>إجراءات</th>
                                    </tr>
                                </thead>
                                <tbody id="sessionsTableBody">
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

    

    <div class="modal fade" id="addSessionModal" tabindex="-1" aria-labelledby="addSessionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="addSessionModalLabel">إضافة حصة جديدة</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addSessionForm">
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="session_first_name" class="form-label">الاسم</label>
                                <input type="text" class="form-control" id="session_first_name" name="member_first" required>
                            </div>

                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="session_phone" class="form-label">رقم الهاتف</label>
                                <input type="text" class="form-control" id="session_phone" name="phone" required>
                            </div>
                            <div class="col-md-6">
                                <label for="session_age" class="form-label">العمر</label>
                                <input type="number" class="form-control" id="session_age" name="age" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="session_coach_name" class="form-label">اسم الكابتن</label>
                            <input type="text" class="form-control" id="session_coach_name" name="coach_name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-primary">حفظ الحصة</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="deleteSessionModal" tabindex="-1" aria-labelledby="deleteSessionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteSessionModalLabel">تأكيد حذف الحصة</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>هل أنت متأكد أنك تريد حذف هذه الحصة؟ هذا الإجراء لا يمكن التراجع عنه.</p>
                    <input type="hidden" id="delete_session_id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteSession">حذف</button>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            loadSessions();
            

            $('#addSessionForm').submit(function(e) {
                e.preventDefault();
                
                const formData = $(this).serialize();
                
                $.ajax({
                    url: 'add_session.php',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        const result = JSON.parse(response);
                        if (result.status === 'success') {
                            $('#addSessionModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'تمت الإضافة',
                                text: 'تمت إضافة الحصة بنجاح',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            loadSessions();
                            $('#addSessionForm')[0].reset();
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
                            text: 'حدث خطأ أثناء إضافة الحصة'
                        });
                    }
                });
            });
            

            $('#confirmDeleteSession').click(function() {
                const id = $('#delete_session_id').val();
                
                $.ajax({
                    url: 'delete_session.php',
                    type: 'POST',
                    data: { id: id },
                    success: function(response) {
                        const result = JSON.parse(response);
                        if (result.status === 'success') {
                            $('#deleteSessionModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'تم الحذف',
                                text: 'تم حذف الحصة بنجاح',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            loadSessions();
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
                            text: 'حدث خطأ أثناء حذف الحصة'
                        });
                    }
                });
            });
            

            $('#searchBtn').click(function() {
                loadSessions();
            });
            
            $('#searchInput').keypress(function(e) {
                if (e.which === 13) {
                    loadSessions();
                }
            });
            

            $('#dateFilter, #coachFilter').change(function() {
                loadSessions();
            });
            

            $('#refreshBtn').click(function() {
                loadSessions();
                Swal.fire({
                    icon: 'success',
                    title: 'تم التحديث',
                    timer: 1000,
                    showConfirmButton: false
                });
            });
            

            $('#addSessionBtn').click(function() {
                $('#addSessionModal').modal('show');
            });
            

            function loadSessions() {
                const searchTerm = $('#searchInput').val();
                const dateFilter = $('#dateFilter').val();
                const coachFilter = $('#coachFilter').val();
                
                $.ajax({
                    url: 'get_all_sessions.php',
                    type: 'GET',
                    data: {
                        search: searchTerm,
                        date: dateFilter,
                        coach: coachFilter
                    },
                    success: function(response) {
                        $('#sessionsTableBody').html(response);
                    },
                    error: function() {
                        $('#sessionsTableBody').html('<tr><td colspan="7" class="text-center">حدث خطأ أثناء جلب البيانات</td></tr>');
                    }
                });
            }
            
            window.deleteSession = function(id) {
                $('#delete_session_id').val(id);
                $('#deleteSessionModal').modal('show');
            }
        });
    </script>
</body>
</html>