<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مدیریت دانش‌آموزان - پنل معاون</title>
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
        .student-card { 
            transition: transform 0.2s, box-shadow 0.2s; 
            border: 1px solid #e9ecef;
            border-radius: 10px;
        }
        .student-card:hover { 
            transform: translateY(-5px); 
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .disciplinary-score { 
            font-weight: bold; 
            font-size: 1.2rem;
        }
        .score-excellent { color: #28a745; }
        .score-good { color: #20c997; }
        .score-average { color: #ffc107; }
        .score-poor { color: #fd7e14; }
        .score-very-poor { color: #dc3545; }

        /* استایل‌های ریسپانسیو */
        @media (max-width: 768px) {
            .col-md-6, .col-lg-4 {
                margin-bottom: 15px;
            }
            .card-body {
                padding: 15px;
            }
            .btn-sm {
                font-size: 0.8rem;
                padding: 5px 10px;
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

        /* استایل برای جستجو */
        .search-highlight {
            background-color: #fff3cd;
            padding: 2px 4px;
            border-radius: 3px;
        }

        /* استایل‌های Modal */
        .modal-header { border-bottom: 2px solid #dee2e6; }
        .modal-footer { border-top: 1px solid #dee2e6; }
        .form-label .text-danger { font-size: 0.8rem; }
        .is-invalid { border-color: #dc3545 !important; }
        .is-valid { border-color: #198754 !important; }

        /* انیمیشن برای Modal */
        .modal.fade .modal-dialog {
            transition: transform 0.3s ease-out;
            transform: translate(0, -50px);
        }
        .modal.show .modal-dialog {
            transform: none;
        }

        /* استایل برای ظرفیت کلاس */
        .capacity-full { color: #dc3545; font-weight: bold; }
        .capacity-warning { color: #fd7e14; }
        .capacity-good { color: #198754; }
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
                        <i class="bi bi-people"></i>
                        مدیریت دانش‌آموزان پایه <?php echo $data['assistant']['grade_name']; ?>
                    </h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <!-- دکمه افزودن دانش‌آموز جدید -->
                            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                                <i class="bi bi-person-plus"></i> افزودن دانش‌آموز
                            </button>
                            <a href="<?php echo BASE_URL; ?>assistant/disciplinary" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-shield-exclamation"></i> ثبت تخلف انضباطی
                            </a>
                        </div>
                    </div>
                </div>

                <!-- پیام‌های موفقیت و خطا -->
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle"></i>
                        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle"></i>
                        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- فیلتر کلاس -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="bi bi-funnel"></i> فیلتر بر اساس کلاس</h5>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <select class="form-select" onchange="window.location.href='<?php echo BASE_URL; ?>assistant/students?class_id=' + this.value">
                                    <option value="">همه کلاس‌ها</option>
                                    <?php foreach ($data['classes'] as $class): ?>
                                        <?php 
                                        $capacity_class = 'capacity-good';
                                        if ($class['student_count'] >= $class['capacity']) {
                                            $capacity_class = 'capacity-full';
                                        } elseif ($class['student_count'] >= $class['capacity'] * 0.8) {
                                            $capacity_class = 'capacity-warning';
                                        }
                                        ?>
                                        <option value="<?php echo $class['id']; ?>" 
                                                <?php echo $data['selected_class'] == $class['id'] ? 'selected' : ''; ?>>
                                            <?php echo $class['name'] . ' - ' . $class['major_name']; ?>
                                            (<?php echo $class['student_count']; ?>/<?php echo $class['capacity']; ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">
                                        <i class="bi bi-people"></i>
                                        نمایش 
                                        <strong><?php echo count($data['students']); ?></strong> 
                                        دانش‌آموز
                                        <?php if ($data['selected_class']): ?>
                                            در کلاس انتخاب شده
                                        <?php endif; ?>
                                    </span>
                                    <a href="<?php echo BASE_URL; ?>assistant/attendance" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-clipboard-check"></i> مشاهده حضور و غیاب
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- جستجوی لایو -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="bi bi-search"></i> جستجوی پیشرفته</h5>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                                    <input type="text" id="liveSearch" class="form-control" placeholder="جستجوی دانش‌آموز بر اساس نام، شماره دانش‌آموزی یا کلاس...">
                                    <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">
                                        <i class="bi bi-filter-circle"></i>
                                        نمایش 
                                        <strong id="studentCount"><?php echo count($data['students']); ?></strong> 
                                        دانش‌آموز
                                    </span>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                                            <i class="bi bi-person-plus"></i> افزودن
                                        </button>
                                        <a href="<?php echo BASE_URL; ?>assistant/attendance" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-clipboard-check"></i> حضور و غیاب
                                        </a>
                                        <a href="<?php echo BASE_URL; ?>assistant/disciplinary" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-shield-exclamation"></i> ثبت تخلف
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- دکمه‌های خروجی -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="bi bi-download"></i> خروجی‌ها</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="text-muted mb-2">خروجی از کلاس انتخاب شده:</p>
                                <?php if ($data['selected_class']): ?>
                                    <?php 
                                    $selected_class_name = '';
                                    foreach ($data['classes'] as $class) {
                                        if ($class['id'] == $data['selected_class']) {
                                            $selected_class_name = $class['name'];
                                            break;
                                        }
                                    }
                                    ?>
                                    <div class="btn-group">
                                        <a href="<?php echo BASE_URL; ?>assistant/exportStudentsPDF?class_id=<?php echo $data['selected_class']; ?>" 
                                           class="btn btn-danger" target="_blank">
                                            <i class="bi bi-file-pdf"></i> خروجی PDF
                                        </a>
                                        <a href="<?php echo BASE_URL; ?>assistant/exportStudentsExcel?class_id=<?php echo $data['selected_class']; ?>" 
                                           class="btn btn-success">
                                            <i class="bi bi-file-excel"></i> خروجی Excel
                                        </a>
                                    </div>
                                    <small class="text-muted mt-2 d-block">کلاس: <?php echo $selected_class_name; ?></small>
                                <?php else: ?>
                                    <p class="text-warning">لطفاً ابتدا یک کلاس انتخاب کنید</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- لیست دانش‌آموزان -->
                <div class="row" id="studentsContainer">
                    <?php foreach ($data['students'] as $student): ?>
                        <div class="col-xl-4 col-lg-6 col-md-6 mb-4 student-card-container">
                            <div class="card student-card h-100">
                                <div class="card-header d-flex justify-content-between align-items-center bg-light">
                                    <h6 class="mb-0 text-dark">
                                        <i class="bi bi-house-door"></i>
                                        <?php echo $student['class_name']; ?>
                                    </h6>
                                    <span class="badge bg-primary"><?php echo $student['student_number']; ?></span>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <h5 class="card-title text-dark student-name">
                                            <?php echo $student['first_name'] . ' ' . $student['last_name']; ?>
                                        </h5>
                                        <?php
                                        $score = $student['disciplinary_score'] ?? 20;
                                        $score_class = 'score-excellent';
                                        if ($score < 20) $score_class = 'score-good';
                                        if ($score < 18) $score_class = 'score-average';
                                        if ($score < 16) $score_class = 'score-poor';
                                        if ($score < 14) $score_class = 'score-very-poor';
                                        ?>
                                        <div class="text-center">
                                            <span class="disciplinary-score <?php echo $score_class; ?> d-block">
                                                <?php echo $score; ?>
                                            </span>
                                            <small class="text-muted">نمره انضباطی</small>
                                        </div>
                                    </div>
                                    
                                    <div class="student-info mb-3">
                                        <small class="text-muted d-block mb-2">
                                            <i class="bi bi-telephone"></i>
                                            <?php echo $student['mobile']; ?>
                                        </small>
                                        <small class="text-muted d-block mb-2">
                                            <i class="bi bi-book"></i>
                                            <?php echo $student['major_name']; ?>
                                        </small>
                                        <small class="text-muted d-block">
                                            <i class="bi bi-person"></i>
                                            <?php echo $student['father_name'] ?? 'نام پدر ثبت نشده'; ?>
                                        </small>
                                    </div>

                                    <div class="student-actions mt-4">
                                        <a href="<?php echo BASE_URL; ?>assistant/studentDetail?student_id=<?php echo $student['id']; ?>" 
                                           class="btn btn-sm btn-outline-primary w-100 mb-2">
                                            <i class="bi bi-eye"></i> مشاهده پرونده کامل
                                        </a>
                                        <a href="<?php echo BASE_URL; ?>assistant/disciplinary?student_id=<?php echo $student['id']; ?>" 
                                           class="btn btn-sm btn-outline-danger w-100">
                                            <i class="bi bi-plus-circle"></i> ثبت تخلف انضباطی
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if (empty($data['students'])): ?>
                    <div class="alert alert-info text-center py-4">
                        <i class="bi bi-info-circle display-4"></i>
                        <h5 class="mt-3">هیچ دانش‌آموزی یافت نشد.</h5>
                        <p class="text-muted">لطفا فیلترهای جستجو را تغییر دهید یا دانش‌آموز جدیدی اضافه کنید.</p>
                        <button type="button" class="btn btn-success mt-2" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                            <i class="bi bi-person-plus"></i> افزودن اولین دانش‌آموز
                        </button>
                    </div>
                <?php endif; ?>

                <!-- پیام زمانی که هیچ نتیجه‌ای در جستجو پیدا نشود -->
                <div id="noResults" class="alert alert-warning text-center py-4" style="display: none;">
                    <i class="bi bi-search display-4"></i>
                    <h5 class="mt-3">هیچ دانش‌آموزی مطابق با جستجوی شما یافت نشد.</h5>
                    <p class="text-muted">لطفا عبارت جستجو را تغییر دهید.</p>
                    <button type="button" class="btn btn-success mt-2" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                        <i class="bi bi-person-plus"></i> افزودن دانش‌آموز جدید
                    </button>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal افزودن دانش‌آموز -->
    <div class="modal fade" id="addStudentModal" tabindex="-1" aria-labelledby="addStudentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="addStudentModalLabel">
                        <i class="bi bi-person-plus"></i> افزودن دانش‌آموز جدید
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" action="<?php echo BASE_URL; ?>assistant/addStudent">
                    <div class="modal-body">
                        <!-- اطلاعات دانش‌آموز -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-success border-bottom pb-2">
                                    <i class="bi bi-person"></i> اطلاعات دانش‌آموز
                                </h6>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">نام <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" class="form-control" required value="<?= $_POST['first_name'] ?? '' ?>">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">نام خانوادگی <span class="text-danger">*</span></label>
                                <input type="text" name="last_name" class="form-control" required value="<?= $_POST['last_name'] ?? '' ?>">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">شماره موبایل <span class="text-danger">*</span></label>
                                <input type="text" name="mobile" class="form-control" required value="<?= $_POST['mobile'] ?? '' ?>">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">کد ملی <span class="text-danger">*</span></label>
                                <input type="text" name="national_code" class="form-control" required value="<?= $_POST['national_code'] ?? '' ?>">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">تاریخ تولد</label>
                                <input type="date" name="birth_date" class="form-control" value="<?= $_POST['birth_date'] ?? '' ?>">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">نام پدر</label>
                                <input type="text" name="father_name" class="form-control" value="<?= $_POST['father_name'] ?? '' ?>">
                            </div>
                            
                            <div class="col-12 mb-3">
                                <label class="form-label">آدرس</label>
                                <textarea name="address" class="form-control" rows="2"><?= $_POST['address'] ?? '' ?></textarea>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">کلاس <span class="text-danger">*</span></label>
                                <select name="class_id" class="form-select" required>
                                    <option value="">انتخاب کلاس</option>
                                    <?php foreach ($data['classes'] as $class): ?>
                                        <?php 
                                        $capacity_class = 'capacity-good';
                                        if ($class['student_count'] >= $class['capacity']) {
                                            $capacity_class = 'capacity-full';
                                        } elseif ($class['student_count'] >= $class['capacity'] * 0.8) {
                                            $capacity_class = 'capacity-warning';
                                        }
                                        ?>
                                        <option value="<?php echo $class['id']; ?>" 
                                                class="<?php echo $capacity_class; ?>"
                                                <?= (isset($_POST['class_id']) && $_POST['class_id'] == $class['id']) ? 'selected' : '' ?>>
                                            <?php echo $class['name']; ?> - <?php echo $class['major_name']; ?>
                                            (ظرفیت: <?php echo $class['student_count']; ?>/<?php echo $class['capacity']; ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="form-text text-muted">
                                    فقط کلاس‌های پایه <?= $data['assistant']['grade_name'] ?> نمایش داده می‌شوند
                                </small>
                            </div>
                        </div>

                        <!-- اطلاعات اولیا -->
                        <div class="row">
                            <div class="col-12">
                                <h6 class="text-info border-bottom pb-2">
                                    <i class="bi bi-people"></i> اطلاعات اولیا (اختیاری)
                                </h6>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">نسبت</label>
                                <select name="relation_type" class="form-select">
                                    <option value="father" <?= (isset($_POST['relation_type']) && $_POST['relation_type'] == 'father') ? 'selected' : '' ?>>پدر</option>
                                    <option value="mother" <?= (isset($_POST['relation_type']) && $_POST['relation_type'] == 'mother') ? 'selected' : '' ?>>مادر</option>
                                    <option value="guardian" <?= (isset($_POST['relation_type']) && $_POST['relation_type'] == 'guardian') ? 'selected' : '' ?>>سرپرست</option>
                                </select>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">نام ولی</label>
                                <input type="text" name="parent_first_name" class="form-control" value="<?= $_POST['parent_first_name'] ?? '' ?>">
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label">نام خانوادگی ولی</label>
                                <input type="text" name="parent_last_name" class="form-control" value="<?= $_POST['parent_last_name'] ?? '' ?>">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">شماره موبایل ولی</label>
                                <input type="text" name="parent_mobile" class="form-control" value="<?= $_POST['parent_mobile'] ?? '' ?>">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">کد ملی ولی</label>
                                <input type="text" name="parent_national_code" class="form-control" value="<?= $_POST['parent_national_code'] ?? '' ?>">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> انصراف
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-lg"></i> ثبت دانش‌آموز
                        </button>
                    </div>
                </form>
            </div>
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

        // جستجوی لایو
        const liveSearch = document.getElementById('liveSearch');
        const clearSearch = document.getElementById('clearSearch');
        const studentCount = document.getElementById('studentCount');
        const studentCards = document.querySelectorAll('.student-card-container');
        const noResults = document.getElementById('noResults');
        const studentsContainer = document.getElementById('studentsContainer');

        function highlightText(text, searchTerm) {
            if (!searchTerm) return text;
            
            const regex = new RegExp(`(${searchTerm})`, 'gi');
            return text.replace(regex, '<mark class="search-highlight">$1</mark>');
        }

        function performSearch() {
            const searchTerm = liveSearch.value.toLowerCase().trim();
            let visibleCount = 0;

            studentCards.forEach(card => {
                const studentName = card.querySelector('.student-name').textContent.toLowerCase();
                const studentNumber = card.querySelector('.badge.bg-primary').textContent.toLowerCase();
                const className = card.querySelector('.card-header h6').textContent.toLowerCase();
                const majorName = card.querySelector('.student-info .bi-book').parentElement.textContent.toLowerCase();
                
                const isVisible = studentName.includes(searchTerm) || 
                                studentNumber.includes(searchTerm) || 
                                className.includes(searchTerm) ||
                                majorName.includes(searchTerm);

                if (isVisible) {
                    card.style.display = 'block';
                    visibleCount++;
                    
                    // هایلایت کردن متن
                    if (searchTerm) {
                        const nameElement = card.querySelector('.student-name');
                        const originalName = nameElement.textContent;
                        nameElement.innerHTML = highlightText(originalName, searchTerm);
                    }
                } else {
                    card.style.display = 'none';
                }
            });

            studentCount.textContent = visibleCount;

            // نمایش پیام عدم وجود نتیجه
            if (visibleCount === 0 && searchTerm) {
                noResults.style.display = 'block';
                studentsContainer.style.display = 'none';
            } else {
                noResults.style.display = 'none';
                studentsContainer.style.display = 'flex';
            }

            // بازگرداندن متن اصلی وقتی جستجو پاک شد
            if (!searchTerm) {
                studentCards.forEach(card => {
                    const nameElement = card.querySelector('.student-name');
                    const originalName = nameElement.textContent;
                    nameElement.textContent = originalName;
                });
            }
        }

        liveSearch.addEventListener('input', performSearch);

        clearSearch.addEventListener('click', function() {
            liveSearch.value = '';
            performSearch();
            liveSearch.focus();
        });

        // پاک کردن جستجو با کلید Esc
        liveSearch.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                liveSearch.value = '';
                performSearch();
            }
        });

        // نمایش وضعیت نمره انضباطی در کارت‌ها
        function updateScoreIndicators() {
            const scoreElements = document.querySelectorAll('.disciplinary-score');
            scoreElements.forEach(element => {
                const score = parseFloat(element.textContent);
                element.className = 'disciplinary-score score-excellent';
                
                if (score < 20) element.className = 'disciplinary-score score-good';
                if (score < 18) element.className = 'disciplinary-score score-average';
                if (score < 16) element.className = 'disciplinary-score score-poor';
                if (score < 14) element.className = 'disciplinary-score score-very-poor';
            });
        }

        updateScoreIndicators();

        // مدیریت Modal افزودن دانش‌آموز
        const addStudentModal = document.getElementById('addStudentModal');
        
        if (addStudentModal) {
            // ریست کردن فرم هنگام بستن modal
            addStudentModal.addEventListener('hidden.bs.modal', function () {
                const form = this.querySelector('form');
                form.reset();
                
                // پاک کردن پیام‌های خطا
                const errorAlerts = this.querySelectorAll('.alert.alert-danger');
                errorAlerts.forEach(alert => alert.remove());
            });
            
            // اعتبارسنجی فرم
            const form = addStudentModal.querySelector('form');
            form.addEventListener('submit', function(e) {
                const requiredFields = this.querySelectorAll('[required]');
                let isValid = true;
                
                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        isValid = false;
                        field.classList.add('is-invalid');
                    } else {
                        field.classList.remove('is-invalid');
                    }
                });
                
                if (!isValid) {
                    e.preventDefault();
                    // نمایش پیام خطا
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-danger alert-dismissible fade show mt-3';
                    alertDiv.innerHTML = `
                        <i class="bi bi-exclamation-triangle"></i>
                        لطفاً فیلدهای ضروری را پر کنید.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;
                    this.querySelector('.modal-body').prepend(alertDiv);
                }
            });
        }
        
        // نمایش اطلاعات کلاس در modal
        const classSelect = document.querySelector('select[name="class_id"]');
        if (classSelect) {
            classSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption.value) {
                    console.log('کلاس انتخاب شده:', selectedOption.text);
                }
            });
        }
    });
    </script>
</body>
</html>