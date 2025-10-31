<?php
class TeacherController extends Controller {
    protected $teacherModel;
    protected $teacherProfileModel;
    protected $attendanceModel;
    protected $gradeModel;
    protected $courseModel;

    public function __construct() {
        parent::__construct();
        if (!$this->isLoggedIn() || $_SESSION['role'] != 'teacher') {
            $this->redirect('auth/login');
        }
        
        $this->teacherModel = $this->model('Teacher');
        $this->teacherProfileModel = $this->model('TeacherProfile');
        $this->attendanceModel = $this->model('StudentAttendance');
        $this->gradeModel = $this->model('StudentGrade');
        $this->courseModel = $this->model('Course');
        
        // بررسی تکمیل پروفایل
        $this->checkProfileCompletion();
    }
    
    private function checkProfileCompletion() {
        $teacher = $this->teacherModel->getByUserId($_SESSION['user_id']);
        
        if ($teacher && !$teacher['profile_completed']) {
            $profile = $this->teacherProfileModel->getByTeacherId($teacher['id']);
            
            // اگر پروفایل کامل شده، آپدیت وضعیت
            if ($profile && $profile['profile_completed']) {
                $this->teacherModel->updateProfileCompletion($teacher['id']);
            } else {
                // اگر پروفایل کامل نشده و در صفحه پروفایل نیست، هدایت به پروفایل
                $current_url = $_SERVER['REQUEST_URI'];
                if (strpos($current_url, 'teacher/profile') === false) {
                    $this->redirect('teacher/profile');
                }
            }
        }
    }
    
    public function index() {
        $teacher = $this->teacherModel->getByUserId($_SESSION['user_id']);
        $profile = $this->teacherProfileModel->getByTeacherId($teacher['id']);
        $courses = $this->teacherModel->getTeacherCourses($teacher['id']);
        
        $data = [
            'teacher' => $teacher,
            'profile' => $profile,
            'courses' => $courses,
            'user_name' => $_SESSION['first_name'] . ' ' . $_SESSION['last_name']
        ];
        
        $this->view('teacher/dashboard', $data);
    }
    
    public function profile() {
        $teacher = $this->teacherModel->getByUserId($_SESSION['user_id']);
        $profile = $this->teacherProfileModel->getByTeacherId($teacher['id']);
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // آپلود فایل‌ها
            $upload_dir = 'uploads/teachers/' . $teacher['id'] . '/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $profile_data = [
                'teacher_id' => $teacher['id'],
                'personnel_code' => $_POST['personnel_code'],
                'bank_account_number' => $_POST['bank_account_number'],
                'bank_card_number' => $_POST['bank_card_number'],
                'education_degree' => $_POST['education_degree'],
                'major' => $_POST['major'],
                'postal_code' => $_POST['postal_code'],
                'address' => $_POST['address'],
                'presence_days' => implode(',', $_POST['presence_days'] ?? []),
                'terms_accepted' => isset($_POST['terms_accepted']) ? 1 : 0,
                'profile_completed' => 1
            ];
            
            // آپلود عکس پروفایل
            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
                $profile_image = $this->uploadFile($_FILES['profile_image'], $upload_dir, 'profile');
                if ($profile_image) {
                    $profile_data['profile_image'] = $profile_image;
                }
            }
            
            // آپلود سایر فایل‌ها
            $file_fields = [
                'national_card_image' => 'national_card',
                'birth_certificate_image' => 'birth_certificate', 
                'decree_file' => 'decree',
                'resume_file' => 'resume'
            ];
            
            foreach ($file_fields as $field => $prefix) {
                if (isset($_FILES[$field]) && $_FILES[$field]['error'] == 0) {
                    $filename = $this->uploadFile($_FILES[$field], $upload_dir, $prefix);
                    if ($filename) {
                        $profile_data[$field] = $filename;
                    }
                }
            }
            
            if ($this->teacherProfileModel->createOrUpdate($profile_data)) {
                // آپدیت وضعیت تکمیل پروفایل در جدول teachers
                $this->teacherModel->updateProfileCompletion($teacher['id']);
                
                // 🔥 ثبت فعالیت در سیستم
                $this->logActivity(
                    'complete_profile', 
                    'تکمیل پروفایل معلم',
                    $teacher['id'],
                    'teacher_profiles'
                );
                
                $_SESSION['success'] = 'پروفایل با موفقیت تکمیل شد';
                $this->redirect('teacher');
            } else {
                $data['error'] = 'خطا در ذخیره اطلاعات پروفایل';
            }
        }
        
        $data['teacher'] = $teacher;
        $data['profile'] = $profile;
        $data['user_name'] = $_SESSION['first_name'] . ' ' . $_SESSION['last_name'];
        
        $this->view('teacher/profile', $data);
    }
    
    private function uploadFile($file, $upload_dir, $prefix) {
        $allowed_types = [
            'image/jpeg', 'image/jpg', 'image/png', 'image/gif',
            'application/pdf'
        ];
        
        if (in_array($file['type'], $allowed_types)) {
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = $prefix . '_' . time() . '.' . $extension;
            $filepath = $upload_dir . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $filepath)) {
                return $filename;
            }
        }
        
        return false;
    }
    public function attendance() {
       
        $teacher = $this->teacherModel->getByUserId($_SESSION['user_id']);
        $courses = $this->teacherModel->getTeacherCourses($teacher['id']);
    
        $selected_course = $_GET['course_id'] ?? null;
        $selected_class = $_GET['class_id'] ?? null;
        
        // 🔥 decode کردن تاریخ از URL
        $selected_date_input = $_GET['date'] ?? date('Y-m-d');
        $selected_date_input = urldecode($selected_date_input); // اضافه شد
    
        // تبدیل تاریخ
        if (preg_match('/\//', $selected_date_input)) {
            // تاریخ شمسی هست - تبدیل به میلادی
            $selected_date = JalaliDate::jalaliToGregorian($selected_date_input, '/');
            $display_date = $selected_date_input; // تاریخ شمسی برای نمایش
        } else {
            // تاریخ میلادی هست - تبدیل به شمسی برای نمایش
            $selected_date = $selected_date_input;
            $display_date = $this->getJalaliDate($selected_date_input);
        }
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $jalali_date = $this->getJalaliDate($selected_date);
            
            $attendance_stats = $this->calculateAttendanceStats($_POST['attendance']);
            
            foreach ($_POST['attendance'] as $student_id => $status) {
                $attendance_data = [
                    'teacher_id'     => $teacher['id'],
                    'student_id'     => $student_id,
                    'course_id'      => $selected_course,
                    'attendance_date'=> $selected_date, // تاریخ میلادی
                    'jalali_date'    => $jalali_date,   // تاریخ شمسی
                    'status'         => $status,
                    'notes'          => $_POST['notes'][$student_id] ?? ''
                ];
                $this->attendanceModel->recordAttendance($attendance_data);
            }
            
            // ثبت لاگ
            $course_info = $this->getCourseInfo($selected_course);
            $class_info = $selected_class ? $this->getClassById($selected_class) : ['name' => 'همه کلاس‌ها'];
            
            $this->logActivity(
                'attendance_record', 
                "ثبت حضورغیاب {$class_info['name']} - {$attendance_stats['present']} حاضر، {$attendance_stats['absent']} غایب",
                $teacher['id'],
                'student_attendance'
            );
    
            $_SESSION['success'] = 'حضور و غیاب با موفقیت ثبت شد.';
// در قسمت redirect بعد از ثبت
$this->redirect('teacher/attendance?course_id=' . $selected_course . '&class_id=' . $selected_class . '&date=' . urlencode($display_date)); // 🔥 ارسال تاریخ شمسی
        }
        $students = [];
        $existing_attendance = [];
    
        if ($selected_course) {
            $students = $this->attendanceModel->getStudentsForAttendance($teacher['id'], $selected_course, $selected_class);
            $existing_attendance = $this->attendanceModel->getAttendanceByTeacher(
                $teacher['id'],
                $selected_course,
                $selected_date
            );
        }
    
        $data = [
            'teacher' => $teacher,
            'courses' => $courses,
            'selected_course' => $selected_course,
            'selected_class' => $selected_class,
            'selected_date' => $selected_date,
            'display_date' => $display_date, // 🔥 این باید تاریخ decode شده باشه
            'students' => $students,
            'existing_attendance' => $existing_attendance,
            'user_name' => $_SESSION['first_name'] . ' ' . $_SESSION['last_name'],
            'controller' => $this
        ];
    
        $this->view('teacher/attendance', $data);
    }

    // در TeacherController اضافه کنید
public function getClassesByCourse($course_id) {
    $course = $this->courseModel->getById($course_id);
    if ($course) {
        $classes = $this->classModel->getByMajorAndGrade($course['major_id'], $course['grade_id']);
        header('Content-Type: application/json');
        echo json_encode($classes);
        exit;
    }
}
    // متد کمکی جدید برای گرفتن اطلاعات کلاس
    private function getClassById($class_id) {
        $query = "SELECT * FROM classes WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$class_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // 🔥 متد کمکی برای محاسبه آمار حضور و غیاب
    private function calculateAttendanceStats($attendance_data) {
        $stats = [
            'present' => 0,
            'absent' => 0,
            'late' => 0,
            'excused' => 0,
            'total' => count($attendance_data)
        ];
        
        foreach ($attendance_data as $status) {
            switch ($status) {
                case 'present':
                    $stats['present']++;
                    break;
                case 'absent':
                    $stats['absent']++;
                    break;
                case 'late':
                    $stats['late']++;
                    break;
                case 'excused':
                    $stats['excused']++;
                    break;
            }
        }
        
        return $stats;
    }
    
    // 🔥 متد کمکی برای دریافت اطلاعات درس
    private function getCourseInfo($course_id) {
        $query = "SELECT c.*, m.name as major_name, g.name as grade_name 
                  FROM courses c 
                  JOIN majors m ON c.major_id = m.id 
                  JOIN grades g ON c.grade_id = g.id 
                  WHERE c.id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$course_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // 🔥 متد کمکی برای دریافت اطلاعات کلاس
    private function getClassInfo($major_id, $grade_id) {
        $query = "SELECT name as class_name FROM classes 
                  WHERE major_id = ? AND grade_id = ? 
                  LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$major_id, $grade_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ?: ['class_name' => 'نامشخص'];
    }
    
    public function grades() {
        $teacher = $this->teacherModel->getByUserId($_SESSION['user_id']);
        $courses = $this->teacherModel->getTeacherCourses($teacher['id']);
        
        $selected_course = $_GET['course_id'] ?? null;
        $selected_class = $_GET['class_id'] ?? null; // اضافه شد
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $course_type = $_POST['course_type'];
            $student_count = count($_POST['grades']);
            
            foreach ($_POST['grades'] as $student_id => $grade_data) {
                $grade_record = [
                    'teacher_id' => $teacher['id'],
                    'student_id' => $student_id,
                    'course_id' => $selected_course,
                    'course_type' => $course_type
                ];
                
                if ($course_type == 'poodmani') {
                    for ($i = 1; $i <= 5; $i++) {
                        $grade_record['poodman' . $i] = $grade_data['poodman' . $i] ?? null;
                    }
                } else {
                    $grade_record['continuous1'] = $grade_data['continuous1'] ?? null;
                    $grade_record['term1'] = $grade_data['term1'] ?? null;
                    $grade_record['continuous2'] = $grade_data['continuous2'] ?? null;
                    $grade_record['term2'] = $grade_data['term2'] ?? null;
                }
                
                $this->gradeModel->recordGrade($grade_record);
            }
            
            // 🔥 ثبت فعالیت در سیستم
            $course_info = $this->getCourseInfo($selected_course);
            $class_info = $selected_class ? $this->getClassById($selected_class) : ['name' => 'همه کلاس‌ها'];
            
            $this->logActivity(
                'grade_record', 
                "ثبت نمرات درس {$course_info['name']} برای کلاس {$class_info['name']} ({$student_count} دانش‌آموز)",
                $teacher['id'],
                'student_grades'
            );
            
            $_SESSION['success'] = 'نمرات با موفقیت ثبت شد';
            $this->redirect('teacher/grades?course_id=' . $selected_course . '&class_id=' . $selected_class);
        }
        
        $students = [];
        $existing_grades = [];
        $course_type = '';
        
        if ($selected_course) {
            // 🔥 استفاده از متد آپدیت شده با پارامتر class_id
            $students = $this->gradeModel->getStudentsForGrading($teacher['id'], $selected_course, $selected_class);
            $existing_grades = $this->gradeModel->getGradesByTeacher($teacher['id'], $selected_course);
            
            // دریافت نوع درس
            $course_info = $this->courseModel->getById($selected_course);
            $course_type = $course_info['course_type'];
        }
        
        $data = [
            'teacher' => $teacher,
            'courses' => $courses,
            'selected_course' => $selected_course,
            'selected_class' => $selected_class, // اضافه شد
            'students' => $students,
            'existing_grades' => $existing_grades,
            'course_type' => $course_type,
            'user_name' => $_SESSION['first_name'] . ' ' . $_SESSION['last_name']
        ];
        
        $this->view('teacher/grades', $data);
    }
    
    // تابع تبدیل تاریخ میلادی به شمسی (ساده)
    private function gregorianToJalali($gregorian_date) {
        $timestamp = strtotime($gregorian_date);
        $year = date('Y', $timestamp);
        $month = date('m', $timestamp);
        $day = date('d', $timestamp);
        
        // تبدیل ساده (برای تست)
        $jalali_year = $year - 621;
        return $jalali_year . '/' . $month . '/' . $day;
    }

    public function staffRecords() {
        $teacher = $this->teacherModel->getByUserId($_SESSION['user_id']);
        
        // دریافت رکوردهای مربوط به این معلم
        $staff_records = $this->model('StaffRecord')->getByTeacher($teacher['id']);
        
        // دریافت امتیاز
        $staff_score = $this->model('StaffScore')->getByTeacher($teacher['id']);
        
        $data = [
            'teacher' => $teacher,
            'staff_records' => $staff_records,
            'staff_score' => $staff_score,
            'user_name' => $_SESSION['first_name'] . ' ' . $_SESSION['last_name']
        ];
        
        $this->view('teacher/staff_records', $data);
    }
}
?>