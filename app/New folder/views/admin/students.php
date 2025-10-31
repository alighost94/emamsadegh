<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مدیریت دانش‌آموزان - هنرستان امام صادق</title>
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
                    <h1 class="h2">مدیریت دانش‌آموزان</h1>
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
                                <h5>جستجوی دانش‌آموزان</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                                            <input type="text" id="searchInput" class="form-control" placeholder="جستجو بر اساس نام، نام خانوادگی، شماره دانش‌آموزی، موبایل یا کد ملی...">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <select id="classFilter" class="form-select">
                                            <option value="">همه کلاس‌ها</option>
                                            <?php foreach ($data['classes'] as $class): ?>
                                                <option value="<?php echo $class['id']; ?>">
                                                    <?php echo $class['name'] . ' - ' . $class['major_name'] . ' - ' . $class['grade_name']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <button id="clearSearch" class="btn btn-outline-secondary w-100">پاک کردن فیلترها</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5>لیست دانش‌آموزان</h5>
                                <span id="studentCount" class="badge bg-primary"><?php echo count($data['students']); ?> دانش‌آموز</span>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>نام دانش‌آموز</th>
                                                <th>شماره دانش‌آموزی</th>
                                                <th>کلاس</th>
                                                <th>رشته</th>
                                                <th>پایه</th>
                                                <th>موبایل</th>
                                                <th>کد ملی</th>
                                            </tr>
                                        </thead>
                                        <tbody id="studentsTable">
                                            <?php foreach ($data['students'] as $student): ?>
                                                <tr>
                                                    <td><?php echo $student['first_name'] . ' ' . $student['last_name']; ?></td>
                                                    <td><?php echo $student['student_number']; ?></td>
                                                    <td><?php echo $student['class_name'] ?? 'ثبت نشده'; ?></td>
                                                    <td><?php echo $student['major_name'] ?? 'ثبت نشده'; ?></td>
                                                    <td><?php echo $student['grade_name'] ?? 'ثبت نشده'; ?></td>
                                                    <td><?php echo $student['mobile']; ?></td>
                                                    <td><?php echo $student['national_code'] ?? 'ثبت نشده'; ?></td>
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
            const classFilter = document.getElementById('classFilter');
            const clearSearch = document.getElementById('clearSearch');
            const studentsTable = document.getElementById('studentsTable');
            const studentCount = document.getElementById('studentCount');
            
            const originalStudents = Array.from(studentsTable.querySelectorAll('tr'));
            
            function filterStudents() {
                const searchTerm = searchInput.value.toLowerCase();
                const selectedClass = classFilter.value;
                
                let visibleCount = 0;
                
                originalStudents.forEach(row => {
                    const cells = row.querySelectorAll('td');
                    const studentName = cells[0].textContent.toLowerCase();
                    const studentNumber = cells[1].textContent.toLowerCase();
                    const className = cells[2].textContent;
                    const major = cells[3].textContent;
                    const grade = cells[4].textContent;
                    const mobile = cells[5].textContent.toLowerCase();
                    const nationalCode = cells[6].textContent.toLowerCase();
                    
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
                        studentName.includes(searchTerm) || 
                        studentNumber.includes(searchTerm) || 
                        mobile.includes(searchTerm) || 
                        nationalCode.includes(searchTerm);
                    
                    // Check if row matches class filter
                    const matchesClass = !selectedClass || 
                        row.getAttribute('data-class-id') === selectedClass;
                    
                    // Show/hide row based on filters
                    if (matchesSearch && matchesClass) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });
                
                // Update student count
                studentCount.textContent = `${visibleCount} دانش‌آموز`;
            }
            
            // Add event listeners
            searchInput.addEventListener('input', filterStudents);
            classFilter.addEventListener('change', filterStudents);
            
            clearSearch.addEventListener('click', function() {
                searchInput.value = '';
                classFilter.value = '';
                filterStudents();
            });
            
            // Store class IDs in data attributes for filtering
            originalStudents.forEach((row, index) => {
                const classOption = document.querySelector(`#classFilter option:nth-child(${index + 2})`);
                if (classOption) {
                    row.setAttribute('data-class-id', classOption.value);
                }
            });
        });
    </script>
</body>
</html>