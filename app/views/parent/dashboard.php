<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>پنل اولیا - هنرستان امام صادق</title>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v30.1.0/dist/font-face.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { font-family: 'Vazir', sans-serif; }
        .sidebar { background: #2c3e50; color: white; height: 100vh; position: fixed; width: 250px; }
        .sidebar .nav-link { color: #bdc3c7; padding: 15px 20px; border-bottom: 1px solid #34495e; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: white; background: #34495e; }
        .main-content { margin-right: 250px; padding: 20px; }
        .welcome-card { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 15px; padding: 30px; margin-bottom: 30px; }
        .stat-card { background: white; border-radius: 10px; padding: 25px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); margin-bottom: 20px; text-align: center; }
        .quick-action { background: white; border-radius: 10px; padding: 20px; text-align: center; transition: transform 0.3s; }
        .quick-action:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        .grade-excellent { color: #28a745; }
        .grade-good { color: #20c997; }
        .grade-average { color: #ffc107; }
        .grade-poor { color: #dc3545; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- سایدبار -->
            <div class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="p-4 text-center text-white border-bottom">
                        <h5>هنرستان امام صادق</h5>
                        <small>پنل اولیا</small>
                        <div class="mt-3">
                            <img src="https://via.placeholder.com/80/667eea/ffffff?text=اولیا" 
                                 class="rounded-circle" alt="پروفایل" style="width: 80px; height: 80px;">
                        </div>
                        <div class="mt-2 small">
                            <i class="bi bi-person-circle"></i>
                            <?php echo $data['user_name']; ?>
                        </div>
                    </div>
                    
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link text-white active" href="<?php echo BASE_URL; ?>parent">
                                <i class="bi bi-speedometer2"></i> داشبورد
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="<?php echo BASE_URL; ?>parent/attendance">
                                <i class="bi bi-clipboard-check"></i> حضور و غیاب
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="<?php echo BASE_URL; ?>parent/grades">
                                <i class="bi bi-journal-text"></i> نمرات
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="<?php echo BASE_URL; ?>parent/disciplinary">
                                <i class="bi bi-shield-exclamation"></i> انضباط
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="<?php echo BASE_URL; ?>parent/profile">
                                <i class="bi bi-person"></i> پروفایل
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="<?php echo BASE_URL; ?>auth/logout">
                                <i class="bi bi-box-arrow-left"></i> خروج
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- محتوای اصلی -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <div class="welcome-card">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3>سلام، <?php echo $data['user_name']; ?>!</h3>
                            <p>به پنل اولیای هنرستان امام صادق خوش آمدید</p>
                            <div class="student-info bg-white bg-opacity-20 p-3 rounded">
                                <h5 class="mb-2">اطلاعات دانش‌آموز:</h5>
                                <div class="row">
                                    <div class="col-md-4">
                                        <strong>نام:</strong> <?php echo $data['student_info']['first_name'] . ' ' . $data['student_info']['last_name']; ?>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>شماره دانش‌آموزی:</strong> <?php echo $data['student_info']['student_number']; ?>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>کلاس:</strong> <?php echo $data['student_info']['class_name']; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <img src="https://via.placeholder.com/120/667eea/ffffff?text=<?php echo substr($data['student_info']['first_name'], 0, 1) . substr($data['student_info']['last_name'], 0, 1); ?>" 
                                 class="rounded-circle" alt="دانش‌آموز" style="width: 120px; height: 120px; border: 4px solid white;">
                        </div>
                    </div>
                </div>
                
                <!-- آمار کلی -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="stat-card">
                            <i class="bi bi-clipboard-check" style="font-size: 2rem; color: #28a745;"></i>
                            <h4><?php echo $data['attendance_stats']['attendance_rate']; ?>%</h4>
                            <p>نرخ حضور</p>
                            <small class="text-muted">
                                <?php echo $data['attendance_stats']['present']; ?> حاضر از 
                                <?php echo $data['attendance_stats']['total']; ?> روز
                            </small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <i class="bi bi-journal-text" style="font-size: 2rem; color: #667eea;"></i>
                            <h4 class="<?php 
                                $avg_class = 'grade-excellent';
                                if ($data['grade_stats']['average'] < 17) $avg_class = 'grade-good';
                                if ($data['grade_stats']['average'] < 15) $avg_class = 'grade-average';
                                if ($data['grade_stats']['average'] < 12) $avg_class = 'grade-poor';
                                echo $avg_class;
                            ?>">
                                <?php echo $data['grade_stats']['average']; ?>
                            </h4>
                            <p>معدل کل</p>
                            <small class="text-muted"><?php echo $data['grade_stats']['course_count']; ?> درس</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <i class="bi bi-shield-check" style="font-size: 2rem; color: #20c997;"></i>
                            <h4><?php echo $data['disciplinary_score']['current_score'] ?? 20; ?></h4>
                            <p>نمره انضباطی</p>
                            <small class="text-muted">
                                کسر: <?php echo $data['disciplinary_score']['total_deductions'] ?? 0; ?>
                            </small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <i class="bi bi-calendar-check" style="font-size: 2rem; color: #ffc107;"></i>
                            <h4>امروز</h4>
                            <p><?php echo JalaliDate::now(); ?></p>
                            <small class="text-muted">آخرین بروزرسانی</small>
                        </div>
                    </div>
                </div>

                <!-- اقدامات سریع -->
                <div class="row mt-4">
                    <div class="col-12">
                        <h4 class="mb-3">دسترسی سریع</h4>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="<?php echo BASE_URL; ?>parent/attendance" class="text-decoration-none">
                            <div class="quick-action">
                                <i class="bi bi-clipboard-check" style="font-size: 2rem; color: #28a745;"></i>
                                <h6 class="mt-2">حضور و غیاب</h6>
                                <small class="text-muted">مشاهده وضعیت حضور و غیاب</small>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="<?php echo BASE_URL; ?>parent/grades" class="text-decoration-none">
                            <div class="quick-action">
                                <i class="bi bi-journal-text" style="font-size: 2rem; color: #667eea;"></i>
                                <h6 class="mt-2">کارنامه</h6>
                                <small class="text-muted">مشاهده نمرات درسی</small>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="<?php echo BASE_URL; ?>parent/disciplinary" class="text-decoration-none">
                            <div class="quick-action">
                                <i class="bi bi-shield-exclamation" style="font-size: 2rem; color: #dc3545;"></i>
                                <h6 class="mt-2">وضعیت انضباطی</h6>
                                <small class="text-muted">مشاهده موارد انضباطی</small>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="<?php echo BASE_URL; ?>parent/profile" class="text-decoration-none">
                            <div class="quick-action">
                                <i class="bi bi-person" style="font-size: 2rem; color: #6f42c1;"></i>
                                <h6 class="mt-2">پروفایل</h6>
                                <small class="text-muted">اطلاعات حساب کاربری</small>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- هشدارها -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card border-warning">
                            <div class="card-header bg-warning text-dark">
                                <h6 class="mb-0"><i class="bi bi-exclamation-triangle"></i> توجه</h6>
                            </div>
                            <div class="card-body">
                                <?php if ($data['attendance_stats']['absent'] > 5): ?>
                                    <div class="alert alert-warning mb-2">
                                        <i class="bi bi-person-x"></i>
                                        دانش‌آموز در 30 روز گذشته <?php echo $data['attendance_stats']['absent']; ?> روز غیبت داشته است.
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (($data['grade_stats']['average'] ?? 0) < 12): ?>
                                    <div class="alert alert-danger mb-2">
                                        <i class="bi bi-journal-x"></i>
                                        معدل دانش‌آموز نیاز به توجه و پیگیری دارد.
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (($data['disciplinary_score']['current_score'] ?? 20) < 16): ?>
                                    <div class="alert alert-info mb-2">
                                        <i class="bi bi-shield-exclamation"></i>
                                        نمره انضباطی دانش‌آموز نیاز به بهبود دارد.
                                    </div>
                                <?php endif; ?>
                                
                                <div class="alert alert-success mb-0">
                                    <i class="bi bi-info-circle"></i>
                                    برای اطلاعات بیشتر با کادر آموزشی هنرستان در ارتباط باشید.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>