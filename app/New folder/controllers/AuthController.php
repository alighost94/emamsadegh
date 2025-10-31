<?php
class AuthController extends Controller {
    
    public function __construct() {
        parent::__construct();
        $this->userModel = $this->model('User');
    }
    
    public function index() {
        if ($this->isLoggedIn()) {
            $this->redirectToDashboard();
        } else {
            $this->redirect('auth/login');
        }
    }
    
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // اعتبارسنجی داده‌های ورودی
            $errors = [];
            
            // استفاده از trim و filter_input برای پاکسازی اولیه و امنیت
            $mobile = filter_input(INPUT_POST, 'mobile', FILTER_SANITIZE_STRING);
            $password = filter_input(INPUT_POST, 'password', FILTER_DEFAULT); // رمز عبور را بدون فیلتر نگه می‌داریم

            if (empty($mobile)) {
                $errors[] = 'شماره موبایل الزامی است';
            }
            
            if (empty($password)) {
                $errors[] = 'رمز عبور الزامی است';
            }
            
            if (!empty($errors)) {
                $data['error'] = implode('<br>', $errors);
                $this->view('auth/login', $data);
                return;
            }
            
            // فراخوانی مدل برای بررسی لاگین
            $user = $this->userModel->login(trim($mobile), $password);
            
            // اعمال منطق جدید درخواستی برنامه‌نویس
            if ($user && empty($user['error'])) {
                
                // بررسی اجبار به تغییر رمز عبور
                if (isset($user['must_change_password']) && $user['must_change_password']) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['mobile'] = $user['mobile']; // برای نمایش در صفحه تغییر رمز
                    $_SESSION['force_password_change'] = true;
                    $this->redirect('auth/changePassword');
                    return;
                }

                // ورود عادی و ثبت اطلاعات سشن
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['mobile'] = $user['mobile'];
                $_SESSION['role'] = $user['role_name'];
                $_SESSION['first_name'] = $user['first_name'];
                $_SESSION['last_name'] = $user['last_name'];
                $_SESSION['national_code'] = $user['national_code'];
                
                $this->redirectToDashboard();
                
            } elseif (is_array($user) && isset($user['error'])) {
                // اگر مدل، آرایه‌ای حاوی پیام خطا برگردانده باشد (مثلاً کاربر غیرفعال است)
                $data['error'] = $user['error'];
                $this->view('auth/login', $data);
                
            } else {
                // اگر لاگین ناموفق باشد (رمز یا موبایل اشتباه)
                $data['error'] = 'شماره موبایل یا رمز عبور اشتباه است';
                $this->view('auth/login', $data);
            }
            
        } else {
            // نمایش فرم لاگین برای اولین بار (متد GET)
            $this->view('auth/login');
        }
    }


    public function changePassword() {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('auth/login');
            return;
        }
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $newPass = $_POST['new_password'];
            $confirmPass = $_POST['confirm_password'];
    
            if ($newPass !== $confirmPass) {
                $data['error'] = 'رمز عبور و تکرار آن یکسان نیست';
                $this->view('auth/change_password', $data);
                return;
            }
    
            $this->userModel->updatePassword($_SESSION['user_id'], $newPass);
            unset($_SESSION['force_password_change']);
    
            $data['success'] = 'رمز عبور با موفقیت تغییر یافت.';
            $this->redirect('dashboard');
        } else {
            $this->view('auth/change_password');
        }
    }
    
    private function redirectToDashboard() {
        switch ($_SESSION['role']) {
            case 'admin':
                $this->redirect('dashboard');
                break;
            case 'teacher':
                $this->redirect('teacher');
                break;
            case 'student':
                $this->redirect('student');
                break;
            case 'parent':
                $this->redirect('parent');
                break;
            case 'assistant':
                $this->redirect('assistant');
                break;
            default:
                $this->redirect('dashboard');
        }
    }
    
    public function logout() {
        session_destroy();
        $this->redirect('auth/login');
    }




    public function grades() {
        $assistant = $this->assistantModel->getByUserId($_SESSION['user_id']);
        
        $grades = $this->assistantModel->getGradesByGrade($assistant['grade_id']);
        
        // گروه‌بندی نمرات بر اساس کلاس و دانش‌آموز
        $grades_by_student = [];
        foreach ($grades as $record) {
            $student_id = $record['student_id'];
            if (!isset($grades_by_student[$student_id])) {
                $grades_by_student[$student_id] = [
                    'student_info' => [
                        'first_name' => $record['first_name'],
                        'last_name' => $record['last_name'],
                        'student_number' => $record['student_number'],
                        'class_name' => $record['class_name']
                    ],
                    'courses' => []
                ];
            }
            $grades_by_student[$student_id]['courses'][] = $record;
        }
        
        // محاسبه معدل برای هر دانش‌آموز
        foreach ($grades_by_student as $student_id => &$student_data) {
            $student_data['average'] = $this->calculateStudentAverage($student_data['courses']);
        }

        $data = [
            'assistant' => $assistant,
            'grades' => $grades,
            'grades_by_student' => $grades_by_student,
            'user_name' => $_SESSION['first_name'] . ' ' . $_SESSION['last_name']
        ];
        
        $this->view('assistant/grades', $data);
    }
    
    // تغییر این متدها به public
    public function calculateCourseGrade($course) {
        if ($course['course_type'] == 'poodmani') {
            // میانگین ۵ پودمان برای دروس پودمانی
            $grades = [
                $course['poodman1'] ?? 0,
                $course['poodman2'] ?? 0,
                $course['poodman3'] ?? 0,
                $course['poodman4'] ?? 0,
                $course['poodman5'] ?? 0
            ];
            $valid_grades = array_filter($grades, function($g) { return $g > 0; });
            return count($valid_grades) > 0 ? array_sum($valid_grades) / count($valid_grades) : 0;
        } else {
            // میانگین دو نیمسال برای دروس غیر پودمانی
            $term1_continuous = $course['continuous1'] ?? 0;
            $term1_final = $course['term1'] ?? 0;
            $term2_continuous = $course['continuous2'] ?? 0;
            $term2_final = $course['term2'] ?? 0;
            
            // اگر نمره‌ای وجود ندارد، صفر در نظر بگیر
            $term1 = ($term1_continuous + $term1_final) > 0 ? ($term1_continuous + $term1_final) / 2 : 0;
            $term2 = ($term2_continuous + $term2_final) > 0 ? ($term2_continuous + $term2_final) / 2 : 0;
            
            // اگر هر دو نیمسال نمره دارند، میانگین بگیر
            if ($term1 > 0 && $term2 > 0) {
                return ($term1 + $term2) / 2;
            } elseif ($term1 > 0) {
                return $term1;
            } elseif ($term2 > 0) {
                return $term2;
            } else {
                return 0;
            }
        }
    }
    
    public function calculateStudentAverage($courses) {
        $total_grade = 0;
        $course_count = 0;
        
        foreach ($courses as $course) {
            $course_grade = $this->calculateCourseGrade($course);
            if ($course_grade > 0) {
                $total_grade += $course_grade;
                $course_count++;
            }
        }
        
        return $course_count > 0 ? round($total_grade / $course_count, 2) : 0;
    }
    // اضافه کردن این متدها به AdminController

// بارگذاری کلاس‌های یک رشته و پایه خاص
public function getClassesByMajorGrade($major_id, $grade_id) {
    $classes = $this->classModel->getByMajorAndGrade($major_id, $grade_id);
    header('Content-Type: application/json');
    echo json_encode($classes);
}

// بررسی تکراری نبودن تخصیص
public function checkAssignment() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $teacher_id = $_POST['teacher_id'];
        $course_id = $_POST['course_id'];
        $class_id = $_POST['class_id'] ?: null;
        
        $exists = $this->teacherCourseModel->checkAssignmentExists($teacher_id, $course_id, $class_id);
        
        header('Content-Type: application/json');
        echo json_encode(['exists' => $exists]);
    }
}

// تخصیص درس به معلم (آپدیت شده)
public function assignCourseToTeacher() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $data = [
            'teacher_id' => $_POST['teacher_id'],
            'course_id' => $_POST['course_id'],
            'class_id' => $_POST['class_id'] ?: null
        ];
        
        if ($this->teacherCourseModel->assignCourse($data)) {
            $_SESSION['success'] = 'درس با موفقیت به معلم تخصیص داده شد';
        } else {
            $_SESSION['error'] = 'خطا در تخصیص درس';
        }
        
        $this->redirect('admin/assign_course');
    }
}
}
?>
