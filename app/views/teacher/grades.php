<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ثبت نمرات - پنل معلم</title>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v30.1.0/dist/font-face.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { font-family: 'Vazir', sans-serif; }
        .sidebar { background: #2c3e50; color: white; height: 100vh; position: fixed; width: 250px; }
        .sidebar .nav-link { color: #bdc3c7; padding: 15px 20px; border-bottom: 1px solid #34495e; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: white; background: #34495e; }
        .main-content { margin-right: 250px; padding: 20px; }
        .grade-input { width: 80px; text-align: center; }
        .poodmani-section, .non-poodmani-section { display: none; }
        .class-badge {
            background: #e3f2fd;
            color: #1976d2;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            border: 1px solid #bbdefb;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- سایدبار -->
            <?php include 'app/views/teacher/partials/sidebar.php'; ?>
            
            <!-- محتوای اصلی -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">ثبت نمرات دانش‌آموزان</h1>
                </div>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- فیلتر -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>انتخاب درس و کلاس</h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-5">
                                <label class="form-label">درس</label>
                                <select name="course_id" id="courseSelect" class="form-select" required 
                                        onchange="GradeApp.loadClasses(this.value)">
                                    <option value="">انتخاب درس</option>
                                    <?php foreach ($data['courses'] as $course): ?>
                                        <option value="<?php echo $course['course_id']; ?>" 
                                                data-class-id="<?php echo $course['class_id'] ?? ''; ?>"
                                                data-class-name="<?php echo $course['class_name'] ?? ''; ?>"
                                                <?php echo $data['selected_course'] == $course['course_id'] ? 'selected' : ''; ?>>
                                            <?php echo $course['course_code'] . ' - ' . $course['course_name'] . ' (' . $course['major_name'] . ' - ' . $course['grade_name'] . ')'; ?>
                                            <?php if (!empty($course['class_name'])): ?>
                                                - <?php echo $course['class_name']; ?>
                                            <?php endif; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <!-- بخش انتخاب کلاس -->
                            <div class="col-md-4" id="classSelection" style="<?php echo empty($data['selected_class']) ? 'display: none;' : ''; ?>">
                                <label class="form-label">کلاس</label>
                                <select name="class_id" id="classSelect" class="form-select">
                                    <option value="">همه کلاس‌ها</option>
                                    <?php if (!empty($data['selected_class'])): ?>
                                        <option value="<?php echo $data['selected_class']; ?>" selected>
                                            <?php echo $data['students'][0]['class_name'] ?? 'کلاس انتخاب شده'; ?>
                                        </option>
                                    <?php endif; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">نمایش دانش‌آموزان</button>
                            </div>
                        </form>
                    </div>
                </div>

                <?php if ($data['selected_course']): ?>
                    <?php
                        $selected_class_name = !empty($data['students']) ? $data['students'][0]['class_name'] : 'همه کلاس‌ها';
                    ?>
                    
                    <!-- هدر اطلاعات -->
                    <div class="alert alert-info d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>کلاس انتخاب شده:</strong> 
                            <span class="class-badge"><?php echo $selected_class_name; ?></span>
                            | <strong>تعداد دانش‌آموزان:</strong> <?php echo count($data['students']); ?> نفر
                            | <strong>نوع درس:</strong> 
                            <span class="badge bg-<?php echo $data['course_type'] == 'poodmani' ? 'info' : 'warning'; ?>">
                                <?php echo $data['course_type'] == 'poodmani' ? 'پودمانی' : 'غیر پودمانی'; ?>
                            </span>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5>ثبت نمرات</h5>
                            <span class="class-badge"><?php echo $selected_class_name; ?></span>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($data['students'])): ?>
                                <form method="POST">
                                    <input type="hidden" name="course_type" value="<?php echo $data['course_type']; ?>">
                                    <input type="hidden" name="class_id" value="<?php echo $data['selected_class']; ?>">
                                    
                                    <!-- هدر نمرات بر اساس نوع درس -->
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <?php if ($data['course_type'] == 'poodmani'): ?>
                                                <div class="poodmani-section">
                                                    <div class="alert alert-info">
                                                        <strong>درس پودمانی:</strong> لطفاً نمرات ۵ پودمان را وارد کنید (هر نمره از ۲۰)
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <div class="non-poodmani-section">
                                                    <div class="alert alert-warning">
                                                        <strong>درس غیر پودمانی:</strong> لطفاً نمرات مستمر و ترم دو نیمسال را وارد کنید (هر نمره از ۲۰)
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>ردیف</th>
                                                    <th>نام دانش‌آموز</th>
                                                    <th>شماره دانش‌آموزی</th>
                                                    <th>کلاس</th>
                                                    <?php if ($data['course_type'] == 'poodmani'): ?>
                                                        <th>پودمان ۱</th>
                                                        <th>پودمان ۲</th>
                                                        <th>پودمان ۳</th>
                                                        <th>پودمان ۴</th>
                                                        <th>پودمان ۵</th>
                                                    <?php else: ?>
                                                        <th>مستمر ۱</th>
                                                        <th>ترم ۱</th>
                                                        <th>مستمر ۲</th>
                                                        <th>ترم ۲</th>
                                                    <?php endif; ?>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                $grades_map = [];
                                                foreach ($data['existing_grades'] as $grade) {
                                                    $grades_map[$grade['student_id']] = $grade;
                                                }
                                                ?>
                                                
                                                <?php foreach ($data['students'] as $index => $student): ?>
                                                    <?php $existing_grade = $grades_map[$student['id']] ?? null; ?>
                                                    <tr>
                                                        <td><?php echo $index + 1; ?></td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <i class="bi bi-person-circle text-muted me-2"></i>
                                                                <?php echo $student['first_name'] . ' ' . $student['last_name']; ?>
                                                            </div>
                                                        </td>
                                                        <td><?php echo $student['student_number']; ?></td>
                                                        <td>
                                                            <span class="class-badge"><?php echo $student['class_name']; ?></span>
                                                        </td>
                                                        
                                                        <?php if ($data['course_type'] == 'poodmani'): ?>
                                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                                <td>
                                                                    <input type="number" 
                                                                           name="grades[<?php echo $student['id']; ?>][poodman<?php echo $i; ?>]" 
                                                                           class="form-control grade-input" 
                                                                           min="0" max="20" step="0.25"
                                                                           value="<?php echo $existing_grade['poodman' . $i] ?? ''; ?>"
                                                                           placeholder="0-20">
                                                                </td>
                                                            <?php endfor; ?>
                                                        <?php else: ?>
                                                            <td>
                                                                <input type="number" 
                                                                       name="grades[<?php echo $student['id']; ?>][continuous1]" 
                                                                       class="form-control grade-input" 
                                                                       min="0" max="20" step="0.25"
                                                                       value="<?php echo $existing_grade['continuous1'] ?? ''; ?>"
                                                                       placeholder="0-20">
                                                            </td>
                                                            <td>
                                                                <input type="number" 
                                                                       name="grades[<?php echo $student['id']; ?>][term1]" 
                                                                       class="form-control grade-input" 
                                                                       min="0" max="20" step="0.25"
                                                                       value="<?php echo $existing_grade['term1'] ?? ''; ?>"
                                                                       placeholder="0-20">
                                                            </td>
                                                            <td>
                                                                <input type="number" 
                                                                       name="grades[<?php echo $student['id']; ?>][continuous2]" 
                                                                       class="form-control grade-input" 
                                                                       min="0" max="20" step="0.25"
                                                                       value="<?php echo $existing_grade['continuous2'] ?? ''; ?>"
                                                                       placeholder="0-20">
                                                            </td>
                                                            <td>
                                                                <input type="number" 
                                                                       name="grades[<?php echo $student['id']; ?>][term2]" 
                                                                       class="form-control grade-input" 
                                                                       min="0" max="20" step="0.25"
                                                                       value="<?php echo $existing_grade['term2'] ?? ''; ?>"
                                                                       placeholder="0-20">
                                                            </td>
                                                        <?php endif; ?>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <div class="mt-3">
                                        <button type="submit" class="btn btn-success">
                                            <i class="bi bi-check-circle"></i> ثبت نمرات
                                        </button>
                                        <a href="<?php echo BASE_URL; ?>teacher/grades" class="btn btn-secondary">انصراف</a>
                                        
                                        <div class="form-check form-check-inline ms-3">
                                            <input class="form-check-input" type="checkbox" id="confirm_grades">
                                            <label class="form-check-label" for="confirm_grades">
                                                از صحت نمرات اطمینان دارم
                                            </label>
                                        </div>
                                    </div>
                                </form>
                            <?php else: ?>
                                <div class="alert alert-warning">
                                    <i class="bi bi-people me-2"></i>
                                    هیچ دانش‌آموزی برای این کلاس یافت نشد.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- نمایش نمرات ثبت شده (فقط دانش‌آموزان همان کلاس) -->
                    <?php if (!empty($data['existing_grades'])): ?>
                        <div class="card mt-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6>نمرات ثبت شده کلاس <?php echo $selected_class_name; ?></h6>
                                <span class="badge bg-primary"><?php echo count($data['existing_grades']); ?> دانش‌آموز</span>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>دانش‌آموز</th>
                                                <th>شماره دانش‌آموزی</th>
                                                <th>کلاس</th>
                                                <?php if ($data['course_type'] == 'poodmani'): ?>
                                                    <th>پودمان ۱</th>
                                                    <th>پودمان ۲</th>
                                                    <th>پودمان ۳</th>
                                                    <th>پودمان ۴</th>
                                                    <th>پودمان ۵</th>
                                                <?php else: ?>
                                                    <th>مستمر ۱</th>
                                                    <th>ترم ۱</th>
                                                    <th>مستمر ۲</th>
                                                    <th>ترم ۲</th>
                                                <?php endif; ?>
                                                <th>آخرین ویرایش</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($data['existing_grades'] as $grade): ?>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <i class="bi bi-person-circle text-muted me-2"></i>
                                                            <!-- 🔥 این خط رو تصحیح کردم -->
                                                            <?php echo $grade['student_name'] ?? (($grade['first_name'] ?? '') . ' ' . ($grade['last_name'] ?? '')) ?: 'نامشخص'; ?>
                                                        </div>
                                                    </td>
                                                    <td><?php echo $grade['student_number'] ?? '---'; ?></td>
                                                    <td>
                                                        <span class="class-badge"><?php echo $grade['class_name'] ?? '---'; ?></span>
                                                    </td>
                                                    <?php if ($data['course_type'] == 'poodmani'): ?>
                                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                                            <td>
                                                                <?php if (isset($grade['poodman' . $i]) && $grade['poodman' . $i] !== null): ?>
                                                                    <span class="badge bg-<?php echo $grade['poodman' . $i] >= 10 ? 'success' : 'danger'; ?>">
                                                                        <?php echo $grade['poodman' . $i]; ?>
                                                                    </span>
                                                                <?php else: ?>
                                                                    <span class="text-muted">-</span>
                                                                <?php endif; ?>
                                                            </td>
                                                        <?php endfor; ?>
                                                    <?php else: ?>
                                                        <td>
                                                            <?php if (isset($grade['continuous1']) && $grade['continuous1'] !== null): ?>
                                                                <span class="badge bg-<?php echo $grade['continuous1'] >= 10 ? 'success' : 'danger'; ?>">
                                                                    <?php echo $grade['continuous1']; ?>
                                                                </span>
                                                            <?php else: ?>
                                                                <span class="text-muted">-</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <?php if (isset($grade['term1']) && $grade['term1'] !== null): ?>
                                                                <span class="badge bg-<?php echo $grade['term1'] >= 10 ? 'success' : 'danger'; ?>">
                                                                    <?php echo $grade['term1']; ?>
                                                                </span>
                                                            <?php else: ?>
                                                                <span class="text-muted">-</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <?php if (isset($grade['continuous2']) && $grade['continuous2'] !== null): ?>
                                                                <span class="badge bg-<?php echo $grade['continuous2'] >= 10 ? 'success' : 'danger'; ?>">
                                                                    <?php echo $grade['continuous2']; ?>
                                                                </span>
                                                            <?php else: ?>
                                                                <span class="text-muted">-</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <?php if (isset($grade['term2']) && $grade['term2'] !== null): ?>
                                                                <span class="badge bg-<?php echo $grade['term2'] >= 10 ? 'success' : 'danger'; ?>">
                                                                    <?php echo $grade['term2']; ?>
                                                                </span>
                                                            <?php else: ?>
                                                                <span class="text-muted">-</span>
                                                            <?php endif; ?>
                                                        </td>
                                                    <?php endif; ?>
                                                    <td>
                                                        <small class="text-muted">
                                                            <?php 
                                                            $date = $grade['updated_at'] ?? $grade['created_at'] ?? '---';
                                                            echo $date !== '---' ? date('Y/m/d H:i', strtotime($date)) : '---'; 
                                                            ?>
                                                        </small>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        لطفاً یک درس را انتخاب کنید.
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    const GradeApp = {
        init() {
            this.bindEvents();
        },
        
        bindEvents() {
            const confirmCheckbox = document.getElementById('confirm_grades');
            const submitButton = document.querySelector('button[type="submit"]');
            
            if (confirmCheckbox && submitButton) {
                submitButton.disabled = true;
                
                confirmCheckbox.addEventListener('change', function() {
                    submitButton.disabled = !this.checked;
                });
            }
            
            // نمایش بخش مربوط به نوع درس
            const courseType = '<?php echo $data['course_type']; ?>';
            if (courseType === 'poodmani') {
                document.querySelector('.poodmani-section').style.display = 'block';
            } else {
                document.querySelector('.non-poodmani-section').style.display = 'block';
            }
        },
        
        // 🔥 تابع برای بارگذاری کلاس‌ها
        loadClasses(courseId) {
            const classSelection = document.getElementById('classSelection');
            const classSelect = document.getElementById('classSelect');
            
            if (!courseId) {
                classSelection.style.display = 'none';
                return;
            }
            
            // پیدا کردن کلاس‌های مربوط به این درس از لیست موجود
            let html = '<option value="">همه کلاس‌ها</option>';
            const options = document.querySelectorAll('#courseSelect option');
            
            options.forEach(option => {
                const classId = option.getAttribute('data-class-id');
                const className = option.getAttribute('data-class-name');
                
                if (classId && className && option.value === courseId) {
                    html += `<option value="${classId}">${className}</option>`;
                }
            });
            
            classSelect.innerHTML = html;
            classSelection.style.display = 'block';
        }
    };

    // راه‌اندازی برنامه
    document.addEventListener('DOMContentLoaded', function() {
        GradeApp.init();
    });
    </script>
</body>
</html>