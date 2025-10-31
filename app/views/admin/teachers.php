<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مدیریت معلمان - هنرستان امام صادق</title>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v30.1.0/dist/font-face.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { font-family: 'Vazir', sans-serif; }
        .search-highlight {
            background-color: #fff3cd;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include 'app/views/partials/sidebar.php'; ?>
            
            <main class="col-md-12 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">مدیریت معلمان</h1>
                </div>

                <?php if (isset($data['success'])): ?>
                    <div class="alert alert-success"><?php echo $data['success']; ?></div>
                <?php endif; ?>
                <?php if (isset($data['error'])): ?>
                    <div class="alert alert-danger"><?php echo $data['error']; ?></div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-12">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5>جستجوی معلمان</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                                            <input type="text" id="searchInput" class="form-control" placeholder="جستجو بر اساس نام، نام خانوادگی، تخصص، موبایل یا کد ملی...">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <button id="clearSearch" class="btn btn-outline-secondary w-100">پاک کردن جستجو</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5>لیست معلمان</h5>
                                <span id="teacherCount" class="badge bg-primary"><?php echo count($data['teachers']); ?> معلم</span>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>نام معلم</th>
                                                <th>کد ملی</th>
                                                <th>تخصص‌ها</th>
                                                <th>تاریخ استخدام</th>
                                                <th>موبایل</th>
                                                <th>عملیات</th>
                                            </tr>
                                        </thead>
                                        <tbody id="teachersTable">
                                            <?php foreach ($data['teachers'] as $teacher): ?>
                                                <tr>
                                                    <td><?php echo $teacher['first_name'] . ' ' . $teacher['last_name']; ?></td>
                                                    <td><?php echo $teacher['national_code'] ?? 'ثبت نشده'; ?></td>
                                                    <td><?php echo $teacher['expertise'] ?? 'ثبت نشده'; ?></td>
                                                    <td><?php echo $teacher['employment_date'] ?? 'ثبت نشده'; ?></td>
                                                    <td><?php echo $teacher['mobile']; ?></td>
                                                    <td>
                                                        <a href="<?php echo BASE_URL; ?>admin/assignTeacherCourse" 
                                                           class="btn btn-sm btn-outline-primary">
                                                            تخصیص درس
                                                        </a>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const clearSearch = document.getElementById('clearSearch');
            const teachersTable = document.getElementById('teachersTable');
            const teacherCount = document.getElementById('teacherCount');
            
            const originalTeachers = Array.from(teachersTable.querySelectorAll('tr'));
            
            function filterTeachers() {
                const searchTerm = searchInput.value.toLowerCase();
                
                let visibleCount = 0;
                
                originalTeachers.forEach(row => {
                    const cells = row.querySelectorAll('td');
                    const teacherName = cells[0].textContent.toLowerCase();
                    const nationalCode = cells[1].textContent.toLowerCase();
                    const expertise = cells[2].textContent.toLowerCase();
                    const employmentDate = cells[3].textContent.toLowerCase();
                    const mobile = cells[4].textContent.toLowerCase();
                    
                    // Highlight matching text
                    cells.forEach(cell => {
                        cell.innerHTML = cell.textContent;
                    });
                    
                    if (searchTerm) {
                        cells.forEach(cell => {
                            const text = cell.textContent;
                            const regex = new RegExp(`(${searchTerm})`, 'gi');
                            cell.innerHTML = text.replace(regex, '<span class="search-highlight">$1</span>');
                        });
                    }
                    
                    // Check if row matches search criteria
                    const matchesSearch = !searchTerm || 
                        teacherName.includes(searchTerm) || 
                        nationalCode.includes(searchTerm) || 
                        expertise.includes(searchTerm) || 
                        employmentDate.includes(searchTerm) || 
                        mobile.includes(searchTerm);
                    
                    // Show/hide row based on search
                    if (matchesSearch) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });
                
                // Update teacher count
                teacherCount.textContent = `${visibleCount} معلم`;
            }
            
            // Add event listeners
            searchInput.addEventListener('input', filterTeachers);
            
            clearSearch.addEventListener('click', function() {
                searchInput.value = '';
                filterTeachers();
            });
        });
    </script>
</body>
</html>