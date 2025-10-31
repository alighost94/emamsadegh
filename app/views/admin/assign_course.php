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
        .search-dropdown { position: relative; }
        .search-results { 
            position: absolute; 
            top: 100%; 
            left: 0; 
            right: 0; 
            background: white; 
            border: 1px solid #ddd; 
            border-top: none; 
            max-height: 200px; 
            overflow-y: auto; 
            z-index: 1000; 
            display: none;
        }
        .search-result-item { 
            padding: 8px 12px; 
            cursor: pointer; 
            border-bottom: 1px solid #eee; 
        }
        .search-result-item:hover { 
            background-color: #f8f9fa; 
        }
        .selected-teacher { 
            background-color: #e9f7ef; 
            border: 1px solid #28a745; 
            border-radius: 5px; 
            padding: 10px; 
            margin-bottom: 15px; 
        }
        .course-table th { background-color: #f8f9fa; }
        .selected-count { 
            background-color: #007bff; 
            color: white; 
            padding: 2px 8px; 
            border-radius: 12px; 
            font-size: 12px; 
            margin-left: 5px;
        }
        .course-info {
            font-size: 0.9rem;
            color: #6c757d;
        }
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

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
                <?php endif; ?>
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
                <?php endif; ?>

                <div class="row">
                    <!-- بخش انتخاب معلم -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5>انتخاب معلم</h5>
                            </div>
                            <div class="card-body">
                                <div class="search-dropdown mb-3">
                                    <label class="form-label">جستجوی معلم</label>
                                    <input type="text" class="form-control" id="teacherSearch" 
                                           placeholder="جستجو بر اساس نام، موبایل یا کد ملی..." 
                                           autocomplete="off">
                                    <div class="search-results" id="teacherResults"></div>
                                </div>
                                
                                <div id="selectedTeacherContainer" style="display: none;">
                                    <div class="selected-teacher">
                                        <strong>معلم انتخاب شده:</strong>
                                        <span id="selectedTeacherName"></span>
                                        <input type="hidden" id="selectedTeacherId">
                                        <button type="button" class="btn btn-sm btn-outline-danger float-start" 
                                                onclick="clearTeacherSelection()">
                                            <i class="bi bi-x"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- بخش دروس انتخاب شده -->
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5>دروس انتخاب شده <span id="selectedCount" class="selected-count">0</span></h5>
                            </div>
                            <div class="card-body">
                                <div id="selectedCoursesList" style="max-height: 300px; overflow-y: auto;">
                                    <p class="text-muted">هیچ درسی انتخاب نشده است</p>
                                </div>
                                <div class="mt-3">
                                    <form id="assignMultipleForm" method="POST" action="<?php echo BASE_URL; ?>admin/assignMultipleCourses">
                                        <input type="hidden" id="formTeacherId" name="teacher_id">
                                        <input type="hidden" id="formCoursesData" name="courses_data">
                                        <button type="submit" class="btn btn-success w-100" 
                                                id="assignMultipleBtn" 
                                                disabled>
                                            تخصیص دروس انتخاب شده
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- بخش جستجو و انتخاب دروس -->
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5>جستجو و انتخاب دروس</h5>
                            </div>
                            <div class="card-body">
                                <!-- فیلترهای جستجو -->
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" id="courseSearch" 
                                               placeholder="جستجوی درس...">
                                    </div>
                                    <div class="col-md-3">
                                        <select class="form-select" id="majorFilter">
                                            <option value="">همه رشته‌ها</option>
                                            <?php foreach ($data['majors'] as $major): ?>
                                                <option value="<?php echo $major['id']; ?>"><?php echo $major['name']; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select class="form-select" id="gradeFilter">
                                            <option value="">همه پایه‌ها</option>
                                            <?php foreach ($data['grades'] as $grade): ?>
                                                <option value="<?php echo $grade['id']; ?>"><?php echo $grade['name']; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-outline-primary w-100" onclick="searchCourses()">
                                            <i class="bi bi-search"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- جدول دروس -->
                                <div class="table-responsive">
                                    <table class="table table-hover course-table">
                                        <thead>
                                            <tr>
                                                <th width="50">
                                                    <input type="checkbox" id="selectAllCourses" onchange="toggleAllCourses(this)">
                                                </th>
                                                <th>کد درس</th>
                                                <th>نام درس</th>
                                                <th>رشته</th>
                                                <th>پایه</th>
                                                <th>واحد</th>
                                                <th>نوع</th>
                                            </tr>
                                        </thead>
                                        <tbody id="coursesTableBody">
                                            <tr>
                                                <td colspan="7" class="text-center text-muted">
                                                    برای مشاهده دروس، جستجو کنید
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <div>
                                        <button type="button" class="btn btn-outline-primary" onclick="addSelectedCourses()">
                                            <i class="bi bi-plus-circle"></i> افزودن به لیست انتخاب
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- بخش دروس تخصیص داده شده -->
                <div class="row mt-4">
                    <div class="col-12">
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

    <!-- Modal برای انتخاب کلاس -->
    <div class="modal fade" id="classModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">انتخاب کلاس</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>برای درس <strong id="modalCourseName"></strong> کلاس انتخاب کنید:</p>
                    <select class="form-select" id="modalClassSelect">
                        <option value="">همه کلاس‌های این درس</option>
                    </select>
                    <div class="form-text mt-2">
                        اگر کلاس انتخاب نشود، معلم به همه کلاس‌های این درس تخصیص می‌یابد
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">انصراف</button>
                    <button type="button" class="btn btn-primary" onclick="confirmCourseSelection()">تأیید</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let selectedCourses = new Map();
        let currentTeacherId = null;
        let currentCourseForModal = null;
        let allCourses = [];

        // جستجوی معلمان
        document.getElementById('teacherSearch').addEventListener('input', function(e) {
            const searchTerm = e.target.value.trim();
            if (searchTerm.length < 2) {
                document.getElementById('teacherResults').style.display = 'none';
                return;
            }

            fetch(`<?php echo BASE_URL; ?>admin/searchTeachers?search=${encodeURIComponent(searchTerm)}`)
                .then(response => response.json())
                .then(teachers => {
                    const resultsContainer = document.getElementById('teacherResults');
                    resultsContainer.innerHTML = '';
                    
                    if (teachers.length > 0) {
                        teachers.forEach(teacher => {
                            const div = document.createElement('div');
                            div.className = 'search-result-item';
                            div.innerHTML = `
                                ${teacher.first_name} ${teacher.last_name} 
                                <small class="text-muted">- ${teacher.mobile}</small>
                            `;
                            div.onclick = () => selectTeacher(teacher);
                            resultsContainer.appendChild(div);
                        });
                        resultsContainer.style.display = 'block';
                    } else {
                        resultsContainer.innerHTML = '<div class="search-result-item text-muted">معلمی یافت نشد</div>';
                        resultsContainer.style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        });

        // انتخاب معلم
        function selectTeacher(teacher) {
            document.getElementById('selectedTeacherId').value = teacher.id;
            document.getElementById('selectedTeacherName').textContent = 
                `${teacher.first_name} ${teacher.last_name} - ${teacher.mobile}`;
            document.getElementById('selectedTeacherContainer').style.display = 'block';
            document.getElementById('teacherResults').style.display = 'none';
            document.getElementById('teacherSearch').value = '';
            
            currentTeacherId = teacher.id;
            document.getElementById('formTeacherId').value = teacher.id;
            loadTeacherCourses(teacher.id);
            updateAssignButton();
        }

        // پاک کردن انتخاب معلم
        function clearTeacherSelection() {
            document.getElementById('selectedTeacherContainer').style.display = 'none';
            document.getElementById('selectedTeacherId').value = '';
            document.getElementById('formTeacherId').value = '';
            currentTeacherId = null;
            selectedCourses.clear();
            updateSelectedCoursesList();
            updateAssignButton();
            document.getElementById('teacherCourses').innerHTML = '<p class="text-muted">لطفاً یک معلم انتخاب کنید</p>';
        }

        // جستجوی دروس
        function searchCourses() {
            const search = document.getElementById('courseSearch').value;
            const majorId = document.getElementById('majorFilter').value;
            const gradeId = document.getElementById('gradeFilter').value;

            const params = new URLSearchParams();
            if (search) params.append('search', search);
            if (majorId) params.append('major_id', majorId);
            if (gradeId) params.append('grade_id', gradeId);

            fetch(`<?php echo BASE_URL; ?>admin/searchCourses?${params}`)
                .then(response => response.json())
                .then(courses => {
                    allCourses = courses;
                    const tbody = document.getElementById('coursesTableBody');
                    tbody.innerHTML = '';

                    if (courses.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">هیچ درسی یافت نشد</td></tr>';
                        return;
                    }

                    courses.forEach(course => {
                        const isSelected = selectedCourses.has(course.id.toString());
                        const row = document.createElement('tr');
                        
                        row.innerHTML = `
                            <td>
                                <input type="checkbox" class="course-checkbox" 
                                       value="${course.id}" 
                                       ${isSelected ? 'checked' : ''}
                                       onchange="toggleCourseSelection(this, '${course.id}')">
                            </td>
                            <td>${course.course_code}</td>
                            <td>
                                <div>${course.name}</div>
                                <small class="course-info">
                                    ${course.major_name} - ${course.grade_name}
                                </small>
                            </td>
                            <td>${course.major_name}</td>
                            <td>${course.grade_name}</td>
                            <td>${course.unit}</td>
                            <td>
                                <span class="badge ${course.course_type === 'poodmani' ? 'bg-success' : 'bg-info'}">
                                    ${course.course_type === 'poodmani' ? 'پودمانی' : 'غیر پودمانی'}
                                </span>
                            </td>
                        `;
                        tbody.appendChild(row);
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    const tbody = document.getElementById('coursesTableBody');
                    tbody.innerHTML = '<tr><td colspan="7" class="text-center text-danger">خطا در بارگذاری دروس</td></tr>';
                });
        }

        // انتخاب/لغو انتخاب درس
        function toggleCourseSelection(checkbox, courseId) {
            if (checkbox.checked && !selectedCourses.has(courseId)) {
                // نمایش مودال برای انتخاب کلاس
                showClassModal(courseId);
            } else if (!checkbox.checked) {
                selectedCourses.delete(courseId);
                updateSelectedCoursesList();
                updateAssignButton();
            }
        }

        // نمایش مودال انتخاب کلاس
        function showClassModal(courseId) {
            currentCourseForModal = courseId;
            
            // پیدا کردن اطلاعات درس از لیست
            const course = allCourses.find(c => c.id.toString() === courseId.toString());
            if (!course) {
                alert('خطا: اطلاعات درس یافت نشد');
                return;
            }
            
            document.getElementById('modalCourseName').textContent = course.name;
            
            // بارگذاری کلاس‌های مربوط به این رشته و پایه
            fetch(`<?php echo BASE_URL; ?>admin/getClassesByMajorGrade/${course.major_id}/${course.grade_id}`)
                .then(response => response.json())
                .then(classes => {
                    const select = document.getElementById('modalClassSelect');
                    select.innerHTML = '<option value="">همه کلاس‌های این درس</option>';
                    
                    classes.forEach(cls => {
                        const option = document.createElement('option');
                        option.value = cls.id;
                        option.textContent = cls.name;
                        select.appendChild(option);
                    });
                    
                    // نمایش مودال
                    const modal = new bootstrap.Modal(document.getElementById('classModal'));
                    modal.show();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('خطا در بارگذاری کلاس‌ها');
                });
        }

        // تأیید انتخاب درس با کلاس
        function confirmCourseSelection() {
            const classId = document.getElementById('modalClassSelect').value;
            const courseId = currentCourseForModal;
            
            // پیدا کردن اطلاعات درس
            const course = allCourses.find(c => c.id.toString() === courseId.toString());
            if (!course) {
                alert('خطا: اطلاعات درس یافت نشد');
                return;
            }
            
            // ذخیره اطلاعات درس انتخاب شده
            selectedCourses.set(courseId, {
                course_id: courseId,
                class_id: classId === '' ? null : classId,
                course_info: course
            });
            
            updateSelectedCoursesList();
            updateAssignButton();
            
            // بستن مودال
            const modal = bootstrap.Modal.getInstance(document.getElementById('classModal'));
            modal.hide();
        }

        // انتخاب همه دروس
        function toggleAllCourses(checkbox) {
            const courseCheckboxes = document.querySelectorAll('.course-checkbox');
            courseCheckboxes.forEach(cb => {
                if (checkbox.checked && !cb.checked) {
                    // برای دروس جدید مودال نشان داده نمی‌شود - کلاس null در نظر گرفته می‌شود
                    const course = allCourses.find(c => c.id.toString() === cb.value.toString());
                    if (course && !selectedCourses.has(cb.value)) {
                        selectedCourses.set(cb.value, {
                            course_id: cb.value,
                            class_id: null,
                            course_info: course
                        });
                    }
                    cb.checked = true;
                } else if (!checkbox.checked && cb.checked) {
                    cb.checked = false;
                    selectedCourses.delete(cb.value);
                }
            });
            
            updateSelectedCoursesList();
            updateAssignButton();
        }

        // افزودن دروس انتخاب شده از جدول
        function addSelectedCourses() {
            const courseCheckboxes = document.querySelectorAll('.course-checkbox:checked');
            courseCheckboxes.forEach(cb => {
                const courseId = cb.value;
                if (!selectedCourses.has(courseId)) {
                    const course = allCourses.find(c => c.id.toString() === courseId.toString());
                    if (course) {
                        selectedCourses.set(courseId, {
                            course_id: courseId,
                            class_id: null,
                            course_info: course
                        });
                    }
                }
            });
            
            updateSelectedCoursesList();
            updateAssignButton();
        }

        // آپدیت لیست دروس انتخاب شده
        function updateSelectedCoursesList() {
            const container = document.getElementById('selectedCoursesList');
            const countElement = document.getElementById('selectedCount');
            
            countElement.textContent = selectedCourses.size;
            
            if (selectedCourses.size === 0) {
                container.innerHTML = '<p class="text-muted">هیچ درسی انتخاب نشده است</p>';
                document.getElementById('formCoursesData').value = '';
                return;
            }
            
            let html = '<div class="list-group">';
            selectedCourses.forEach((courseData, courseId) => {
                const course = courseData.course_info;
                const className = courseData.class_id ? 
                    (document.querySelector(`#modalClassSelect option[value="${courseData.class_id}"]`)?.textContent || 'نامشخص') : 
                    'همه کلاس‌ها';
                
                html += `
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">${course.course_code}</small>
                            <div>${course.name}</div>
                            <small class="text-muted">
                                ${course.major_name} - ${course.grade_name} | 
                                کلاس: ${className}
                            </small>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                onclick="removeSelectedCourse('${courseId}')">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                `;
            });
            html += '</div>';
            
            container.innerHTML = html;
            
            // آپدیت داده‌های فرم
            const coursesArray = Array.from(selectedCourses.values());
            document.getElementById('formCoursesData').value = JSON.stringify(coursesArray);
        }

        // حذف درس از لیست انتخاب شده
        function removeSelectedCourse(courseId) {
            selectedCourses.delete(courseId);
            
            // آپدیت چک‌باکس در جدول
            const checkbox = document.querySelector(`.course-checkbox[value="${courseId}"]`);
            if (checkbox) {
                checkbox.checked = false;
            }
            
            updateSelectedCoursesList();
            updateAssignButton();
        }

        // آپدیت وضعیت دکمه تخصیص
        function updateAssignButton() {
            const btn = document.getElementById('assignMultipleBtn');
            btn.disabled = !currentTeacherId || selectedCourses.size === 0;
        }

        // بارگذاری دروس تخصیص داده شده به معلم
        function loadTeacherCourses(teacherId) {
            fetch('<?php echo BASE_URL; ?>admin/getTeacherCourses/' + teacherId)
                .then(response => response.json())
                .then(data => {
                    let html = '';
                    if (data.length > 0) {
                        html = '<div class="table-responsive"><table class="table table-sm"><thead><tr><th>کد درس</th><th>نام درس</th><th>رشته</th><th>پایه</th><th>کلاس</th><th>نوع</th></tr></thead><tbody>';
                        data.forEach(course => {
                            html += `<tr>
                                <td>${course.course_code}</td>
                                <td>${course.course_name}</td>
                                <td>${course.major_name}</td>
                                <td>${course.grade_name}</td>
                                <td>${course.class_name || 'همه کلاس‌ها'}</td>
                                <td>
                                    <span class="badge ${course.course_type === 'poodmani' ? 'bg-success' : 'bg-info'}">
                                        ${course.course_type === 'poodmani' ? 'پودمانی' : 'غیر پودمانی'}
                                    </span>
                                </td>
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

        // مدیریت ارسال فرم
        document.getElementById('assignMultipleForm').addEventListener('submit', function(e) {
            if (!currentTeacherId || selectedCourses.size === 0) {
                e.preventDefault();
                alert('لطفاً معلم و دروس را انتخاب کنید');
                return;
            }
            
            // نمایش تأیید نهایی
            if (!confirm(`آیا از تخصیص ${selectedCourses.size} درس به این معلم اطمینان دارید؟`)) {
                e.preventDefault();
            }
        });

        // بارگذاری اولیه دروس
        document.addEventListener('DOMContentLoaded', function() {
            searchCourses();
        });

        // بستن dropdown هنگام کلیک خارج
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.search-dropdown')) {
                document.getElementById('teacherResults').style.display = 'none';
            }
        });
    </script>
</body>
</html>