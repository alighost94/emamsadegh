<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>پرونده انضباطی - پنل معاون</title>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v30.1.0/dist/font-face.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/persian-datepicker@1.2.0/dist/css/persian-datepicker.min.css">
    
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        * { font-family: 'Vazir', sans-serif; }
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
            }
        }

        .sidebar .nav-link { color: #bdc3c7; padding: 15px 20px; border-bottom: 1px solid #34495e; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: white; background: #34495e; }
        .main-content { margin-right: 250px; padding: 20px; transition: all 0.3s; }
        .violation-card { border-right: 4px solid #dc3545; }
        .score-card { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        .deduction-badge { font-size: 0.8rem; }

        .pdp-btn-today, .pdp-btn-clear {
            background-color: #dc3545 !important;
            border-color: #dc3545 !important;
        }
        .pdp-selected {
            background-color: #dc3545 !important;
        }
        .pdp-header {
            background-color: #2c3e50 !important;
        }

        /* استایل‌های Select2 */
        .select2-container--default .select2-selection--single {
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
            height: 38px;
            padding: 0.375rem 0.75rem;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 24px;
            text-align: right;
        }
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #dc3545;
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
            .row {
                margin: 0 -5px;
            }
            .col-lg-5, .col-lg-7 {
                padding: 0 5px;
                margin-bottom: 15px;
            }
            .card-body {
                padding: 15px;
            }
            .table-responsive {
                font-size: 0.8rem;
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

        /* استایل‌های لودینگ */
        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            z-index: 2000;
            align-items: center;
            justify-content: center;
        }
        
        .loading-overlay.show {
            display: flex;
        }
        
        .loading-spinner {
            width: 60px;
            height: 60px;
            border: 5px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }
        
        .loading-text {
            color: white;
            margin-top: 15px;
            font-size: 1.1rem;
            text-align: center;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        .btn-loading {
            position: relative;
            pointer-events: none;
        }
        
        .btn-loading .spinner-border {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            width: 1rem;
            height: 1rem;
        }
    </style>
</head>
<body>
    <!-- Overlay لودینگ -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="d-flex flex-column align-items-center">
            <div class="loading-spinner"></div>
            <div class="loading-text">در حال ثبت تخلف، لطفاً منتظر بمانید...</div>
        </div>
    </div>

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
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">مدیریت پرونده انضباطی</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <button type="button" class="btn btn-sm btn-danger">
                            <i class="bi bi-plus-circle"></i> ثبت تخلف جدید
                        </button>
                    </div>
                </div>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($data['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $data['error']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (!empty($data['selected_student'])): ?>
                <div class="alert alert-info mb-4">
                    <div class="d-flex justify-content-between align-items-center flex-column flex-md-row">
                        <div class="mb-2 mb-md-0">
                            <strong>دانش‌آموز انتخاب شده:</strong>
                            <?php echo $data['selected_student']['first_name'] . ' ' . $data['selected_student']['last_name']; ?>
                            - <?php echo $data['selected_student']['class_name']; ?>
                            (<?php echo $data['selected_student']['student_number']; ?>)
                        </div>
                        <div>
                            <a href="<?php echo BASE_URL; ?>assistant/studentDetail?student_id=<?php echo $data['selected_student']['id']; ?>" 
                                class="btn btn-sm btn-outline-primary me-1">
                                <i class="bi bi-eye"></i> مشاهده پرونده
                            </a>
                            <a href="<?php echo BASE_URL; ?>assistant/disciplinary" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-x-circle"></i> حذف انتخاب
                            </a>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card score-card">
                            <div class="card-body">
                                <h5 class="card-title">وضعیت نمرات انضباطی پایه <?php echo $data['assistant']['grade_name']; ?></h5>
                                <div class="row text-center">
                                    <div class="col-6 col-md-3 mb-3">
                                        <h2 class="text-warning">۲۰</h2>
                                        <small>نمره پایه</small>
                                    </div>
                                    <div class="col-6 col-md-3 mb-3">
                                        <h2 class="text-success"><?php echo count(array_filter($data['disciplinary_scores'], function($s) { return $s['current_score'] >= 18; })); ?></h2>
                                        <small>عالی (۱۸-۲۰)</small>
                                    </div>
                                    <div class="col-6 col-md-3 mb-3">
                                        <h2 class="text-info"><?php echo count(array_filter($data['disciplinary_scores'], function($s) { return $s['current_score'] >= 16 && $s['current_score'] < 18; })); ?></h2>
                                        <small>خوب (۱۶-۱۷.۹)</small>
                                    </div>
                                    <div class="col-6 col-md-3 mb-3">
                                        <h2 class="text-danger"><?php echo count(array_filter($data['disciplinary_scores'], function($s) { return $s['current_score'] < 16; })); ?></h2>
                                        <small>نیاز توجه (زیر ۱۶)</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-5 col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header bg-danger text-white">
                                <h5 class="mb-0"><i class="bi bi-shield-exclamation"></i> ثبت تخلف انضباطی</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" id="violationForm">
                                    <div class="mb-3">
                                        <label class="form-label">دانش‌آموز</label>
                                        <select name="student_id" class="form-select student-select" required id="studentSelect">
                                            <option value="">انتخاب دانش‌آموز</option>
                                            <?php foreach ($data['students'] as $student): ?>
                                                <option value="<?php echo $student['id']; ?>"
                                                    <?php echo (!empty($data['selected_student_id']) && $data['selected_student_id'] == $student['id']) ? 'selected' : ''; ?>>
                                                    <?php echo $student['first_name'] . ' ' . $student['last_name'] . ' - ' . $student['class_name'] . ' (' . $student['student_number'] . ')'; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">تاریخ تخلف</label>
                                        <input type="text" name="violation_date" class="form-control" id="violationDate" 
                                               placeholder="برای انتخاب تاریخ کلیک کنید" required 
                                               value="<?php echo JalaliDate::now('Y/m/d'); ?>">
                                        <input type="hidden" name="violation_date_gregorian" id="violationDateGregorian">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">نوع تخلف</label>
                                        <select name="violation_type" class="form-select" required>
                                        <option value="">انتخاب نوع تخلف</option>

<!-- نظم و انضباط عمومی -->
<option value="تاخیر در ورود به هنرستان">تاخیر در ورود به هنرستان</option>
<option value="تاخیر در ورود به کلاس">تاخیر در ورود به کلاس</option>
<option value="عدم حضور در صبحگاه">عدم حضور در صبحگاه</option>
<option value="بی نظمی در کلاس">بی نظمی در کلاس</option>
<option value="بی نظمی در مراسم صبحگاه">بی نظمی در مراسم صبحگاه</option>
<option value="خروج زودهنگام از کلاس">خروج زودهنگام از کلاس</option>
<option value="خروج زودهنگام از هنرستان">خروج زودهنگام از هنرستان</option>
<option value="عدم انجام وظایف محوله">عدم انجام وظایف محوله</option>
<option value="عدم رعایت قوانین هنرستان">عدم رعایت قوانین هنرستان</option>
<option value="آسیب به اموال هنرستان">آسیب به اموال هنرستان</option>
<option value="ورود به فضاهای ممنوعه هنرستان">ورود به فضاهای ممنوعه هنرستان</option>

<!-- پوشش و ظاهر -->
<option value="پوشش نامناسب (نداشتن لباس فرم)">پوشش نامناسب (نداشتن لباس فرم)</option>
<option value="نداشتن لباس ورزشی یا کارگاهی">نداشتن لباس ورزشی یا کارگاهی</option>
<option value="وضعیت نامناسب ظاهری و موی نامناسب">وضعیت نامناسب ظاهری و موی نامناسب</option>
<option value="عدم رعایت بهداشت فردی">عدم رعایت بهداشت فردی</option>

<!-- رفتار و اخلاق -->
<option value="درگیری و اخلال در نظم هنرستان">درگیری و اخلال در نظم هنرستان</option>
<option value="شوخی نامتعارف با سایر هنرجویان">شوخی نامتعارف با سایر هنرجویان</option>
<option value="توهین یا هرگونه بی احترامی به کادر هنرستان">توهین یا هرگونه بی احترامی به کادر هنرستان</option>
<option value="بی احترامی به همکلاسی‌ها">بی احترامی به همکلاسی‌ها</option>
<option value="فحاشی یا به کار بردن الفاظ رکیک">فحاشی یا به کار بردن الفاظ رکیک</option>
<option value="اخراج از کلاس">اخراج از کلاس</option>

<!-- آموزشی و حضور -->
<option value="تقلب در امتحان">تقلب در امتحان</option>

<!-- وسایل و مواد ممنوعه -->
<option value="استفاده از تلفن همراه">استفاده از تلفن همراه</option>
<option value="به همراه داشتن یا مصرف مواد دخانی در هنرستان">به همراه داشتن یا مصرف مواد دخانی در هنرستان</option>
<option value="حمل یا استفاده از وسایل ممنوعه یا امثال آنها در هنرستان">حمل یا استفاده از وسایل ممنوعه یا امثال آنها در هنرستان</option>

<!-- سایر -->
<option value="سایر">سایر</option>

                                        </select>
                                    </div>
                                    
                      <div class="mb-3">
    <label class="form-label">کسر نمره</label>
    <select name="point_deduction" class="form-select" required>
        <option value="0.25">۰.۲۵ نمره (تخلف بسیار جزئی)</option>
        <option value="0.5">۰.۵ نمره (تخلف جزئی)</option>
        <option value="0.75">۰.۷۵ نمره (تخلف جزئی-متوسط)</option>
        <option value="1">۱ نمره (تخلف متوسط)</option>
        <option value="1.25">۱.۲۵ نمره (تخلف متوسط-شدید)</option>
        <option value="1.5">۱.۵ نمره (تخلف نسبتاً شدید)</option>
        <option value="1.75">۱.۷۵ نمره (تخلف شدید)</option>
        <option value="2">۲ نمره (تخلف بسیار شدید)</option>
        <option value="2.25">۲.۲۵ نمره (تخلف بحرانی)</option>
        <option value="2.5">۲.۵ نمره (تخلف بسیار بحرانی)</option>
        <option value="2.75">۲.۷۵ نمره (تخلف فوق‌العاده شدید)</option>
        <option value="3">۳ نمره (حداکثر کسر)</option>
    </select>
</div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">شرح تخلف</label>
                                        <textarea name="description" class="form-control" rows="3" 
                                                  placeholder="شرح کامل تخلف را وارد کنید..." required></textarea>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-danger w-100" id="submitBtn">
                                        <i class="bi bi-check-circle"></i> ثبت تخلف
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-7 col-md-6">
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">تخلفات ثبت شده</h5>
                                <span class="badge bg-primary"><?php echo count($data['disciplinary_records']); ?> مورد</span>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($data['disciplinary_records'])): ?>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-sm">
                                            <thead>
                                                <tr>
                                                    <th>دانش‌آموز</th>
                                                    <th>تاریخ</th>
                                                    <th>نوع</th>
                                                    <th>کسر</th>
                                                    <th>وضعیت</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($data['disciplinary_records'] as $record): ?>
                                               <!-- در فایل disciplinary.php، قسمت جدول تخلفات -->
<tr>
    <td>
        <div class="d-flex flex-column">
            <small class="fw-bold"><?php echo $record['first_name'] . ' ' . $record['last_name']; ?></small>
            <small class="text-muted"><?php echo $record['class_name']; ?></small>
        </div>
    </td>
    <td>
        <small><?php echo $record['jalali_date']; ?></small>
    </td>
    <td>
        <small><?php echo $record['violation_type']; ?></small>
    </td>
    <td>
        <span class="badge bg-danger deduction-badge">
            -<?php echo $record['point_deduction']; ?>
        </span>
    </td>
    <td>
        <span class="badge bg-success">تأیید شده</span>
    </td>
    <td>
        <div class="btn-group btn-group-sm">
            <a href="<?php echo BASE_URL; ?>assistant/editDisciplinary?record_id=<?php echo $record['id']; ?>" 
               class="btn btn-outline-warning btn-sm" title="ویرایش">
                <i class="bi bi-pencil"></i>
            </a>
            <button type="button" class="btn btn-outline-danger btn-sm delete-record" 
                    data-record-id="<?php echo $record['id']; ?>" 
                    data-student-name="<?php echo $record['first_name'] . ' ' . $record['last_name']; ?>"
                    title="حذف">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    </td>
</tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-info text-center py-3">
                                        <i class="bi bi-info-circle fs-4"></i>
                                        <p class="mt-2 mb-0">هیچ تخلفی ثبت نشده است.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">نمرات انضباطی</h5>
                                <span class="badge bg-primary"><?php echo count($data['disciplinary_scores']); ?> دانش‌آموز</span>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($data['disciplinary_scores'])): ?>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>دانش‌آموز</th>
                                                    <th>کلاس</th>
                                                    <th>نمره</th>
                                                    <th>کسر کل</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($data['disciplinary_scores'] as $score): ?>
                                                    <?php
                                                    $score_class = 'text-success';
                                                    if ($score['current_score'] < 20) $score_class = 'text-info';
                                                    if ($score['current_score'] < 18) $score_class = 'text-warning';
                                                    if ($score['current_score'] < 16) $score_class = 'text-danger';
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <small><?php echo $score['first_name'] . ' ' . $score['last_name']; ?></small>
                                                        </td>
                                                        <td>
                                                            <small class="text-muted"><?php echo $score['class_name']; ?></small>
                                                        </td>
                                                        <td>
                                                            <strong class="<?php echo $score_class; ?>">
                                                                <?php echo $score['current_score']; ?>
                                                            </strong>
                                                        </td>
                                                        <td>
                                                            <span class="text-danger">
                                                                -<?php echo $score['total_deductions']; ?>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-warning text-center py-3">
                                        <i class="bi bi-exclamation-triangle fs-4"></i>
                                        <p class="mt-2 mb-0">نمرات انضباطی هنوز محاسبه نشده است.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/persian-date@1.1.0/dist/persian-date.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/persian-datepicker@1.2.0/dist/js/persian-datepicker.min.js"></script>
    
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/fa.js"></script>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // فعال کردن Select2 برای جستجوی دانش‌آموزان
        $('.student-select').select2({
            language: 'fa',
            placeholder: "جستجوی دانش‌آموز (نام، شماره یا کلاس)...",
            allowClear: true,
            width: '100%',
            dir: 'rtl'
        });

        // فعال کردن تقویم شمسی
        $('#violationDate').persianDatepicker({
            format: 'YYYY/MM/DD',
            autoClose: true,
            initialValue: true,
            initialValueType: 'persian',
            position: 'auto',
            observer: true,
            calendar: {
                persian: {
                    locale: 'fa',
                    showHint: true
                }
            },
            onSelect: function(unixDate) {
                const selectedDate = $(this).val();
                if (selectedDate) {
                    const persianDateArray = selectedDate.split('/');
                    if (persianDateArray.length === 3) {
                        const year = parseInt(persianDateArray[0]);
                        const month = parseInt(persianDateArray[1]);
                        const day = parseInt(persianDateArray[2]);
                        
                        const gregorianDate = jalaliToGregorian(year, month, day);
                        const gregorianDateStr = `${gregorianDate[0]}-${gregorianDate[1].toString().padStart(2, '0')}-${gregorianDate[2].toString().padStart(2, '0')}`;
                        
                        document.getElementById('violationDateGregorian').value = gregorianDateStr;
                    }
                }
            }
        });

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

        // مدیریت ارسال فرم
        const violationForm = document.getElementById('violationForm');
        const loadingOverlay = document.getElementById('loadingOverlay');
        const submitBtn = document.getElementById('submitBtn');

        violationForm.addEventListener('submit', function(e) {
            // نمایش لودینگ
            loadingOverlay.classList.add('show');
            
            // غیرفعال کردن دکمه ارسال
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> در حال ثبت...';
            submitBtn.classList.add('btn-loading');
            
            // فرم به صورت عادی ارسال می‌شود
            // لودینگ تا بارگذاری مجدد صفحه نمایش داده می‌شود
        });

        // تابع تبدیل تاریخ شمسی به میلادی
        function jalaliToGregorian(jy, jm, jd) {
            const breaks = [-61, 9, 38, 199, 426, 686, 756, 818, 1111, 1181, 1210, 1635, 2060, 2097, 2192, 2262, 2324, 2394, 2456, 3178];
            const breaksLength = breaks.length;
            
            let gy = jy + 621;
            let leapJ = -14;
            let jp = breaks[0];
            
            if (jy < jp || jy >= breaks[breaksLength - 1]) {
                throw new Error('Invalid date');
            }
            
            let jump = 0;
            for (let i = 1; i < breaksLength; i++) {
                let jm = breaks[i];
                jump = jm - jp;
                if (jy < jm) {
                    break;
                }
                leapJ = leapJ + div(jump, 33) * 8 + div(mod(jump, 33), 4);
                jp = jm;
            }
            
            let n = jy - jp;
            leapJ = leapJ + div(n, 33) * 8 + div(mod(n, 33) + 3, 4);
            if (mod(jump, 33) === 4 && jump - n === 4) {
                leapJ++;
            }
            
            let leapG = div(gy, 4) - div((div(gy, 100) + 1) * 3, 4) - 150;
            let march = 20 + leapJ - leapG;
            
            if (march < 3) {
                gy--;
                march += 20;
            }
            
            let m = (jm - 1 + 9) % 12 + 1;
            let gd = jd;
            
            if (m < 7) {
                gd += (m - 1) * 31;
            } else {
                gd += (m - 7) * 30 + 186;
            }
            
            gd += march - 1;
            
            if (gd <= 0) {
                gy--;
                gd += 365 + (isLeapYear(gy) ? 1 : 0);
            } else if (gd > 365 + (isLeapYear(gy) ? 1 : 0)) {
                gd -= 365 + (isLeapYear(gy) ? 1 : 0);
                gy++;
            }
            
            let gm = 1;
            while (gd > getMonthDays(gy, gm)) {
                gd -= getMonthDays(gy, gm);
                gm++;
            }
            
            return [gy, gm, gd];
        }

        function div(a, b) {
            return ~~(a / b);
        }

        function mod(a, b) {
            return a - ~~(a / b) * b;
        }

        function isLeapYear(year) {
            return (year % 4 === 0 && year % 100 !== 0) || year % 400 === 0;
        }

        function getMonthDays(year, month) {
            const monthDays = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
            if (month === 2 && isLeapYear(year)) {
                return 29;
            }
            return monthDays[month - 1];
        }

        // مقداردهی اولیه برای تاریخ امروز
        const today = new Date();
        const todayGregorian = today.toISOString().split('T')[0];
        document.getElementById('violationDateGregorian').value = todayGregorian;
    });

    // مدیریت حذف موارد انضباطی
    document.querySelectorAll('.delete-record').forEach(button => {
        button.addEventListener('click', function() {
            const recordId = this.getAttribute('data-record-id');
            const studentName = this.getAttribute('data-student-name');
            
            if (confirm(`آیا از حذف مورد انضباطی برای دانش‌آموز "${studentName}" اطمینان دارید؟`)) {
                window.location.href = `<?php echo BASE_URL; ?>assistant/deleteDisciplinary?record_id=${recordId}`;
            }
        });
    });
    </script>
</body>
</html>