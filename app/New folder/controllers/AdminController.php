<?php
class AdminController extends Controller {
    
    public function __construct() {
        parent::__construct();
        if (!$this->isLoggedIn() || !$this->isAdmin()) {
            $this->redirect('auth/login');
        }
        
        // بارگذاری مدل‌ها با مدیریت خطا
        try {
            $this->userModel = $this->model('User');
            $this->majorModel = $this->model('Major');
            $this->gradeModel = $this->model('Grade');
            $this->courseModel = $this->model('Course');
            $this->classModel = $this->model('ClassModel');
            $this->studentModel = $this->model('Student');
            $this->teacherModel = $this->model('Teacher');
            $this->parentModel = $this->model('ParentModel');
            $this->assistantModel = $this->model('Assistant');
            $this->staffRecordModel = $this->model('StaffRecord');
            $this->staffScoreModel = $this->model('StaffScore');
            $this->teacherCourseModel = $this->model('TeacherCourse'); // اضافه شد

        } catch (Exception $e) {
            die('خطا در بارگذاری مدل‌ها: ' . $e->getMessage());
        }
    }
    
    // متد کمکی برای داده‌های پایه
    private function getBaseData() {
        return [
            'user_name' => $_SESSION['first_name'] . ' ' . $_SESSION['last_name'],
            'role' => $_SESSION['role'] ?? 'Admin' 
        ];
    }
    
    // اضافه کردن متد index
    public function index() {
        $this->redirect('admin/users');
    }
    
    
    public function majors() {
        $data = []; 
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = $_POST['name'];
            $query = "INSERT INTO majors (name) VALUES (?)";
            $stmt = $this->db->prepare($query);
            
            if ($stmt->execute([$name])) {
                // 👇 اینجا لاگ فعالیت اضافه شده است
                $this->logActivity('create_major', 'ایجاد رشته جدید: ' . $name, $this->db->lastInsertId(), 'majors');
                $data['success'] = 'رشته با موفقیت ایجاد شد';
            } else {
                $data['error'] = 'خطا در ایجاد رشته';
            }
        }
        
        $data = array_merge($this->getBaseData(), $data, [
            'majors' => $this->majorModel->getAll()->fetchAll(PDO::FETCH_ASSOC)
        ]);
        
        $this->view('admin/majors', $data);
    }
    
    public function courses() {
        $data = []; 
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $courseData = [
                'course_code' => $_POST['course_code'],
                'name' => $_POST['name'],
                'major_id' => $_POST['major_id'],
                'grade_id' => $_POST['grade_id'],
                'unit' => $_POST['unit'],
                'course_type' => $_POST['course_type']
            ];
            
            if ($this->courseModel->create($courseData)) {
                // 👇 اینجا لاگ فعالیت اضافه شده است
                $this->logActivity('create_course', 'ایجاد درس جدید: ' . $courseData['name'], $this->db->lastInsertId(), 'courses');
                $data['success'] = 'درس با موفقیت ایجاد شد';
            } else {
                $data['error'] = 'خطا در ایجاد درس';
            }
        }
        
        $data = array_merge($this->getBaseData(), $data, [
            'courses' => $this->courseModel->getAllWithDetails(),
            'majors' => $this->majorModel->getAll()->fetchAll(PDO::FETCH_ASSOC),
            'grades' => $this->gradeModel->getAll()->fetchAll(PDO::FETCH_ASSOC)
        ]);
        
        $this->view('admin/courses', $data);
    }
    
    public function classes() {
        $data = []; 
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $classData = [
                'name' => $_POST['name'],
                'major_id' => $_POST['major_id'],
                'grade_id' => $_POST['grade_id'],
                'capacity' => $_POST['capacity']
            ];
            
            if ($this->classModel->create($classData)) {
                // 👇 اینجا لاگ فعالیت اضافه شده است
                $this->logActivity('create_class', 'ایجاد کلاس جدید: ' . $classData['name'], $this->db->lastInsertId(), 'classes');
                $data['success'] = 'کلاس با موفقیت ایجاد شد';
            } else {
                $data['error'] = 'خطا در ایجاد کلاس';
            }
        }
        
        $data = array_merge($this->getBaseData(), $data, [
            'classes' => $this->classModel->getAllWithDetails(),
            'majors' => $this->majorModel->getAll()->fetchAll(PDO::FETCH_ASSOC),
            'grades' => $this->gradeModel->getAll()->fetchAll(PDO::FETCH_ASSOC)
        ]);
        
        $this->view('admin/classes', $data);
    }

    public function assignStudents($class_id = null) {
        // اگر class_id وجود ندارد، از GET بگیرید
        if (!$class_id && isset($_GET['class_id'])) {
            $class_id = $_GET['class_id'];
        }
        
        if (!$class_id) {
            header('Location: ' . BASE_URL . 'admin/classes');
            exit;
        }
        
        $data = [];
        
        // اطلاعات کلاس را دریافت می‌کنیم
        $class = $this->classModel->getById($class_id);
        
        if (!$class) {
            $data['error'] = 'کلاس مورد نظر یافت نشد';
            header('Location: ' . BASE_URL . 'admin/classes');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['student_ids'])) {
                $assignedCount = 0;
                foreach ($_POST['student_ids'] as $student_id) {
                    if ($this->studentModel->assignToClass($student_id, $class_id)) {
                        $assignedCount++;
                    }
                }
                
                // 👇 اینجا لاگ فعالیت اضافه شده است
                if ($assignedCount > 0) {
                    $this->logActivity('assign_students', "تخصیص $assignedCount دانش‌آموز به کلاس " . $class['name'], $class_id, 'classes');
                }
                
                $data['success'] = "تعداد $assignedCount دانش‌آموز با موفقیت به کلاس تخصیص داده شدند";
            } else {
                $data['error'] = 'هیچ دانش‌آموزی انتخاب نشده است';
            }
        }
        
        $data = array_merge($this->getBaseData(), $data, [
            'class' => $class,
            'students' => $this->studentModel->getStudentsByMajor($class['major_id']),
            'current_students' => $this->studentModel->getStudentsByClass($class_id)
        ]);
        
        $this->view('admin/assign_students', $data);
    }
    
    // متد کمکی برای دریافت اطلاعات کلاس بر اساس ID
    public function getClassById($id) {
        $query = "SELECT c.*, m.name as major_name, g.name as grade_name 
                      FROM classes c 
                      JOIN majors m ON c.major_id = m.id 
                      JOIN grades g ON c.grade_id = g.id 
                      WHERE c.id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function students() {
        $data = []; 
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // ابتدا کاربر دانش‌آموز ایجاد می‌کنیم
            $userData = [
                'mobile' => $_POST['mobile'],
                'national_code' => $_POST['national_code'],
                'role_id' => 2, // نقش دانش‌آموز
                'first_name' => $_POST['first_name'],
                'last_name' => $_POST['last_name']
            ];
            
            if ($this->userModel->create($userData)) {
                // کاربر ایجاد شد، حالا دانش‌آموز ایجاد می‌کنیم
                $user_id = $this->db->lastInsertId();
                
                $studentData = [
                    'user_id' => $user_id,
                    'class_id' => $_POST['class_id'],
                    'student_number' => $_POST['student_number'],
                    'birth_date' => $_POST['birth_date'],
                    'father_name' => $_POST['father_name'],
                    'address' => $_POST['address']
                ];
                
                if ($this->studentModel->create($studentData)) {
                    // 👇 اینجا لاگ فعالیت اضافه شده است
                    $this->logActivity('create_student', 'ثبت دانش‌آموز جدید: ' . $userData['first_name'] . ' ' . $userData['last_name'], $user_id, 'students');
                    $data['success'] = 'دانش‌آموز با موفقیت ثبت شد';
                } else {
                    $data['error'] = 'خطا در ثبت اطلاعات دانش‌آموز';
                }
            } else {
                $data['error'] = 'خطا در ایجاد کاربر دانش‌آموز';
            }
        }
        
        $data = array_merge($this->getBaseData(), $data, [
            'students' => $this->studentModel->getAllWithDetails(),
            'classes' => $this->classModel->getAllWithDetails()
        ]);
        
        $this->view('admin/students', $data);
    }
    
    public function teachers() {
        $data = []; 
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // ایجاد کاربر معلم
            $userData = [
                'mobile' => $_POST['mobile'],
                'national_code' => $_POST['national_code'],
                'role_id' => 3, // نقش معلم
                'first_name' => $_POST['first_name'],
                'last_name' => $_POST['last_name']
            ];
            
            if ($this->userModel->create($userData)) {
                $user_id = $this->db->lastInsertId();
                
                $teacherData = [
                    'user_id' => $user_id,
                    'expertise' => $_POST['expertise'],
                    'employment_date' => $_POST['employment_date']
                ];
                
                if ($this->teacherModel->create($teacherData)) {
                    // 👇 اینجا لاگ فعالیت اضافه شده است
                    $this->logActivity('create_teacher', 'ثبت معلم جدید: ' . $userData['first_name'] . ' ' . $userData['last_name'], $user_id, 'teachers');
                    $data['success'] = 'معلم با موفقیت ثبت شد';
                } else {
                    $data['error'] = 'خطا در ثبت اطلاعات معلم';
                }
            } else {
                $data['error'] = 'خطا در ایجاد کاربر معلم';
            }
        }
        
        $data = array_merge($this->getBaseData(), $data, [
            'teachers' => $this->teacherModel->getAllWithDetails()
        ]);
        
        $this->view('admin/teachers', $data);
    }
    
    public function assignTeacherCourse() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $teacher_id = $_POST['teacher_id'] ?? null;
            $course_id = $_POST['course_id'] ?? null;
            $class_id = $_POST['class_id'] ?? null;
            
            // اعتبارسنجی
            if (!$teacher_id || !$course_id) {
                $_SESSION['error'] = 'لطفاً معلم و درس را انتخاب کنید';
                $this->redirect('admin/assign_course');
                return;
            }
            
            // تبدیل رشته خالی به null
            if ($class_id === '') {
                $class_id = null;
            }
            
            // بررسی تکراری نبودن
            if ($this->teacherCourseModel->checkAssignmentExists($teacher_id, $course_id, $class_id)) {
                $_SESSION['error'] = 'این تخصیص قبلاً انجام شده است';
                $this->redirect('admin/assign_course');
                return;
            }
            
            // تخصیص درس
            $assignment_data = [
                'teacher_id' => $teacher_id,
                'course_id' => $course_id,
                'class_id' => $class_id
            ];
            
            if ($this->teacherCourseModel->assignCourse($assignment_data)) {
                $_SESSION['success'] = 'درس با موفقیت به معلم تخصیص داده شد';
                
                // ثبت لاگ فعالیت
                $this->logActivity(
                    'course_assignment',
                    "تخصیص درس جدید به معلم",
                    $teacher_id,
                    'teacher_courses'
                );
            } else {
                $_SESSION['error'] = 'خطا در تخصیص درس';
            }
            
            $this->redirect('admin/assign_course');
        }
        
        // نمایش صفحه
        $data = [
            'teachers' => $this->teacherModel->getAllWithDetails(),
            'courses' => $this->courseModel->getAllWithDetails()
        ];
        
        $this->view('admin/assign_course', $data);
    }
    
    public function getTeacherCourses($teacher_id) {
        $courses = $this->teacherModel->getTeacherCourses($teacher_id);
        echo json_encode($courses);
    }
    
    public function getCoursesByMajorGrade($major_id, $grade_id) {
        $query = "SELECT * FROM courses WHERE major_id = ? AND grade_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$major_id, $grade_id]);
        $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($courses);
    }


    public function users() {
        $data = []; 
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $userData = [
                'mobile' => $_POST['mobile'],
                'national_code' => $_POST['national_code'],
                'role_id' => $_POST['role_id'],
                'first_name' => $_POST['first_name'],
                'last_name' => $_POST['last_name']
            ];
            
            // اضافه کردن فیلدهای اختیاری بر اساس نقش
            if ($_POST['role_id'] == 2) { // دانش‌آموز
                $userData['class_id'] = $_POST['class_id'] ?? null;
                $userData['birth_date'] = $_POST['birth_date'] ?? null;
                $userData['father_name'] = $_POST['father_name'] ?? '';
                $userData['address'] = $_POST['address'] ?? '';
            } elseif ($_POST['role_id'] == 3) { // معلم
                $userData['expertise'] = $_POST['expertise'] ?? '';
            } elseif ($_POST['role_id'] == 5) { // معاون
                $userData['grade_id'] = $_POST['grade_id'] ?? 1;
            } elseif ($_POST['role_id'] == 4) { // اولیا
                $userData['student_id'] = $_POST['student_id'] ?? null;
                $userData['relation_type'] = $_POST['relation_type'] ?? 'father';
            }
            
            if ($this->userModel->create($userData)) {
                // 👇 اینجا لاگ فعالیت اضافه شده است
                $user_id = $this->db->lastInsertId(); // باید ID کاربر ایجاد شده را بگیرید
                $this->logActivity('create_user', 'ایجاد کاربر جدید: ' . $userData['first_name'] . ' ' . $userData['last_name'], $user_id, 'users');
                $data['success'] = 'کاربر با موفقیت ایجاد شد و در سیستم مربوطه ثبت گردید';
            } else {
                $data['error'] = 'خطا در ایجاد کاربر';
            }
        }
        
        $data = array_merge($this->getBaseData(), $data, [
            'users' => $this->userModel->getUsersWithRole(),
            'roles' => $this->userModel->getAllRoles(),
            'grades' => $this->gradeModel->getAll()->fetchAll(PDO::FETCH_ASSOC),
            'students' => $this->studentModel->getAllWithDetails(),
            'classes' => $this->classModel->getAllWithDetails()
        ]);
        
        $this->view('admin/users', $data);
    }

    
    public function staffFiles() {
        try {
            // دریافت لیست معلمان و معاونین
            $teachers = $this->teacherModel->getAllWithDetails();
            $assistants = $this->assistantModel->getAllWithDetails();
            
            // دریافت امتیازات
            $staff_scores = $this->staffScoreModel->getAllScores();
            
            // ترکیب داده‌های پایه با داده‌های خاص این صفحه
            $data = array_merge($this->getBaseData(), [
                'teachers' => $teachers,
                'assistants' => $assistants,
                'staff_scores' => $staff_scores
            ]);
            
            $this->view('admin/staff_files', $data);
        } catch (Exception $e) {
            die('خطا در بارگذاری صفحه پرونده همکاران: ' . $e->getMessage());
        }
    }
    
    public function staffDetail($staff_type, $staff_id) {
        try {
            // دریافت اطلاعات همکار
            if ($staff_type == 'teacher') {
                $staff = $this->teacherModel->getById($staff_id);
                if (!$staff) {
                    throw new Exception('معلم یافت نشد');
                }
                
                $staff_profile_model = $this->model('TeacherProfile');
                $staff_profile = $staff_profile_model->getByTeacherId($staff_id);
                $staff_user = $this->userModel->getUserWithRoleDetails($staff['user_id']);
            } else {
                $staff = $this->assistantModel->getByAssistantId($staff_id);
                if (!$staff) {
                    throw new Exception('معاون یافت نشد');
                }
                
                $staff_profile_model = $this->model('AssistantProfile');
                $staff_profile = $staff_profile_model->getByAssistantId($staff_id);
                $staff_user = $this->userModel->getUserWithRoleDetails($staff['user_id']);
            }

            // بررسی وجود staff_user
            if (!$staff_user) {
                throw new Exception('اطلاعات کاربر یافت نشد');
            }

            // دریافت پرونده انضباطی/تشویقی
            $staff_records = $this->staffRecordModel->getByStaff($staff_id, $staff_type);
            
            // دریافت امتیاز
            $staff_score = $this->staffScoreModel->getByStaff($staff_id, $staff_type);
            
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $jalali_date = $this->gregorianToJalali($_POST['record_date']);
                
                $record_data = [
                    'staff_id' => $staff_id,
                    'staff_type' => $staff_type,
                    'record_date' => $_POST['record_date'],
                    'jalali_date' => $jalali_date,
                    'record_type' => $_POST['record_type'],
                    'title' => $_POST['title'],
                    'description' => $_POST['description'],
                    'points' => $_POST['points'],
                    'created_by' => $_SESSION['user_id']
                ];
                
                if ($this->staffRecordModel->create($record_data)) {
                    // 👇 اینجا لاگ فعالیت اضافه شده است
                    $this->logActivity('staff_record', 'ثبت رکورد برای ' . $staff_type . ': ' . $record_data['title'], $staff_id, 'staff_records');

                    // آپدیت امتیاز
                    $points_data = $this->staffRecordModel->getTotalPoints($staff_id, $staff_type);
                    $this->staffScoreModel->updateScore(
                        $staff_id, 
                        $staff_type, 
                        $points_data['total_encouragement'] ?? 0, 
                        $points_data['total_disciplinary'] ?? 0
                    );
                    
                    $_SESSION['success'] = 'رکورد با موفقیت ثبت شد';
                    $this->redirect('admin/staffDetail/' . $staff_type . '/' . $staff_id);
                } else {
                    $data['error'] = 'خطا در ثبت رکورد';
                }
            }
            
            $data = [
                'staff' => $staff,
                'staff_profile' => $staff_profile ?: [],
                'staff_user' => $staff_user,
                'staff_type' => $staff_type,
                'staff_records' => $staff_records,
                'staff_score' => $staff_score ?: ['current_score' => 100, 'total_encouragement' => 0, 'total_disciplinary' => 0],
                'user_name' => $_SESSION['first_name'] . ' ' . $_SESSION['last_name']
            ];
            
            $this->view('admin/staff_detail', $data);
            
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('admin/staffFiles');
        }
    }
    
    public function staffRecords() {
        try {
            $filters = [
                'staff_type' => $_GET['staff_type'] ?? '',
                'record_type' => $_GET['record_type'] ?? '',
                'start_date' => $_GET['start_date'] ?? '',
                'end_date' => $_GET['end_date'] ?? '',
                'status' => $_GET['status'] ?? ''
            ];
            
            $records = $this->staffRecordModel->getAllRecords($filters);
            
            $data = array_merge($this->getBaseData(), [
                'records' => $records,
                'filters' => $filters
            ]);
            
            $this->view('admin/staff_records', $data);
        } catch (Exception $e) {
            die('خطا در بارگذاری صفحه رکوردها: ' . $e->getMessage());
        }
    }
    
    // تابع تبدیل تاریخ میلادی به شمسی
    private function gregorianToJalali($gregorian_date) {
        $timestamp = strtotime($gregorian_date);
        $year = date('Y', $timestamp);
        $month = date('m', $timestamp);
        $day = date('d', $timestamp);
        
        // تبدیل ساده
        $jalali_year = $year - 621;
        return $jalali_year . '/' . $month . '/' . $day;
    }





// متد برای بارگذاری کلاس‌ها
public function getClassesByMajorGrade($major_id, $grade_id) {
    $classes = $this->classModel->getByMajorAndGrade($major_id, $grade_id);
    header('Content-Type: application/json');
    echo json_encode($classes);
    exit;
}

// متد برای بررسی تخصیص
public function checkAssignment() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $teacher_id = $_POST['teacher_id'] ?? null;
        $course_id = $_POST['course_id'] ?? null;
        $class_id = $_POST['class_id'] ?? null;
        
        if ($class_id === '') {
            $class_id = null;
        }
        
        $exists = $this->teacherModel->checkAssignmentExists($teacher_id, $course_id, $class_id);
        
        header('Content-Type: application/json');
        echo json_encode(['exists' => $exists]);
        exit;
    }
}
}
?>