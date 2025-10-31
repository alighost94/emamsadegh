<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>کارنامه آموزشی - پنل معاون</title>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v30.1.0/dist/font-face.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { font-family: 'Vazir', sans-serif; }
        .sidebar { background: #2c3e50; color: white; height: 100vh; position: fixed; width: 250px; }
        .sidebar .nav-link { color: #bdc3c7; padding: 15px 20px; border-bottom: 1px solid #34495e; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: white; background: #34495e; }
        .main-content { margin-right: 250px; padding: 20px; background: #f8f9fa; min-height: 100vh; }
        
        /* کارت آمار */
        .stat-card { 
            border: none; 
            border-radius: 12px; 
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            border-left: 4px solid;
        }
        .stat-card:hover { 
            transform: translateY(-3px); 
            box-shadow: 0 6px 20px rgba(0,0,0,0.15);
        }
        .stat-excellent { border-left-color: #28a745; }
        .stat-good { border-left-color: #20c997; }
        .stat-average { border-left-color: #ffc107; }
        .stat-poor { border-left-color: #fd7e14; }
        .stat-fail { border-left-color: #dc3545; }
        .stat-total { border-left-color: #007bff; }
        
        /* جدول لیست دانش‌آموزان */
        .students-table {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .students-table thead th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 15px 12px;
            font-weight: 600;
        }
        .students-table tbody tr {
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .students-table tbody tr:hover {
            background-color: #f8f9fa;
            transform: translateX(-5px);
        }
        .students-table tbody td {
            padding: 12px;
            vertical-align: middle;
            border-color: #e9ecef;
        }
        
        /* وضعیت نمرات */
        .grade-badge { 
            font-size: 0.75rem; 
            padding: 4px 8px; 
            border-radius: 20px;
        }
        .grade-excellent { background: #d4edda; color: #155724; }
        .grade-good { background: #d1ecf1; color: #0c5460; }
        .grade-average { background: #fff3cd; color: #856404; }
        .grade-poor { background: #ffeaa7; color: #8d6e00; }
        .grade-fail { background: #f8d7da; color: #721c24; }
        
        /* نمودارهای کوچک */
        .progress-mini {
            height: 6px;
            background: #e9ecef;
            border-radius: 3px;
            overflow: hidden;
        }
        .progress-mini-fill {
            height: 100%;
            border-radius: 3px;
        }
        
        /* دکمه‌ها */
        .btn-report {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            transition: all 0.3s ease;
        }
        .btn-report:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(102, 126, 234, 0.3);
        }
        
        /* فیلترها */
        .filter-section {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        /* مودال کارنامه */
        .report-card-modal .modal-dialog { max-width: 900px; }
        .report-card-header { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            color: white; 
        }
        .grade-table th { 
            background-color: #f8f9fa; 
            font-weight: 600;
            color: #495057;
        }
        
        /* رتبه‌بندی */
        .rank-badge {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 0.9rem;
        }
        .rank-1 { background: linear-gradient(135deg, #FFD700, #FFA500); color: #000; }
        .rank-2 { background: linear-gradient(135deg, #C0C0C0, #A0A0A0); color: #000; }
        .rank-3 { background: linear-gradient(135deg, #CD7F32, #A56A2B); color: #000; }
        .rank-other { background: #6c757d; color: white; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- سایدبار -->
            <?php include 'app/views/assistant/partials/sidebar.php'; ?>
            
            <!-- محتوای اصلی -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <!-- هدر -->
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
                    <div>
                        <h1 class="h3 mb-1">
                            <i class="bi bi-journal-text text-primary"></i>
                            کارنامه آموزشی پایه <?php echo $data['assistant']['grade_name']; ?>
                        </h1>
                        <p class="text-muted mb-0">مدیریت و بررسی کارنامه‌های دانش‌آموزان</p>
                    </div>
                    <div class="btn-toolbar">
                        <button class="btn btn-success me-2" onclick="exportToExcel()">
                            <i class="bi bi-file-earmark-excel"></i> خروجی Excel
                        </button>
                        <a href="<?php echo BASE_URL; ?>assistant/students" class="btn btn-outline-primary">
                            <i class="bi bi-people"></i> مدیریت دانش‌آموزان
                        </a>
                    </div>
                </div>

                <!-- توابع محاسباتی -->
                <?php
                // 🔥 تابع محاسبه نمره هر درس - نسخه تصحیح شده
                function calculateCourseGrade($course) {
                    if ($course['course_type'] == 'poodmani') {
                        // محاسبه برای دروس پودمانی
                        $poodman_grades = [];
                        for ($i = 1; $i <= 5; $i++) {
                            $grade_field = 'poodman' . $i;
                            if (isset($course[$grade_field]) && $course[$grade_field] !== null && $course[$grade_field] > 0) {
                                $poodman_grades[] = floatval($course[$grade_field]);
                            }
                        }
                        return !empty($poodman_grades) ? round(array_sum($poodman_grades) / count($poodman_grades), 2) : 0;
                    } else {
                        // 🔥 محاسبه برای دروس غیر پودمانی - نسخه تصحیح شده
                        $continuous1 = isset($course['continuous1']) && $course['continuous1'] !== null ? floatval($course['continuous1']) : 0;
                        $term1 = isset($course['term1']) && $course['term1'] !== null ? floatval($course['term1']) : 0;
                        $continuous2 = isset($course['continuous2']) && $course['continuous2'] !== null ? floatval($course['continuous2']) : 0;
                        $term2 = isset($course['term2']) && $course['term2'] !== null ? floatval($course['term2']) : 0;
                        
                        // محاسبه نمره نیمسال اول
                        $term1_grade = 0;
                        $term1_count = 0;
                        
                        if ($continuous1 > 0) {
                            $term1_grade += $continuous1;
                            $term1_count++;
                        }
                        if ($term1 > 0) {
                            $term1_grade += $term1;
                            $term1_count++;
                        }
                        $term1_average = ($term1_count > 0) ? ($term1_grade / $term1_count) : 0;
                        
                        // محاسبه نمره نیمسال دوم
                        $term2_grade = 0;
                        $term2_count = 0;
                        
                        if ($continuous2 > 0) {
                            $term2_grade += $continuous2;
                            $term2_count++;
                        }
                        if ($term2 > 0) {
                            $term2_grade += $term2;
                            $term2_count++;
                        }
                        $term2_average = ($term2_count > 0) ? ($term2_grade / $term2_count) : 0;
                        
                        // محاسبه نمره نهایی
                        if ($term1_average > 0 && $term2_average > 0) {
                            return round(($term1_average + $term2_average) / 2, 2);
                        } elseif ($term1_average > 0) {
                            return round($term1_average, 2);
                        } elseif ($term2_average > 0) {
                            return round($term2_average, 2);
                        } else {
                            return 0;
                        }
                    }
                }

                function calculateStudentAverage($courses) {
                    $total_grade = 0;
                    $course_count = 0;
                    
                    foreach ($courses as $course) {
                        $course_grade = calculateCourseGrade($course);
                        if ($course_grade > 0) {
                            $total_grade += $course_grade;
                            $course_count++;
                        }
                    }
                    
                    return $course_count > 0 ? round($total_grade / $course_count, 2) : 0;
                }

                function getGradeStatus($grade) {
                    if ($grade >= 17) return ['text' => 'عالی', 'class' => 'grade-excellent', 'color' => 'success'];
                    if ($grade >= 15) return ['text' => 'خوب', 'class' => 'grade-good', 'color' => 'info'];
                    if ($grade >= 12) return ['text' => 'متوسط', 'class' => 'grade-average', 'color' => 'warning'];
                    if ($grade >= 10) return ['text' => 'ضعیف', 'class' => 'grade-poor', 'color' => 'orange'];
                    return ['text' => 'مردود', 'class' => 'grade-fail', 'color' => 'danger'];
                }

                // محاسبات آماری
                $total_students = count($data['grades_by_student']);
                $excellent_count = $good_count = $average_count = $poor_count = $fail_count = 0;
                $all_averages = [];
                $students_with_avg = [];

                foreach ($data['grades_by_student'] as $student_id => $student_data) {
                    $total_avg = calculateStudentAverage($student_data['courses']);
                    $all_averages[] = $total_avg;
                    $students_with_avg[$student_id] = $total_avg;
                    
                    $status = getGradeStatus($total_avg);
                    switch($status['text']) {
                        case 'عالی': $excellent_count++; break;
                        case 'خوب': $good_count++; break;
                        case 'متوسط': $average_count++; break;
                        case 'ضعیف': $poor_count++; break;
                        case 'مردود': $fail_count++; break;
                    }
                }

                // مرتب‌سازی دانش‌آموزان بر اساس معدل
                arsort($students_with_avg);
                $ranked_students = [];
                $rank = 1;
                foreach ($students_with_avg as $student_id => $average) {
                    $ranked_students[$student_id] = [
                        'rank' => $rank,
                        'average' => $average
                    ];
                    $rank++;
                }

                $class_average = $all_averages ? round(array_sum($all_averages) / count($all_averages), 2) : 0;
                $max_average = $all_averages ? max($all_averages) : 0;
                $min_average = $all_averages ? min($all_averages) : 0;
                ?>

                <!-- آمار کلی -->
                <div class="row mb-4">
                    <div class="col-md-2 mb-3">
                        <div class="card stat-card stat-total text-center">
                            <div class="card-body">
                                <i class="bi bi-people-fill fs-1 text-primary mb-2"></i>
                                <h3 class="text-primary"><?php echo $total_students; ?></h3>
                                <small class="text-muted">کل دانش‌آموزان</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 mb-3">
                        <div class="card stat-card stat-excellent text-center">
                            <div class="card-body">
                                <i class="bi bi-trophy fs-1 text-success mb-2"></i>
                                <h3 class="text-success"><?php echo $excellent_count; ?></h3>
                                <small class="text-muted">عالی</small>
                                <div class="progress-mini mt-2">
                                    <div class="progress-mini-fill bg-success" style="width: <?php echo $total_students ? ($excellent_count/$total_students)*100 : 0; ?>%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 mb-3">
                        <div class="card stat-card stat-good text-center">
                            <div class="card-body">
                                <i class="bi bi-star fs-1 text-info mb-2"></i>
                                <h3 class="text-info"><?php echo $good_count; ?></h3>
                                <small class="text-muted">خوب</small>
                                <div class="progress-mini mt-2">
                                    <div class="progress-mini-fill bg-info" style="width: <?php echo $total_students ? ($good_count/$total_students)*100 : 0; ?>%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 mb-3">
                        <div class="card stat-card stat-average text-center">
                            <div class="card-body">
                                <i class="bi bi-graph-up fs-1 text-warning mb-2"></i>
                                <h3 class="text-warning"><?php echo $average_count; ?></h3>
                                <small class="text-muted">متوسط</small>
                                <div class="progress-mini mt-2">
                                    <div class="progress-mini-fill bg-warning" style="width: <?php echo $total_students ? ($average_count/$total_students)*100 : 0; ?>%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 mb-3">
                        <div class="card stat-card stat-poor text-center">
                            <div class="card-body">
                                <i class="bi bi-exclamation-triangle fs-1 text-orange mb-2"></i>
                                <h3 class="text-orange"><?php echo $poor_count + $fail_count; ?></h3>
                                <small class="text-muted">نیازمند توجه</small>
                                <div class="progress-mini mt-2">
                                    <div class="progress-mini-fill bg-orange" style="width: <?php echo $total_students ? (($poor_count+$fail_count)/$total_students)*100 : 0; ?>%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 mb-3">
                        <div class="card stat-card bg-light text-center">
                            <div class="card-body">
                                <i class="bi bi-calculator fs-1 text-secondary mb-2"></i>
                                <h3 class="text-secondary"><?php echo number_format($class_average, 1); ?></h3>
                                <small class="text-muted">میانگین پایه</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- فیلتر و جستجو -->
                <div class="filter-section">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">جستجوی دانش‌آموز</label>
                            <input type="text" id="searchInput" class="form-control" placeholder="نام، شماره دانش‌آموزی یا کلاس...">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">وضعیت تحصیلی</label>
                            <select id="statusFilter" class="form-select">
                                <option value="">همه وضعیت‌ها</option>
                                <option value="excellent">عالی</option>
                                <option value="good">خوب</option>
                                <option value="average">متوسط</option>
                                <option value="poor">ضعیف</option>
                                <option value="fail">مردود</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">مرتب‌سازی بر اساس</label>
                            <select id="sortFilter" class="form-select">
                                <option value="rank">رتبه</option>
                                <option value="name">نام دانش‌آموز</option>
                                <option value="class">کلاس</option>
                                <option value="average">معدل</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button class="btn btn-outline-secondary w-100" onclick="resetFilters()">
                                <i class="bi bi-arrow-clockwise"></i> بازنشانی
                            </button>
                        </div>
                    </div>
                </div>

                <!-- لیست دانش‌آموزان -->
                <div class="students-table">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th width="60">رتبه</th>
                                    <th>دانش‌آموز</th>
                                    <th width="120">شماره دانش‌آموزی</th>
                                    <th width="150">کلاس</th>
                                    <th width="100">معدل</th>
                                    <th width="120">وضعیت</th>
                                    <th width="100">تعداد دروس</th>
                                    <th width="120">عملیات</th>
                                </tr>
                            </thead>
                            <tbody id="studentsTableBody">
                                <?php foreach ($data['grades_by_student'] as $student_id => $student_data): ?>
                                    <?php
                                    $student_info = $student_data['student_info'];
                                    $courses = $student_data['courses'];
                                    $total_avg = $ranked_students[$student_id]['average'] ?? calculateStudentAverage($courses);
                                    $rank = $ranked_students[$student_id]['rank'] ?? 0;
                                    $status = getGradeStatus($total_avg);
                                    
                                    // تفکیک دروس
                                    $poodmani_count = count(array_filter($courses, fn($c) => $c['course_type'] == 'poodmani'));
                                    $non_poodmani_count = count(array_filter($courses, fn($c) => $c['course_type'] != 'poodmani'));
                                    $total_courses = count($courses);
                                    
                                    // 🔥 محاسبه معدل جداگانه برای دروس پودمانی و غیر پودمانی
                                    $poodmani_courses = array_filter($courses, fn($c) => $c['course_type'] == 'poodmani');
                                    $non_poodmani_courses = array_filter($courses, fn($c) => $c['course_type'] != 'poodmani');
                                    
                                    $poodmani_avg = calculateStudentAverage($poodmani_courses);
                                    $non_poodmani_avg = calculateStudentAverage($non_poodmani_courses);
                                    ?>
                                    
                                    <tr class="student-row" 
                                        data-name="<?php echo $student_info['first_name'] . ' ' . $student_info['last_name']; ?>"
                                        data-number="<?php echo $student_info['student_number']; ?>"
                                        data-class="<?php echo $student_info['class_name']; ?>"
                                        data-status="<?php echo $status['text']; ?>"
                                        data-average="<?php echo $total_avg; ?>">
                                        <td>
                                            <div class="rank-badge <?php echo $rank <= 3 ? 'rank-'.$rank : 'rank-other'; ?>">
                                                <?php echo $rank; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3" 
                                                     style="width: 40px; height: 40px;">
                                                    <span class="text-white fw-bold">
                                                        <?php echo substr($student_info['first_name'], 0, 1) . substr($student_info['last_name'], 0, 1); ?>
                                                    </span>
                                                </div>
                                                <div>
                                                    <div class="fw-bold"><?php echo $student_info['first_name'] . ' ' . $student_info['last_name']; ?></div>
                                                    <small class="text-muted"><?php echo $student_info['class_name']; ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary"><?php echo $student_info['student_number']; ?></span>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-dark"><?php echo $student_info['class_name']; ?></span>
                                        </td>
                                        <td>
                                            <div class="fw-bold fs-5 <?php echo $status['class']; ?>">
                                                <?php echo number_format($total_avg, 1); ?>
                                            </div>
                                            <!-- 🔥 نمایش معدل جداگانه -->
                                            <div class="small text-muted">
                                                پ: <?php echo number_format($poodmani_avg, 1); ?> | 
                                                ع: <?php echo number_format($non_poodmani_avg, 1); ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="grade-badge <?php echo $status['class']; ?>">
                                                <i class="bi bi-<?php echo $status['text'] == 'عالی' ? 'trophy' : ($status['text'] == 'مردود' ? 'exclamation-triangle' : 'circle-fill'); ?> me-1"></i>
                                                <?php echo $status['text']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="text-center">
                                                <div class="fw-bold text-primary"><?php echo $total_courses; ?></div>
                                                <small class="text-muted">
                                                    <?php echo $non_poodmani_count; ?> ع / <?php echo $poodmani_count; ?> پ
                                                </small>
                                            </div>
                                        </td>
                                        <td>
                                            <button class="btn btn-report w-100" onclick="event.stopPropagation(); showReportCard(<?php echo $student_id; ?>)">
                                                <i class="bi bi-eye me-1"></i> مشاهده کارنامه
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <?php if (empty($data['grades_by_student'])): ?>
                    <div class="alert alert-info text-center mt-4">
                        <i class="bi bi-info-circle fs-2 d-block mb-2"></i>
                        <h5>هیچ نمره‌ای برای دانش‌آموزان این پایه ثبت نشده است.</h5>
                        <p class="text-muted">پس از ثبت نمرات توسط معلمان، کارنامه‌ها در این بخش نمایش داده خواهند شد.</p>
                    </div>
                <?php endif; ?>

                <!-- اطلاعات پایانی -->
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6><i class="bi bi-info-circle text-primary me-2"></i>راهنما</h6>
                                <ul class="mb-0">
                                    <li>برای مشاهده کارنامه کامل روی ردیف دانش‌آموز کلیک کنید</li>
                                    <li>از فیلترها برای جستجوی سریع استفاده نمایید</li>
                                    <li>رتبه‌ها بر اساس معدل کل محاسبه می‌شوند</li>
                                    <li><strong>پ:</strong> معدل دروس پودمانی | <strong>ع:</strong> معدل دروس غیر پودمانی</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h6><i class="bi bi-graph-up text-success me-2"></i>آمار پایه</h6>
                                <div class="row mt-3">
                                    <div class="col-4">
                                        <div class="text-success">
                                            <div class="fs-4 fw-bold"><?php echo number_format($max_average, 1); ?></div>
                                            <small>بیشترین معدل</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="text-primary">
                                            <div class="fs-4 fw-bold"><?php echo number_format($class_average, 1); ?></div>
                                            <small>میانگین پایه</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="text-danger">
                                            <div class="fs-4 fw-bold"><?php echo number_format($min_average, 1); ?></div>
                                            <small>کمترین معدل</small>
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

    <!-- مودال کارنامه -->
    <div class="modal fade report-card-modal" id="reportCardModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header report-card-header">
                    <h5 class="modal-title" id="reportCardTitle">کارنامه آموزشی</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="reportCardContent">
                    <!-- محتوای کارنامه با Ajax پر می‌شود -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">بستن</button>
                    <button type="button" class="btn btn-primary" onclick="printReportCard()">
                        <i class="bi bi-printer"></i> چاپ کارنامه
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // فیلتر و جستجو
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const statusFilter = document.getElementById('statusFilter');
        const sortFilter = document.getElementById('sortFilter');
        
        function filterStudents() {
            const searchTerm = searchInput.value.toLowerCase();
            const statusValue = statusFilter.value;
            const sortValue = sortFilter.value;
            
            const rows = document.querySelectorAll('.student-row');
            let visibleRows = [];
            
            rows.forEach(row => {
                const name = row.dataset.name.toLowerCase();
                const number = row.dataset.number;
                const className = row.dataset.class.toLowerCase();
                const status = row.dataset.status;
                const average = parseFloat(row.dataset.average);
                
                const matchesSearch = name.includes(searchTerm) || number.includes(searchTerm) || className.includes(searchTerm);
                const matchesStatus = !statusValue || 
                    (statusValue === 'excellent' && status === 'عالی') ||
                    (statusValue === 'good' && status === 'خوب') ||
                    (statusValue === 'average' && status === 'متوسط') ||
                    (statusValue === 'poor' && status === 'ضعیف') ||
                    (statusValue === 'fail' && status === 'مردود');
                
                if (matchesSearch && matchesStatus) {
                    row.style.display = '';
                    visibleRows.push(row);
                } else {
                    row.style.display = 'none';
                }
            });
            
            // مرتب‌سازی
            sortRows(visibleRows, sortValue);
        }
        
        function sortRows(rows, sortBy) {
            const tbody = document.getElementById('studentsTableBody');
            const sortedRows = Array.from(rows).sort((a, b) => {
                switch(sortBy) {
                    case 'name':
                        return a.dataset.name.localeCompare(b.dataset.name);
                    case 'class':
                        return a.dataset.class.localeCompare(b.dataset.class);
                    case 'average':
                        return parseFloat(b.dataset.average) - parseFloat(a.dataset.average);
                    case 'rank':
                    default:
                        return Array.from(a.parentNode.children).indexOf(a) - Array.from(b.parentNode.children).indexOf(b);
                }
            });
            
            // حذف ردیف‌های فعلی و اضافه کردن مرتب‌شده
            rows.forEach(row => row.remove());
            sortedRows.forEach(row => tbody.appendChild(row));
        }
        
        searchInput.addEventListener('input', filterStudents);
        statusFilter.addEventListener('change', filterStudents);
        sortFilter.addEventListener('change', filterStudents);
        
        // کلیک روی ردیف برای نمایش کارنامه
        document.querySelectorAll('.student-row').forEach(row => {
            row.addEventListener('click', function() {
                const studentId = this.querySelector('button').getAttribute('onclick').match(/\d+/)[0];
                showReportCard(studentId);
            });
        });
    });
    
    function resetFilters() {
        document.getElementById('searchInput').value = '';
        document.getElementById('statusFilter').value = '';
        document.getElementById('sortFilter').value = 'rank';
        document.querySelectorAll('.student-row').forEach(row => row.style.display = '');
    }
    
    // توابع مودال کارنامه
    function showReportCard(studentId) {
        document.getElementById('reportCardContent').innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 text-muted">در حال بارگذاری کارنامه...</p>
            </div>
        `;
        
        const modal = new bootstrap.Modal(document.getElementById('reportCardModal'));
        modal.show();
        
        fetch(`<?php echo BASE_URL; ?>assistant/getStudentReportCard/${studentId}`)
            .then(response => response.text())
            .then(html => {
                document.getElementById('reportCardContent').innerHTML = html;
                document.getElementById('reportCardTitle').textContent = 'کارنامه آموزشی - ' + 
                    document.querySelector('#reportCardContent [data-student-name]')?.dataset.studentName || 'دانش‌آموز';
            })
            .catch(error => {
                document.getElementById('reportCardContent').innerHTML = `
                    <div class="alert alert-danger text-center">
                        <i class="bi bi-exclamation-triangle"></i>
                        خطا در بارگذاری کارنامه
                    </div>
                `;
            });
    }
    
    function printReportCard() {
        const content = document.getElementById('reportCardContent').innerHTML;
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <!DOCTYPE html>
            <html dir="rtl">
            <head>
                <title>کارنامه آموزشی</title>
                <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v30.1.0/dist/font-face.css" rel="stylesheet">
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
                <style>
                    body { font-family: 'Vazir', sans-serif; padding: 20px; }
                    @media print {
                        .no-print { display: none !important; }
                    }
                </style>
            </head>
            <body>
                ${content}
                <div class="no-print text-center mt-4">
                    <button onclick="window.print()" class="btn btn-primary me-2">چاپ</button>
                    <button onclick="window.close()" class="btn btn-secondary">بستن</button>
                </div>
            </body>
            </html>
        `);
        printWindow.document.close();
    }
    
    function exportToExcel() {
        alert('قابلیت خروجی Excel به زودی اضافه خواهد شد');
    }
    </script>
</body>
</html>