<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>جزئیات دانش‌آموز - پنل معاون</title>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v30.1.0/dist/font-face.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { font-family: 'Vazir', sans-serif; }
        
        /* استایل سایدبار */
        .sidebar { 
            background: #2c3e50; 
            color: white; 
            height: 100vh; 
            position: fixed; 
            width: 250px; 
            transition: all 0.3s;
            z-index: 1000;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                width: 280px;
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .main-content {
                margin-right: 0 !important;
                padding: 15px;
            }
        }

        .sidebar .nav-link { color: #bdc3c7; padding: 15px 20px; border-bottom: 1px solid #34495e; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: white; background: #34495e; }
        .main-content { margin-right: 250px; padding: 20px; transition: all 0.3s; }
        
        /* استایل‌های اصلی صفحه */
        .student-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 15px; padding: 25px; margin-bottom: 25px; }
        .info-card { border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .disciplinary-badge { font-size: 1.2rem; font-weight: bold; }
        .score-excellent { color: #28a745; }
        .score-good { color: #20c997; }
        .score-average { color: #ffc107; }
        .score-poor { color: #fd7e14; }
        .score-very-poor { color: #dc3545; }
        .violation-item { border-right: 4px solid #dc3545; margin-bottom: 10px; }
        .attendance-present { background-color: #d4edda !important; }
        .attendance-absent { background-color: #f8d7da !important; }
        .attendance-late { background-color: #fff3cd !important; }
        .attendance-excused { background-color: #e2e3e5 !important; }
        .grade-excellent { color: #28a745; font-weight: bold; }
        .grade-good { color: #20c997; font-weight: bold; }
        .grade-average { color: #ffc107; font-weight: bold; }
        .grade-poor { color: #fd7e14; font-weight: bold; }
        .grade-fail { color: #dc3545; font-weight: bold; }

        /* استایل‌های ریسپانسیو */
        @media (max-width: 768px) {
            .student-header {
                padding: 15px;
                margin-bottom: 15px;
            }
            .student-header .row > div {
                margin-bottom: 15px;
            }
            .student-header .btn {
                font-size: 0.9rem;
                padding: 8px 12px;
            }
            .nav-tabs .nav-link {
                font-size: 0.8rem;
                padding: 8px 12px;
            }
            .table-responsive {
                font-size: 0.75rem;
            }
            .card-body {
                padding: 15px;
            }
            .info-card {
                margin-bottom: 15px;
            }
        }

        /* دکمه منو برای موبایل */
        .mobile-menu-btn {
            display: none;
            position: fixed;
            top: 15px;
            right: 15px;
            z-index: 1100;
            background: #dc3545;
            border: none;
            border-radius: 5px;
            color: white;
            padding: 8px 12px;
            font-size: 1.2rem;
        }
        
        @media (max-width: 768px) {
            .mobile-menu-btn {
                display: block;
            }
        }

        /* استایل برای overlay در موبایل */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 999;
        }
        
        @media (max-width: 768px) {
            .sidebar-overlay.show {
                display: block;
            }
        }

        /* بهبود ظاهر تب‌ها */
        .nav-tabs .nav-link {
            border: none;
            color: #6c757d;
            font-weight: 500;
        }
        .nav-tabs .nav-link.active {
            color: #495057;
            background-color: #fff;
            border-bottom: 3px solid #dc3545;
        }
        .nav-tabs .nav-link:hover {
            border: none;
            color: #dc3545;
        }
    </style>
</head>
<body>
    <!-- دکمه منو برای موبایل -->
    <button class="mobile-menu-btn" id="mobileMenuBtn">
        <i class="bi bi-list"></i>
    </button>

    <!-- Overlay برای موبایل -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="container-fluid">
        <div class="row">
            <?php include 'app/views/assistant/partials/sidebar.php'; ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                
                <div class="student-header">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h2 class="mb-3"><?php echo $data['student']['first_name'] . ' ' . $data['student']['last_name']; ?></h2>
                            <p class="mb-2">
                                <i class="bi bi-person-badge"></i>
                                شماره دانش‌آموزی: <?php echo $data['student']['student_number']; ?>
                            </p>
                            <p class="mb-2">
                                <i class="bi bi-house-door"></i>
                                کلاس: <?php echo $data['student']['class_name']; ?> - رشته: <?php echo $data['student']['major_name']; ?>
                            </p>
                            <p class="mb-0">
                                <i class="bi bi-book"></i>
                                پایه: <?php echo $data['student']['grade_name']; ?>
                            </p>
                        </div>
                        <div class="col-md-3 text-center">
                            <?php
                            $score = $data['disciplinary_score']['current_score'] ?? 20;
                            $score_class = 'score-excellent';
                            if ($score < 20) $score_class = 'score-good';
                            if ($score < 18) $score_class = 'score-average';
                            if ($score < 16) $score_class = 'score-poor';
                            if ($score < 14) $score_class = 'score-very-poor';
                            ?>
                            <div class="disciplinary-badge <?php echo $score_class; ?>">
                                <h3><?php echo $score; ?></h3>
                                <small>نمره انضباطی</small>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <a href="<?php echo BASE_URL; ?>assistant/disciplinary?student_id=<?php echo $data['student']['id']; ?>" 
                               class="btn btn-danger btn-lg w-100 mb-2">
                                <i class="bi bi-plus-circle"></i> ثبت تخلف جدید
                            </a>
                            <a href="<?php echo BASE_URL; ?>assistant/students" class="btn btn-outline-light btn-sm w-100">
                                <i class="bi bi-arrow-right"></i> بازگشت به لیست
                            </a>
                        </div>
                    </div>
                </div>

                <ul class="nav nav-tabs" id="studentTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="disciplinary-tab" data-bs-toggle="tab" data-bs-target="#disciplinary" type="button" role="tab">
                            <i class="bi bi-shield-exclamation"></i> پرونده انضباطی
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="attendance-tab" data-bs-toggle="tab" data-bs-target="#attendance" type="button" role="tab">
                            <i class="bi bi-clipboard-check"></i> حضور و غیاب
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="grades-tab" data-bs-toggle="tab" data-bs-target="#grades" type="button" role="tab">
                            <i class="bi bi-journal-text"></i> نمرات درسی
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button" role="tab">
                            <i class="bi bi-info-circle"></i> اطلاعات فردی
                        </button>
                    </li>
                </ul>

                <div class="tab-content mt-4" id="studentTabsContent">
                    <!-- تب پرونده انضباطی -->
                    <div class="tab-pane fade show active" id="disciplinary" role="tabpanel">
                        <div class="row">
                            <div class="col-lg-8 col-md-7">
                                <div class="card info-card">
                                    <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0"><i class="bi bi-shield-exclamation"></i> سوابق تخلفات انضباطی</h5>
                                        <span class="badge bg-light text-dark"><?php echo count($data['disciplinary_records']); ?> مورد</span>
                                    </div>
                                    <div class="card-body">
                                        <?php if (!empty($data['disciplinary_records'])): ?>
                                            <?php foreach ($data['disciplinary_records'] as $record): ?>
                                                <div class="card violation-item mb-3">
                                                    <div class="card-body">
                                                        <div class="d-flex justify-content-between align-items-start">
                                                            <div class="flex-grow-1">
                                                                <h6 class="card-title text-danger">
                                                                    <i class="bi bi-exclamation-triangle"></i>
                                                                    <?php echo $record['violation_type']; ?>
                                                                </h6>
                                                                <p class="card-text mt-2"><?php echo $record['description']; ?></p>
                                                                <small class="text-muted">
                                                                    <i class="bi bi-calendar"></i>
                                                                    تاریخ: <?php echo $record['jalali_date']; ?> | 
                                                                    <i class="bi bi-person"></i>
                                                                    ثبت کننده: <?php echo $record['assistant_first_name'] . ' ' . $record['assistant_last_name']; ?>
                                                                </small>
                                                            </div>
                                                            <div class="text-end ms-3">
                                                                <span class="badge bg-danger fs-6">کسر <?php echo $record['point_deduction']; ?> نمره</span>
                                                                <br>
                                                                <small class="text-muted mt-2 d-block"><?php echo $record['created_at']; ?></small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <div class="alert alert-success text-center py-4">
                                                <i class="bi bi-check-circle display-4"></i>
                                                <h5 class="mt-3">این دانش‌آموز هیچ سابقه تخلف انضباطی ندارد.</h5>
                                                <p class="text-muted">نمره انضباطی کامل (۲۰ از ۲۰)</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-5">
                                <div class="card info-card">
                                    <div class="card-header bg-warning text-dark">
                                        <h5 class="mb-0"><i class="bi bi-graph-up"></i> آمار انضباطی</h5>
                                    </div>
                                    <div class="card-body text-center">
                                        <div class="mb-4">
                                            <h2 class="<?php echo $score_class; ?>"><?php echo $score; ?></h2>
                                            <small>نمره انضباطی فعلی</small>
                                        </div>
                                        <div class="mb-4">
                                            <h4 class="text-danger"><?php echo $data['disciplinary_score']['total_deductions'] ?? 0; ?></h4>
                                            <small>مجموع کسر نمرات</small>
                                        </div>
                                        <div class="mb-4">
                                            <h4><?php echo count($data['disciplinary_records']); ?></h4>
                                            <small>تعداد تخلفات ثبت شده</small>
                                        </div>
                                        <div class="mt-4">
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar <?php echo $score_class; ?>" 
                                                     role="progressbar" 
                                                     style="width: <?php echo ($score / 20) * 100; ?>%"
                                                     aria-valuenow="<?php echo $score; ?>" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="20">
                                                    <?php echo $score; ?> از ۲۰
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- تب حضور و غیاب -->
                    <div class="tab-pane fade" id="attendance" role="tabpanel">
                        <div class="card info-card">
                            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="bi bi-clipboard-check"></i> تاریخچه حضور و غیاب</h5>
                                <span class="badge bg-light text-dark"><?php echo count($data['student_attendance']); ?> رکورد</span>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($data['student_attendance'])): ?>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>تاریخ</th>
                                                    <th>درس</th>
                                                    <th>وضعیت</th>
                                                    <th>معلم</th>
                                                    <th>توضیحات</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($data['student_attendance'] as $attendance): ?>
                                                    <tr class="attendance-<?php echo $attendance['status']; ?>">
                                                        <td>
                                                            <small><?php echo $attendance['attendance_date']; ?></small>
                                                            <br>
                                                            <small class="text-muted"><?php echo $attendance['jalali_date']; ?></small>
                                                        </td>
                                                        <td>
                                                            <small class="fw-bold"><?php echo $attendance['course_name']; ?></small>
                                                            <br>
                                                            <small class="text-muted"><?php echo $attendance['course_code']; ?></small>
                                                        </td>
                                                        <td>
                                                            <?php
                                                            $status_text = '';
                                                            $status_badge = '';
                                                            switch($attendance['status']) {
                                                                case 'present':
                                                                    $status_text = 'حاضر';
                                                                    $status_badge = 'bg-success';
                                                                    break;
                                                                case 'absent':
                                                                    $status_text = 'غایب';
                                                                    $status_badge = 'bg-danger';
                                                                    break;
                                                                case 'late':
                                                                    $status_text = 'تأخیر';
                                                                    $status_badge = 'bg-warning';
                                                                    break;
                                                                case 'excused':
                                                                    $status_text = 'عذردار';
                                                                    $status_badge = 'bg-secondary';
                                                                    break;
                                                            }
                                                            ?>
                                                            <span class="badge <?php echo $status_badge; ?>"><?php echo $status_text; ?></span>
                                                        </td>
                                                        <td>
                                                            <small><?php echo $attendance['teacher_first_name'] . ' ' . $attendance['teacher_last_name']; ?></small>
                                                        </td>
                                                        <td>
                                                            <small class="text-muted"><?php echo $attendance['notes'] ?: '-'; ?></small>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-info text-center py-4">
                                        <i class="bi bi-info-circle display-4"></i>
                                        <h5 class="mt-3">هیچ رکورد حضور و غیابی برای این دانش‌آموز یافت نشد.</h5>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- تب نمرات درسی -->
                    <div class="tab-pane fade" id="grades" role="tabpanel">
                        <div class="card info-card">
                            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="bi bi-journal-text"></i> کارنامه آموزشی</h5>
                                <span class="badge bg-light text-dark"><?php echo count($data['student_grades']); ?> درس</span>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($data['student_grades'])): ?>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>درس</th>
                                                    <th>نوع</th>
                                                    <th>نمرات</th>
                                                    <th>میانگین</th>
                                                    <th>معلم</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($data['student_grades'] as $grade): ?>
                                                    <?php
                                                    $course_grade = $data['controller']->calculateCourseGrade($grade);
                                                    $grade_class = 'grade-excellent';
                                                    if ($course_grade < 17) $grade_class = 'grade-good';
                                                    if ($course_grade < 15) $grade_class = 'grade-average';
                                                    if ($course_grade < 12) $grade_class = 'grade-poor';
                                                    if ($course_grade < 10) $grade_class = 'grade-fail';
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <strong><?php echo $grade['course_name']; ?></strong>
                                                            <br>
                                                            <small class="text-muted"><?php echo $grade['course_code']; ?></small>
                                                        </td>
                                                        <td>
                                                            <?php if ($grade['course_type'] == 'poodmani'): ?>
                                                                <span class="badge bg-info">پودمانی</span>
                                                            <?php else: ?>
                                                                <span class="badge bg-warning text-dark">غیر پودمانی</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <?php if ($grade['course_type'] == 'poodmani'): ?>
                                                                <small>
                                                                    پ۱: <?php echo $grade['poodman1'] ?? '-'; ?> |
                                                                    پ۲: <?php echo $grade['poodman2'] ?? '-'; ?> |
                                                                    پ۳: <?php echo $grade['poodman3'] ?? '-'; ?>
                                                                </small>
                                                                <br>
                                                                <small>
                                                                    پ۴: <?php echo $grade['poodman4'] ?? '-'; ?> |
                                                                    پ۵: <?php echo $grade['poodman5'] ?? '-'; ?>
                                                                </small>
                                                            <?php else: ?>
                                                                <small>
                                                                    مست۱: <?php echo $grade['continuous1'] ?? '-'; ?> |
                                                                    ترم۱: <?php echo $grade['term1'] ?? '-'; ?>
                                                                </small>
                                                                <br>
                                                                <small>
                                                                    مست۲: <?php echo $grade['continuous2'] ?? '-'; ?> |
                                                                    ترم۲: <?php echo $grade['term2'] ?? '-'; ?>
                                                                </small>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <span class="<?php echo $grade_class; ?> fs-5">
                                                                <?php echo number_format($course_grade, 1); ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <small><?php echo $grade['teacher_first_name'] . ' ' . $grade['teacher_last_name']; ?></small>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <?php
                                    $total_avg = $data['controller']->calculateStudentAverage($data['student_grades']);
                                    $avg_class = 'grade-excellent';
                                    if ($total_avg < 17) $avg_class = 'grade-good';
                                    if ($total_avg < 15) $avg_class = 'grade-average';
                                    if ($total_avg < 12) $avg_class = 'grade-poor';
                                    if ($total_avg < 10) $avg_class = 'grade-fail';
                                    ?>
                                    <div class="alert alert-light text-center mt-4 py-3">
                                        <h3 class="<?php echo $avg_class; ?> mb-0">
                                            <i class="bi bi-award"></i>
                                            معدل کل: <?php echo number_format($total_avg, 2); ?>
                                        </h3>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-info text-center py-4">
                                        <i class="bi bi-info-circle display-4"></i>
                                        <h5 class="mt-3">هیچ نمره‌ای برای این دانش‌آموز ثبت نشده است.</h5>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- تب اطلاعات فردی -->
                    <div class="tab-pane fade" id="info" role="tabpanel">
                        <div class="row">
                            <div class="col-lg-6 col-md-6 mb-4">
                                <div class="card info-card h-100">
                                    <div class="card-header bg-info text-white">
                                        <h5 class="mb-0"><i class="bi bi-person"></i> اطلاعات شخصی</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <strong><i class="bi bi-person-circle"></i> نام کامل:</strong>
                                            <p class="mt-1"><?php echo $data['student']['first_name'] . ' ' . $data['student']['last_name']; ?></p>
                                        </div>
                                        <div class="mb-3">
                                            <strong><i class="bi bi-person-badge"></i> شماره دانش‌آموزی:</strong>
                                            <p class="mt-1"><?php echo $data['student']['student_number']; ?></p>
                                        </div>
                                        <div class="mb-3">
                                            <strong><i class="bi bi-credit-card"></i> کد ملی:</strong>
                                            <p class="mt-1"><?php echo $data['student']['national_code'] ?? 'ثبت نشده'; ?></p>
                                        </div>
                                        <div class="mb-3">
                                            <strong><i class="bi bi-phone"></i> شماره موبایل:</strong>
                                            <p class="mt-1"><?php echo $data['student']['mobile']; ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 mb-4">
                                <div class="card info-card h-100">
                                    <div class="card-header bg-secondary text-white">
                                        <h5 class="mb-0"><i class="bi bi-house"></i> اطلاعات تحصیلی</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <strong><i class="bi bi-house-door"></i> کلاس:</strong>
                                            <p class="mt-1"><?php echo $data['student']['class_name']; ?></p>
                                        </div>
                                        <div class="mb-3">
                                            <strong><i class="bi bi-briefcase"></i> رشته:</strong>
                                            <p class="mt-1"><?php echo $data['student']['major_name']; ?></p>
                                        </div>
                                        <div class="mb-3">
                                            <strong><i class="bi bi-book"></i> پایه:</strong>
                                            <p class="mt-1"><?php echo $data['student']['grade_name']; ?></p>
                                        </div>
                                        <div class="mb-3">
                                            <strong><i class="bi bi-person"></i> نام پدر:</strong>
                                            <p class="mt-1"><?php echo $data['student']['father_name'] ?? 'ثبت نشده'; ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // مدیریت منوی موبایل
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const sidebar = document.querySelector('.sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const mainContent = document.querySelector('.main-content');

        function toggleMobileMenu() {
            sidebar.classList.toggle('show');
            sidebarOverlay.classList.toggle('show');
            document.body.style.overflow = sidebar.classList.contains('show') ? 'hidden' : '';
        }

        mobileMenuBtn.addEventListener('click', toggleMobileMenu);
        sidebarOverlay.addEventListener('click', toggleMobileMenu);

        // بستن منو هنگام کلیک روی لینک‌ها در موبایل
        if (window.innerWidth <= 768) {
            const navLinks = document.querySelectorAll('.sidebar .nav-link');
            navLinks.forEach(link => {
                link.addEventListener('click', toggleMobileMenu);
            });
        }

        // فعال‌سازی تب‌ها
        const triggerTabList = [].slice.call(document.querySelectorAll('#studentTabs button'));
        triggerTabList.forEach(function (triggerEl) {
            var tabTrigger = new bootstrap.Tab(triggerEl);
            triggerEl.addEventListener('click', function (event) {
                event.preventDefault();
                tabTrigger.show();
            });
        });
    });
    </script>
</body>
</html>