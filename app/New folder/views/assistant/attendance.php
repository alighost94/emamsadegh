<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>حضور و غیاب - پنل معاون</title>
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
        
        /* استایل‌های اصلی */
        .attendance-badge { font-size: 0.75rem; }
        .present { background-color: #d4edda !important; color: #155724 !important; }
        .absent { background-color: #f8d7da !important; color: #721c24 !important; }
        .late { background-color: #fff3cd !important; color: #856404 !important; }
        .excused { background-color: #e2e3e5 !important; color: #383d41 !important; }
        .date-section { border: 2px solid #e9ecef; border-radius: 10px; padding: 15px; margin-bottom: 20px; }
        .date-header { background: #f8f9fa; padding: 10px; border-radius: 5px; margin-bottom: 10px; }
        .stats-card { 
            text-align: center; 
            padding: 15px; 
            border-radius: 8px; 
            color: white;
            transition: transform 0.3s;
        }
        .stats-card:hover {
            transform: translateY(-3px);
        }
        .tab-content { 
            border: 1px solid #dee2e6; 
            border-top: none; 
            padding: 20px; 
            border-radius: 0 0 5px 5px;
            background: #fff;
        }

        /* استایل‌های ریسپانسیو */
        @media (max-width: 768px) {
            .stats-card {
                margin-bottom: 10px;
                padding: 12px;
            }
            .date-section {
                padding: 10px;
                margin-bottom: 15px;
            }
            .tab-content {
                padding: 15px;
            }
            .table-responsive {
                font-size: 0.8rem;
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
            padding: 12px 20px;
        }
        .nav-tabs .nav-link.active {
            color: #495057;
            background-color: #fff;
            border-bottom: 3px solid #007bff;
        }
        .nav-tabs .nav-link:hover {
            border: none;
            color: #007bff;
        }

        /* استایل برای کارت‌ها */
        .class-section {
            border-right: 3px solid #007bff;
            padding-right: 15px;
            margin-bottom: 20px;
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
            <!-- سایدبار -->
            <?php include 'app/views/assistant/partials/sidebar.php'; ?>
            
            <!-- محتوای اصلی -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">
                        <i class="bi bi-clipboard-check"></i>
                        گزارش کامل حضور و غیاب پایه <?php echo $data['assistant']['grade_name']; ?>
                    </h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="<?php echo BASE_URL; ?>assistant/students" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-people"></i> مدیریت دانش‌آموزان
                        </a>
                    </div>
                </div>

                <!-- فیلترها -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="bi bi-funnel"></i>
                            فیلترها و جستجو
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-lg-3 col-md-6">
                                <label class="form-label">نوع نمایش</label>
                                <select name="view" class="form-select" onchange="this.form.submit()">
                                    <option value="daily" <?php echo $data['view_type'] == 'daily' ? 'selected' : ''; ?>>روزانه</option>
                                    <option value="range" <?php echo $data['view_type'] == 'range' ? 'selected' : ''; ?>>بازه زمانی</option>
                                </select>
                            </div>
                            
                            <?php if ($data['view_type'] == 'daily'): ?>
                                <div class="col-lg-3 col-md-6">
                                    <label class="form-label">تاریخ</label>
                                    <input type="date" name="date" class="form-control" 
                                           value="<?php echo $data['selected_date']; ?>" required>
                                </div>
                            <?php else: ?>
                                <div class="col-lg-3 col-md-6">
                                    <label class="form-label">از تاریخ</label>
                                    <input type="date" name="start_date" class="form-control" 
                                           value="<?php echo $data['start_date']; ?>">
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <label class="form-label">تا تاریخ</label>
                                    <input type="date" name="end_date" class="form-control" 
                                           value="<?php echo $data['end_date']; ?>">
                                </div>
                            <?php endif; ?>
                            
                            <div class="col-lg-3 col-md-6">
                                <label class="form-label">کلاس</label>
                                <select name="class_id" class="form-select">
                                    <option value="">همه کلاس‌ها</option>
                                    <?php foreach ($data['classes'] as $class): ?>
                                        <option value="<?php echo $class['id']; ?>" 
                                                <?php echo $data['selected_class'] == $class['id'] ? 'selected' : ''; ?>>
                                            <?php echo $class['name'] . ' - ' . $class['major_name']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-lg-3 col-md-6 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-search"></i> اعمال فیلتر
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- آمار کلی -->
                <?php
                $total_records = count($data['attendance']);
                $present_count = count(array_filter($data['attendance'], function($a) { return $a['status'] == 'present'; }));
                $absent_count = count(array_filter($data['attendance'], function($a) { return $a['status'] == 'absent'; }));
                $late_count = count(array_filter($data['attendance'], function($a) { return $a['status'] == 'late'; }));
                $excused_count = count(array_filter($data['attendance'], function($a) { return $a['status'] == 'excused'; }));
                $attendance_rate = $total_records > 0 ? round(($present_count / $total_records) * 100, 1) : 0;
                ?>

                <div class="row mb-4">
                    <div class="col-xl-2 col-md-4 col-6 mb-3">
                        <div class="stats-card bg-primary">
                            <h5><?php echo $total_records; ?></h5>
                            <small>تعداد رکوردها</small>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-6 mb-3">
                        <div class="stats-card bg-success">
                            <h5><?php echo $present_count; ?></h5>
                            <small>حاضر</small>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-6 mb-3">
                        <div class="stats-card bg-danger">
                            <h5><?php echo $absent_count; ?></h5>
                            <small>غایب</small>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-6 mb-3">
                        <div class="stats-card bg-warning text-dark">
                            <h5><?php echo $late_count; ?></h5>
                            <small>تأخیر</small>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-6 mb-3">
                        <div class="stats-card bg-secondary">
                            <h5><?php echo $excused_count; ?></h5>
                            <small>عذردار</small>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-6 mb-3">
                        <div class="stats-card bg-info">
                            <h5><?php echo $attendance_rate; ?>%</h5>
                            <small>نرخ حضور</small>
                        </div>
                    </div>
                </div>

                <!-- تب‌های مختلف نمایش -->
                <ul class="nav nav-tabs" id="attendanceTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="daily-tab" data-bs-toggle="tab" data-bs-target="#daily" type="button" role="tab">
                            <i class="bi bi-calendar-day"></i> نمایش روزانه
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="student-tab" data-bs-toggle="tab" data-bs-target="#student" type="button" role="tab">
                            <i class="bi bi-person"></i> بر اساس دانش‌آموز
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="stats-tab" data-bs-toggle="tab" data-bs-target="#stats" type="button" role="tab">
                            <i class="bi bi-graph-up"></i> آمار و نمودار
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="attendanceTabsContent">
                    <!-- تب نمایش روزانه -->
                    <div class="tab-pane fade show active" id="daily" role="tabpanel">
                        <?php if (!empty($data['attendance_by_date'])): ?>
                            <?php foreach ($data['attendance_by_date'] as $date => $date_attendance): ?>
                                <div class="date-section">
                                    <div class="date-header d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">
                                            <i class="bi bi-calendar-check"></i>
                                            تاریخ: <?php echo JalaliDate::gregorianToJalali($date); ?>
                                        </h5>
                                        <span class="badge bg-secondary"><?php echo count($date_attendance); ?> رکورد</span>
                                    </div>
                                    
                                    <?php
                                    // گروه‌بندی بر اساس کلاس و دانش‌آموز
                                    $date_attendance_by_class_student = [];
                                    foreach ($date_attendance as $record) {
                                        $class_name = $record['class_name'];
                                        $student_id = $record['student_id'];
                                        
                                        if (!isset($date_attendance_by_class_student[$class_name])) {
                                            $date_attendance_by_class_student[$class_name] = [];
                                        }
                                        if (!isset($date_attendance_by_class_student[$class_name][$student_id])) {
                                            $date_attendance_by_class_student[$class_name][$student_id] = [
                                                'student_info' => [
                                                    'first_name' => $record['first_name'],
                                                    'last_name' => $record['last_name'],
                                                    'student_number' => $record['student_number']
                                                ],
                                                'courses' => []
                                            ];
                                        }
                                        $date_attendance_by_class_student[$class_name][$student_id]['courses'][] = $record;
                                    }
                                    ?>
                                    
                                    <?php foreach ($date_attendance_by_class_student as $class_name => $class_students): ?>
                                        <div class="class-section mb-4">
                                            <h6 class="text-primary mb-3">
                                                <i class="bi bi-house-door"></i>
                                                کلاس <?php echo $class_name; ?>
                                                <span class="badge bg-primary"><?php echo count($class_students); ?> دانش‌آموز</span>
                                            </h6>
                                            
                                            <?php foreach ($class_students as $student_id => $student_data): ?>
                                                <div class="card mb-3">
                                                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                                        <h6 class="mb-0">
                                                            <i class="bi bi-person"></i>
                                                            <?php echo $student_data['student_info']['first_name'] . ' ' . $student_data['student_info']['last_name']; ?>
                                                            <small class="text-muted">(شماره: <?php echo $student_data['student_info']['student_number']; ?>)</small>
                                                        </h6>
                                                        <span class="badge bg-primary"><?php echo count($student_data['courses']); ?> درس</span>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="table-responsive">
                                                            <table class="table table-sm table-hover">
                                                                <thead class="table-light">
                                                                    <tr>
                                                                        <th>ردیف</th>
                                                                        <th>درس</th>
                                                                        <th>کد درس</th>
                                                                        <th>وضعیت</th>
                                                                        <th>معلم</th>
                                                                        <th>توضیحات</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php foreach ($student_data['courses'] as $index => $course): ?>
                                                                        <tr class="<?php echo $course['status']; ?>">
                                                                            <td><strong><?php echo $index + 1; ?></strong></td>
                                                                            <td>
                                                                                <strong><?php echo $course['course_name']; ?></strong>
                                                                            </td>
                                                                            <td>
                                                                                <small class="text-muted"><?php echo $course['course_code']; ?></small>
                                                                            </td>
                                                                            <td>
                                                                                <?php
                                                                                $status_badge = '';
                                                                                switch($course['status']) {
                                                                                    case 'present':
                                                                                        $status_badge = '<span class="badge present attendance-badge"><i class="bi bi-check-circle"></i> حاضر</span>';
                                                                                        break;
                                                                                    case 'absent':
                                                                                        $status_badge = '<span class="badge absent attendance-badge"><i class="bi bi-x-circle"></i> غایب</span>';
                                                                                        break;
                                                                                    case 'late':
                                                                                        $status_badge = '<span class="badge late attendance-badge"><i class="bi bi-clock"></i> تأخیر</span>';
                                                                                        break;
                                                                                    case 'excused':
                                                                                        $status_badge = '<span class="badge excused attendance-badge"><i class="bi bi-envelope"></i> عذردار</span>';
                                                                                        break;
                                                                                }
                                                                                echo $status_badge;
                                                                                ?>
                                                                            </td>
                                                                            <td>
                                                                                <small><?php echo $course['teacher_first_name'] . ' ' . $course['teacher_last_name']; ?></small>
                                                                            </td>
                                                                            <td>
                                                                                <small class="text-muted"><?php echo $course['notes'] ?: '-'; ?></small>
                                                                            </td>
                                                                        </tr>
                                                                    <?php endforeach; ?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="alert alert-info text-center py-4">
                                <i class="bi bi-info-circle display-4"></i>
                                <h5 class="mt-3">هیچ رکورد حضور و غیابی برای فیلترهای انتخاب شده یافت نشد.</h5>
                                <p class="text-muted">لطفا فیلترهای دیگری را امتحان کنید.</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- تب نمایش بر اساس دانش‌آموز -->
                    <div class="tab-pane fade" id="student" role="tabpanel">
                        <?php if (!empty($data['attendance_by_student'])): ?>
                            <?php foreach ($data['attendance_by_student'] as $student_id => $student_data): ?>
                                <div class="card mb-4">
                                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">
                                            <i class="bi bi-person-circle"></i>
                                            <?php echo $student_data['student_info']['first_name'] . ' ' . $student_data['student_info']['last_name']; ?>
                                            <small class="text-muted">(<?php echo $student_data['student_info']['student_number']; ?>)</small>
                                            - کلاس <?php echo $student_data['student_info']['class_name']; ?>
                                        </h6>
                                        <div>
                                            <span class="badge bg-primary"><?php echo count($student_data['records']); ?> رکورد</span>
                                            <a href="<?php echo BASE_URL; ?>assistant/disciplinary?student_id=<?php echo $student_id; ?>" 
                                               class="btn btn-sm btn-outline-danger me-2">
                                                <i class="bi bi-shield-exclamation"></i> ثبت تخلف
                                            </a>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm table-hover">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>تاریخ</th>
                                                        <th>درس</th>
                                                        <th>وضعیت</th>
                                                        <th>معلم</th>
                                                        <th>توضیحات</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($student_data['records'] as $record): ?>
                                                        <tr class="<?php echo $record['status']; ?>">
                                                            <td>
                                                                <small><?php echo JalaliDate::gregorianToJalali($record['attendance_date']); ?></small>
                                                            </td>
                                                            <td>
                                                                <strong><?php echo $record['course_name']; ?></strong>
                                                                <br>
                                                                <small class="text-muted"><?php echo $record['course_code']; ?></small>
                                                            </td>
                                                            <td>
                                                                <?php
                                                                switch($record['status']) {
                                                                    case 'present':
                                                                        echo '<span class="badge present attendance-badge"><i class="bi bi-check-circle"></i> حاضر</span>';
                                                                        break;
                                                                    case 'absent':
                                                                        echo '<span class="badge absent attendance-badge"><i class="bi bi-x-circle"></i> غایب</span>';
                                                                        break;
                                                                    case 'late':
                                                                        echo '<span class="badge late attendance-badge"><i class="bi bi-clock"></i> تأخیر</span>';
                                                                        break;
                                                                    case 'excused':
                                                                        echo '<span class="badge excused attendance-badge"><i class="bi bi-envelope"></i> عذردار</span>';
                                                                        break;
                                                                }
                                                                ?>
                                                            </td>
                                                            <td>
                                                                <small><?php echo $record['teacher_first_name'] . ' ' . $record['teacher_last_name']; ?></small>
                                                            </td>
                                                            <td>
                                                                <small class="text-muted"><?php echo $record['notes'] ?: '-'; ?></small>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="alert alert-info text-center py-4">
                                <i class="bi bi-info-circle display-4"></i>
                                <h5 class="mt-3">هیچ رکوردی یافت نشد.</h5>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- تب آمار و نمودار -->
                    <div class="tab-pane fade" id="stats" role="tabpanel">
                        <div class="row">
                            <div class="col-lg-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="bi bi-bar-chart"></i> آمار روزانه</h6>
                                    </div>
                                    <div class="card-body">
                                        <?php if (!empty($data['attendance_stats'])): ?>
                                            <div class="table-responsive">
                                                <table class="table table-sm table-hover">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>تاریخ</th>
                                                            <th>کلاس</th>
                                                            <th>حاضر</th>
                                                            <th>غایب</th>
                                                            <th>تأخیر</th>
                                                            <th>عذردار</th>
                                                            <th>نرخ حضور</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($data['attendance_stats'] as $stat): ?>
                                                            <?php
                                                            $total = $stat['total_records'];
                                                            $rate = $total > 0 ? round(($stat['present_count'] / $total) * 100, 1) : 0;
                                                            ?>
                                                            <tr>
                                                                <td>
                                                                    <small><?php echo JalaliDate::gregorianToJalali($stat['attendance_date']); ?></small>
                                                                </td>
                                                                <td>
                                                                    <small><?php echo $stat['class_name']; ?></small>
                                                                </td>
                                                                <td class="text-success"><strong><?php echo $stat['present_count']; ?></strong></td>
                                                                <td class="text-danger"><strong><?php echo $stat['absent_count']; ?></strong></td>
                                                                <td class="text-warning"><strong><?php echo $stat['late_count']; ?></strong></td>
                                                                <td class="text-secondary"><strong><?php echo $stat['excused_count']; ?></strong></td>
                                                                <td>
                                                                    <span class="badge bg-<?php echo $rate >= 90 ? 'success' : ($rate >= 80 ? 'warning' : 'danger'); ?>">
                                                                        <?php echo $rate; ?>%
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        <?php else: ?>
                                            <div class="alert alert-info text-center">
                                                <i class="bi bi-info-circle"></i>
                                                هیچ آماری یافت نشد.
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="bi bi-exclamation-triangle"></i> دانش‌آموزان با غیبت مکرر</h6>
                                    </div>
                                    <div class="card-body">
                                        <?php if (!empty($data['absent_students'])): ?>
                                            <div class="table-responsive">
                                                <table class="table table-sm table-hover">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>دانش‌آموز</th>
                                                            <th>کلاس</th>
                                                            <th>تعداد غیبت</th>
                                                            <th>عملیات</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($data['absent_students'] as $student): ?>
                                                            <tr>
                                                                <td>
                                                                    <strong><?php echo $student['first_name'] . ' ' . $student['last_name']; ?></strong>
                                                                    <br>
                                                                    <small class="text-muted"><?php echo $student['student_number']; ?></small>
                                                                </td>
                                                                <td>
                                                                    <small><?php echo $student['class_name']; ?></small>
                                                                </td>
                                                                <td>
                                                                    <span class="badge bg-danger"><?php echo $student['absent_days']; ?> روز</span>
                                                                </td>
                                                                <td>
                                                                    <a href="<?php echo BASE_URL; ?>assistant/disciplinary?student_id=<?php echo $student['id']; ?>" 
                                                                       class="btn btn-sm btn-outline-danger">
                                                                        <i class="bi bi-shield-exclamation"></i> ثبت تخلف
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        <?php else: ?>
                                            <div class="alert alert-success text-center">
                                                <i class="bi bi-check-circle"></i>
                                                هیچ دانش‌آموزی با غیبت مکرر یافت نشد.
                                            </div>
                                        <?php endif; ?>
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

        // فعال کردن تب‌ها
        const triggerTabList = [].slice.call(document.querySelectorAll('#attendanceTabs button'))
        triggerTabList.forEach(function (triggerEl) {
            const tabTrigger = new bootstrap.Tab(triggerEl)
            triggerEl.addEventListener('click', function (event) {
                event.preventDefault()
                tabTrigger.show()
            })
        });

        // اضافه کردن انیمیشن به کارت‌های آمار
        const statsCards = document.querySelectorAll('.stats-card');
        statsCards.forEach((card, index) => {
            card.style.animationDelay = `${index * 0.1}s`;
        });
    });
    </script>
</body>
</html>