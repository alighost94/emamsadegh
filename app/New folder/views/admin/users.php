<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مدیریت کاربران - هنرستان امام صادق</title>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v30.1.0/dist/font-face.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { 
            font-family: 'Vazir', sans-serif; 
        }
        
        .role-specific { 
            display: none; 
        }
        
        .main-content {
            background: #f8f9fa;
            min-height: 100vh;
            margin-right: 0;
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            margin-bottom: 25px;
        }
        
        .card-header {
            background: white;
            border-bottom: 1px solid #eaeaea;
            padding: 20px 25px;
            border-radius: 15px 15px 0 0 !important;
        }
        
        .card-header h5 {
            margin: 0;
            color: #2c3e50;
            font-weight: 600;
        }
        
        .card-body {
            padding: 25px;
        }
        
        .form-control, .form-select {
            border-radius: 10px;
            border: 1px solid #e0e0e0;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.1);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 500;
        }
        
        .table {
            margin-bottom: 0;
        }
        
        .table th {
            border-top: none;
            font-weight: 600;
            color: #2c3e50;
            background: #f8f9fa;
            padding: 15px 12px;
        }
        
        .table td {
            padding: 15px 12px;
            vertical-align: middle;
        }
        
        .badge {
            font-weight: 500;
            padding: 6px 12px;
            border-radius: 8px;
        }
        
        .search-box {
            position: relative;
        }
        
        .search-box .form-control {
            padding-right: 45px;
        }
        
        .search-box i {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }
        
        .filter-badge {
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .filter-badge:hover {
            transform: translateY(-2px);
        }
        
        .role-filter {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .user-name {
            font-weight: 600;
            color: #2c3e50;
        }
        
        .user-info {
            color: #6c757d;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include 'app/views/partials/sidebar.php'; ?>
            
            <main class="col-md-12 px-md-4 main-content">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">مدیریت کاربران</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group">
                            <span class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-people"></i>
                                <?php echo count($data['users']); ?> کاربر
                            </span>
                        </div>
                    </div>
                </div>

                <?php if (isset($data['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i>
                        <?php echo $data['success']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                <?php if (isset($data['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <?php echo $data['error']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="bi bi-person-plus me-2"></i>افزودن کاربر جدید</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" id="userForm">
                                    <div class="mb-3">
                                        <label class="form-label">شماره موبایل</label>
                                        <input type="text" name="mobile" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">کد ملی</label>
                                        <input type="text" name="national_code" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">نام</label>
                                        <input type="text" name="first_name" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">نام خانوادگی</label>
                                        <input type="text" name="last_name" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">نقش</label>
                                        <select name="role_id" class="form-select" required onchange="showRoleSpecificFields(this.value)">
                                            <option value="">انتخاب نقش</option>
                                            <?php foreach ($data['roles'] as $role): ?>
                                                <option value="<?php echo $role['id']; ?>">
                                                    <?php 
                                                    $role_names = [
                                                        'admin' => 'مدیر',
                                                        'student' => 'دانش‌آموز', 
                                                        'teacher' => 'معلم',
                                                        'parent' => 'ولی',
                                                        'assistant' => 'معاون'
                                                    ];
                                                    echo $role_names[$role['name']] ?? $role['name']; 
                                                    ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <!-- فیلدهای خاص دانش‌آموز -->
                                    <div id="studentFields" class="role-specific">
                                        <div class="mb-3">
                                            <label class="form-label">کلاس</label>
                                            <select name="class_id" class="form-select">
                                                <option value="">انتخاب کلاس</option>
                                                <?php foreach ($data['classes'] as $class): ?>
                                                    <option value="<?php echo $class['id']; ?>">
                                                        <?php echo $class['name'] . ' - ' . $class['major_name'] . ' - ' . $class['grade_name']; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">تاریخ تولد</label>
                                            <input type="text" name="birth_date" class="form-control" placeholder="۱۳۸۰/۰۱/۰۱">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">نام پدر</label>
                                            <input type="text" name="father_name" class="form-control">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">آدرس</label>
                                            <textarea name="address" class="form-control" rows="2"></textarea>
                                        </div>
                                    </div>
                                    
                                    <!-- فیلدهای خاص معلم -->
                                    <div id="teacherFields" class="role-specific">
                                        <div class="mb-3">
                                            <label class="form-label">تخصص‌ها</label>
                                            <textarea name="expertise" class="form-control" rows="2" 
                                                      placeholder="مثال: برنامه نویسی, شبکه, پایگاه داده"></textarea>
                                        </div>
                                    </div>
                                    
                                    <!-- فیلدهای خاص معاون -->
                                    <div id="assistantFields" class="role-specific">
                                        <div class="mb-3">
                                            <label class="form-label">پایه تحت مسئولیت</label>
                                            <select name="grade_id" class="form-select">
                                                <?php foreach ($data['grades'] as $grade): ?>
                                                    <option value="<?php echo $grade['id']; ?>"><?php echo $grade['name']; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <!-- فیلدهای خاص اولیا -->
                                    <div id="parentFields" class="role-specific">
                                        <div class="mb-3">
                                            <label class="form-label">دانش‌آموز مرتبط</label>
                                            <select name="student_id" class="form-select">
                                                <option value="">انتخاب دانش‌آموز</option>
                                                <?php foreach ($data['students'] as $student): ?>
                                                    <option value="<?php echo $student['id']; ?>">
                                                        <?php echo $student['first_name'] . ' ' . $student['last_name'] . ' (' . $student['student_number'] . ')'; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">نسبت</label>
                                            <select name="relation_type" class="form-select">
                                                <option value="father">پدر</option>
                                                <option value="mother">مادر</option>
                                                <option value="guardian">سرپرست</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-person-plus me-2"></i>
                                        ایجاد کاربر
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-8">
                        <!-- فیلترها و جستجو -->
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="search-box">
                                            <input type="text" class="form-control" placeholder="جستجو در کاربران..." id="searchInput">
                                            <i class="bi bi-search"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="role-filter">
                                            <label class="form-label mb-2">فیلتر بر اساس نقش:</label>
                                            <div class="d-flex flex-wrap gap-2">
                                                <span class="badge bg-primary filter-badge active" data-role="all">همه</span>
                                                <span class="badge bg-success filter-badge" data-role="student">دانش‌آموز</span>
                                                <span class="badge bg-info filter-badge" data-role="teacher">معلم</span>
                                                <span class="badge bg-warning filter-badge" data-role="parent">ولی</span>
                                                <span class="badge bg-secondary filter-badge" data-role="assistant">معاون</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="bi bi-people me-2"></i>لیست کاربران</h5>
                                <span class="badge bg-primary" id="userCount"><?php echo count($data['users']); ?> کاربر</span>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>نام کامل</th>
                                                <th>موبایل</th>
                                                <th>کد ملی</th>
                                                <th>نقش</th>
                                                <th>اطلاعات اضافی</th>
                                                <th>وضعیت</th>
                                                <th>تاریخ ایجاد</th>
                                            </tr>
                                        </thead>
                                        <tbody id="usersTable">
                                            <?php 
                                            $jalaliDate = new JalaliDate();
                                            $role_names = [
                                                'admin' => 'مدیر',
                                                'student' => 'دانش‌آموز', 
                                                'teacher' => 'معلم',
                                                'parent' => 'ولی',
                                                'assistant' => 'معاون'
                                            ];
                                            
                                            foreach ($data['users'] as $user): 
                                                $created_at = $jalaliDate->gregorianToJalali($user['created_at'], 'Y/m/d');
                                            ?>
                                                <tr data-role="<?php echo $user['role_name']; ?>">
                                                    <td>
                                                        <div class="user-name">
                                                            <?php echo $user['first_name'] . ' ' . $user['last_name']; ?>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <?php echo $user['mobile']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $user['national_code']; ?>
                                                    </td>
                                                    <td>
                                                        <span class="badge 
                                                            <?php 
                                                                switch($user['role_name']) {
                                                                    case 'admin': echo 'bg-danger'; break;
                                                                    case 'student': echo 'bg-primary'; break;
                                                                    case 'teacher': echo 'bg-success'; break;
                                                                    case 'parent': echo 'bg-warning'; break;
                                                                    case 'assistant': echo 'bg-info'; break;
                                                                    default: echo 'bg-secondary';
                                                                }
                                                            ?>">
                                                            <?php echo $role_names[$user['role_name']] ?? $user['role_name']; ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <?php if ($user['role_name'] == 'student' && isset($user['class_name'])): ?>
                                                            <small>
                                                                کلاس: <?php echo $user['class_name']; ?><br>
                                                                <?php if (isset($user['major_name'])): ?>
                                                                    رشته: <?php echo $user['major_name']; ?>
                                                                <?php endif; ?>
                                                            </small>
                                                        <?php elseif ($user['role_name'] == 'student'): ?>
                                                            <small class="text-muted">کلاس تعیین نشده</small>
                                                        <?php elseif ($user['role_name'] == 'teacher' && isset($user['expertise'])): ?>
                                                            <small>تخصص: <?php echo $user['expertise']; ?></small>
                                                        <?php else: ?>
                                                            <small class="text-muted">-</small>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <span class="badge <?php echo $user['is_active'] ? 'bg-success' : 'bg-secondary'; ?>">
                                                            <?php echo $user['is_active'] ? 'فعال' : 'غیرفعال'; ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <small class="text-muted"><?php echo $created_at; ?></small>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
    function showRoleSpecificFields(roleId) {
        // مخفی کردن همه فیلدهای خاص نقش
        document.querySelectorAll('.role-specific').forEach(function(el) {
            el.style.display = 'none';
        });
        
        // نمایش فیلدهای مربوط به نقش انتخاب شده
        switch(roleId) {
            case '2': // دانش‌آموز
                document.getElementById('studentFields').style.display = 'block';
                break;
            case '3': // معلم
                document.getElementById('teacherFields').style.display = 'block';
                break;
            case '5': // معاون
                document.getElementById('assistantFields').style.display = 'block';
                break;
            case '4': // اولیا
                document.getElementById('parentFields').style.display = 'block';
                break;
        }
    }

    // فیلتر کردن و جستجو
    document.addEventListener('DOMContentLoaded', function() {
        const roleSelect = document.querySelector('select[name="role_id"]');
        if (roleSelect.value) {
            showRoleSpecificFields(roleSelect.value);
        }

        const searchInput = document.getElementById('searchInput');
        const filterBadges = document.querySelectorAll('.filter-badge');
        const usersTable = document.getElementById('usersTable');
        const userCount = document.getElementById('userCount');
        const originalUsers = Array.from(usersTable.querySelectorAll('tr'));

        function filterUsers() {
            const searchTerm = searchInput.value.toLowerCase();
            const activeFilter = document.querySelector('.filter-badge.active')?.dataset.role || 'all';
            
            let visibleCount = 0;

            originalUsers.forEach(row => {
                const name = row.cells[0].textContent.toLowerCase();
                const mobile = row.cells[1].textContent.toLowerCase();
                const nationalCode = row.cells[2].textContent.toLowerCase();
                const role = row.dataset.role;

                const matchesSearch = !searchTerm || 
                    name.includes(searchTerm) || 
                    mobile.includes(searchTerm) || 
                    nationalCode.includes(searchTerm);

                const matchesFilter = activeFilter === 'all' || role === activeFilter;

                if (matchesSearch && matchesFilter) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            userCount.textContent = `${visibleCount} کاربر`;
        }

        // رویداد جستجو
        searchInput.addEventListener('input', filterUsers);

        // رویداد فیلتر نقش
        filterBadges.forEach(badge => {
            badge.addEventListener('click', function() {
                filterBadges.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                filterUsers();
            });
        });
    });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>