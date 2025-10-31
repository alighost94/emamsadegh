<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تخصیص درس به معلم - هنرستان امام صادق</title>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v30.1.0/dist/font-face.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { font-family: 'Vazir', sans-serif; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include 'app/views/partials/sidebar.php'; ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">تخصیص درس به معلم</h1>
                </div>

                <?php if (isset($data['success'])): ?>
                    <div class="alert alert-success"><?php echo $data['success']; ?></div>
                <?php endif; ?>
                <?php if (isset($data['error'])): ?>
                    <div class="alert alert-danger"><?php echo $data['error']; ?></div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>تخصیص درس جدید</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" id="assignForm">
                                    <div class="mb-3">
                                        <label class="form-label">معلم</label>
                                        <select name="teacher_id" class="form-select" required 
                                                onchange="loadTeacherCourses(this.value); loadClasses()">
                                            <option value="">انتخاب معلم</option>
                                            <?php foreach ($data['teachers'] as $teacher): ?>
                                                <option value="<?php echo $teacher['id']; ?>">
                                                    <?php echo $teacher['first_name'] . ' ' . $teacher['last_name']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">درس</label>
                                        <select name="course_id" class="form-select" required 
                                                onchange="loadClasses()">
                                            <option value="">انتخاب درس</option>
                                            <?php foreach ($data['courses'] as $course): ?>
                                                <option value="<?php echo $course['id']; ?>" 
                                                        data-major="<?php echo $course['major_id']; ?>"
                                                        data-grade="<?php echo $course['grade_id']; ?>">
                                                    <?php echo $course['course_code'] . ' - ' . $course['name'] . ' (' . $course['major_name'] . ' - ' . $course['grade_name'] . ')'; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <!-- 🔥 بخش جدید: انتخاب کلاس -->
                                    <div class="mb-3">
                                        <label class="form-label">کلاس (اختیاری)</label>
                                        <select name="class_id" class="form-select" id="classSelect">
                                            <option value="">همه کلاس‌های این درس</option>
                                            <!-- کلاس‌ها با AJAX پر می‌شوند -->
                                        </select>
                                        <div class="form-text">
                                            <small class="text-muted">
                                                اگر کلاس انتخاب نشود، معلم به همه کلاس‌های این درس تخصیص می‌یابد
                                            </small>
                                        </div>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">تخصیص درس</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>دروس تخصیص داده شده</h5>
                            </div>
                            <div class="card-body">
                                <div id="teacherCourses">
                                    <p class="text-muted">لطفاً یک معلم انتخاب کنید</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
    function loadTeacherCourses(teacherId) {
        if (!teacherId) {
            document.getElementById('teacherCourses').innerHTML = '<p class="text-muted">لطفاً یک معلم انتخاب کنید</p>';
            return;
        }
        
        fetch('<?php echo BASE_URL; ?>admin/getTeacherCourses/' + teacherId)
            .then(response => response.json())
            .then(data => {
                let html = '';
                if (data.length > 0) {
                    html = '<div class="table-responsive"><table class="table table-sm"><thead><tr><th>کد درس</th><th>نام درس</th><th>رشته</th><th>پایه</th><th>کلاس</th></tr></thead><tbody>';
                    data.forEach(course => {
                        html += `<tr>
                            <td>${course.course_code}</td>
                            <td>${course.course_name}</td>
                            <td>${course.major_name}</td>
                            <td>${course.grade_name}</td>
                            <td>${course.class_name || 'همه کلاس‌ها'}</td>
                        </tr>`;
                    });
                    html += '</tbody></table></div>';
                } else {
                    html = '<p class="text-muted">هیچ درسی به این معلم تخصیص داده نشده است</p>';
                }
                document.getElementById('teacherCourses').innerHTML = html;
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('teacherCourses').innerHTML = '<p class="text-danger">خطا در بارگذاری اطلاعات</p>';
            });
    }

    // 🔥 تابع جدید برای بارگذاری کلاس‌ها
    function loadClasses() {
        const courseSelect = document.querySelector('select[name="course_id"]');
        const classSelect = document.getElementById('classSelect');
        const teacherSelect = document.querySelector('select[name="teacher_id"]');
        
        const courseId = courseSelect.value;
        const teacherId = teacherSelect.value;
        
        if (!courseId || !teacherId) {
            classSelect.innerHTML = '<option value="">همه کلاس‌های این درس</option>';
            return;
        }
        
        const selectedOption = courseSelect.options[courseSelect.selectedIndex];
        const majorId = selectedOption.getAttribute('data-major');
        const gradeId = selectedOption.getAttribute('data-grade');
        
        // بارگذاری کلاس‌های مربوط به این رشته و پایه
        fetch(`<?php echo BASE_URL; ?>admin/getClassesByMajorGrade/${majorId}/${gradeId}`)
            .then(response => response.json())
            .then(classes => {
                let html = '<option value="">همه کلاس‌های این درس</option>';
                
                classes.forEach(cls => {
                    html += `<option value="${cls.id}">${cls.name}</option>`;
                });
                
                classSelect.innerHTML = html;
            })
            .catch(error => {
                console.error('Error:', error);
                classSelect.innerHTML = '<option value="">خطا در بارگذاری کلاس‌ها</option>';
            });
    }

    // 🔥 اعتبارسنجی فرم
    document.getElementById('assignForm').addEventListener('submit', function(e) {
        const teacherId = document.querySelector('select[name="teacher_id"]').value;
        const courseId = document.querySelector('select[name="course_id"]').value;
        const classId = document.querySelector('select[name="class_id"]').value;
        
        if (!teacherId || !courseId) {
            e.preventDefault();
            alert('لطفاً معلم و درس را انتخاب کنید');
            return;
        }
        
        // بررسی تکراری نبودن تخصیص
        const formData = new FormData();
        formData.append('teacher_id', teacherId);
        formData.append('course_id', courseId);
        formData.append('class_id', classId);
        
        fetch('<?php echo BASE_URL; ?>admin/checkAssignment', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.exists) {
                e.preventDefault();
                alert('این تخصیص قبلاً انجام شده است');
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>