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
    
    private function uploadFile($file, $upload_dir, $prefix) {
        $allowed_types = [
            'image/jpeg', 'image/jpg', 'image/png', 'image/gif',
            'application/pdf'
        ];
        
        if (in_array($file['type'], $allowed_types)) {
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = $prefix . '_' . time() . '.' . $extension;
            $filepath = $upload_dir . $filename;
            
            // اگر فایل عکس است، کم کردن حجم
            if (strpos($file['type'], 'image/') === 0) {
                if ($this->compressImage($file['tmp_name'], $filepath, 75)) {
                    return $filename;
                }
            } else {
                if (move_uploaded_file($file['tmp_name'], $filepath)) {
                    return $filename;
                }
            }
        }
        
        return false;
    }
    
    // تابع جدید برای کم کردن حجم عکس
    private function compressImage($source, $destination, $quality) {
        $info = getimagesize($source);
        
        if ($info['mime'] == 'image/jpeg') {
            $image = imagecreatefromjpeg($source);
        } elseif ($info['mime'] == 'image/png') {
            $image = imagecreatefrompng($source);
        } elseif ($info['mime'] == 'image/gif') {
            $image = imagecreatefromgif($source);
        } else {
            return false;
        }
        
        // کاهش سایز اگر عکس بزرگ است
        $max_width = 800;
        $max_height = 800;
        
        $width = imagesx($image);
        $height = imagesy($image);
        
        if ($width > $max_width || $height > $max_height) {
            $ratio = $width / $height;
            
            if ($ratio > 1) {
                $new_width = $max_width;
                $new_height = $max_width / $ratio;
            } else {
                $new_height = $max_height;
                $new_width = $max_height * $ratio;
            }
            
            $resized_image = imagecreatetruecolor($new_width, $new_height);
            imagecopyresampled($resized_image, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
            $image = $resized_image;
        }
        
        // ذخیره عکس با کیفیت کاهش یافته
        if ($info['mime'] == 'image/jpeg') {
            $result = imagejpeg($image, $destination, $quality);
        } elseif ($info['mime'] == 'image/png') {
            $result = imagepng($image, $destination, 9 - round($quality / 10));
        } elseif ($info['mime'] == 'image/gif') {
            $result = imagegif($image, $destination);
        }
        
        imagedestroy($image);
        return $result;
    }
    
    public function profile() {
        $teacher = $this->teacherModel->getByUserId($_SESSION['user_id']);
        $profile = $this->teacherProfileModel->getByTeacherId($teacher['id']);
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // دیباگ داده‌های دریافتی
            error_log('=== POST DATA DEBUG ===');
            error_log('Birth Date: ' . ($_POST['birth_date'] ?? 'NOT SET'));
            error_log('Is Retired: ' . ($_POST['is_retired'] ?? 'NOT SET'));
            error_log('All POST data: ' . print_r($_POST, true));
            
            // آپلود فایل‌ها
            $upload_dir = 'uploads/teachers/' . $teacher['id'] . '/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            // تبدیل اعداد فارسی به انگلیسی
            $personnel_code = $this->convertToEnglishNumbers($_POST['personnel_code'] ?? '');
            $bank_account_number = $this->convertToEnglishNumbers($_POST['bank_account_number'] ?? '');
            $bank_card_number = $this->convertToEnglishNumbers($_POST['bank_card_number'] ?? '');
            $sheba_number = $this->convertToEnglishNumbers($_POST['sheba_number'] ?? '');
            $postal_code = $this->convertToEnglishNumbers($_POST['postal_code'] ?? '');
            
            // اضافه کردن IR به شماره شبا اگر وجود ندارد
            if (!empty($sheba_number) && !str_starts_with($sheba_number, 'IR')) {
                $sheba_number = 'IR' . $sheba_number;
            }
            $is_retired = isset($_POST['is_retired']) ? (int)$_POST['is_retired'] : 0;

            $profile_data = [
                'teacher_id' => $teacher['id'],
                'personnel_code' => $personnel_code,
                'bank_account_number' => $bank_account_number,
                'bank_card_number' => $bank_card_number,
                'sheba_number' => $sheba_number,
                'father_name' => $_POST['father_name'] ?? '',
                'birth_date' => $_POST['birth_date'] ?? null,
                'education_degree' => $_POST['education_degree'] ?? '',
                'major' => $_POST['major'] ?? '',
                'postal_code' => $postal_code,
                'address' => $_POST['address'] ?? '',
                'presence_days' => implode(',', $_POST['presence_days'] ?? []),
                'terms_accepted' => isset($_POST['terms_accepted']) ? 1 : 0,
                'is_retired' => $is_retired, // استفاده از مقدار دریافت شده
                'profile_completed' => 1
            ];
            
            error_log('Profile data to save: ' . print_r($profile_data, true));
            
            // آپلود عکس پروفایل (اجباری)
            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
                $profile_image = $this->uploadFile($_FILES['profile_image'], $upload_dir, 'profile');
                if ($profile_image) {
                    $profile_data['profile_image'] = $profile_image;
                }
            }
            
            // آپلود سایر فایل‌های اجباری
            $file_fields = [
                'national_card_image' => 'national_card',
                'birth_certificate_image' => 'birth_certificate', 
                'decree_file' => 'decree'
            ];
            
            foreach ($file_fields as $field => $prefix) {
                if (isset($_FILES[$field]) && $_FILES[$field]['error'] == 0) {
                    $filename = $this->uploadFile($_FILES[$field], $upload_dir, $prefix);
                    if ($filename) {
                        $profile_data[$field] = $filename;
                    }
                }
            }
            
            // آپلود فایل سوابق (اگر ارسال شده)
            if (isset($_FILES['resume_file']) && $_FILES['resume_file']['error'] == 0) {
                $resume_file = $this->uploadFile($_FILES['resume_file'], $upload_dir, 'resume');
                if ($resume_file) {
                    $profile_data['resume_file'] = $resume_file;
                }
            }
            $remove_fields = ['profile_image', 'national_card_image', 'birth_certificate_image', 'decree_file', 'resume_file'];
foreach ($remove_fields as $field) {
    if (isset($_POST['remove_' . $field]) && $_POST['remove_' . $field] == '1') {
        // حذف فایل از سرور
        if (!empty($profile[$field]) && file_exists('uploads/teachers/' . $teacher['id'] . '/' . $profile[$field])) {
            unlink('uploads/teachers/' . $teacher['id'] . '/' . $profile[$field]);
        }
        // حذف از داده‌های پروفایل
        $profile_data[$field] = null;
    }
}
            
            if ($this->teacherProfileModel->createOrUpdate($profile_data)) {
                // آپدیت وضعیت تکمیل پروفایل در جدول teachers
                $this->teacherModel->updateProfileCompletion($teacher['id']);
                
                // ثبت فعالیت در سیستم
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
            
            // آماده‌سازی داده‌ها برای ثبت گروهی
            $batch_data = [];
            foreach ($_POST['attendance'] as $student_id => $status) {
                $batch_data[] = [
                    'teacher_id'     => $teacher['id'],
                    'student_id'     => $student_id,
                    'course_id'      => $selected_course,
                    'attendance_date'=> $selected_date,
                    'jalali_date'    => $jalali_date,
                    'status'         => $status,
                    'notes'          => $_POST['notes'][$student_id] ?? ''
                ];
            }
            
            // استفاده از ثبت گروهی
            if ($this->attendanceModel->recordBatchAttendance($batch_data)) {
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
                $this->redirect('teacher/attendance?course_id=' . $selected_course . '&class_id=' . $selected_class . '&date=' . urlencode($display_date));
            } else {
                $_SESSION['error'] = 'خطا در ثبت حضور و غیاب';
            }
        }
// در قسمت redirect بعد از ثبت
        
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
        
        // 🔥 استفاده از کش برای لیست دروس
        $courses = $this->getCachedData(
            "teacher_courses_{$teacher['id']}", 
            function() use ($teacher) {
                return $this->teacherModel->getTeacherCourses($teacher['id']);
            },
            3600 // 1 ساعت کش
        );
        
        $selected_course = $_GET['course_id'] ?? null;
        $selected_class = $_GET['class_id'] ?? null;
        $page = $_GET['page'] ?? 1;
        $limit = 50; // تعداد رکورد در هر صفحه
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $student_count = count($_POST['grades']);
            $batch_data = [];
            
            foreach ($_POST['grades'] as $student_id => $grade_data) {
                $batch_data[] = [
                    'teacher_id' => $teacher['id'],
                    'student_id' => $student_id,
                    'course_id' => $selected_course,
                    'course_type' => $_POST['course_type'],
                    'poodman1' => $grade_data['poodman1'] ?? null,
                    'poodman2' => $grade_data['poodman2'] ?? null,
                    'poodman3' => $grade_data['poodman3'] ?? null,
                    'poodman4' => $grade_data['poodman4'] ?? null,
                    'poodman5' => $grade_data['poodman5'] ?? null,
                    'continuous1' => $grade_data['continuous1'] ?? null,
                    'term1' => $grade_data['term1'] ?? null,
                    'continuous2' => $grade_data['continuous2'] ?? null,
                    'term2' => $grade_data['term2'] ?? null
                ];
            }
            
            // 🔥 استفاده از ثبت گروهی
            if ($this->gradeModel->recordBatchGrades($batch_data)) {
                // پاک کردن کش معدل دانش‌آموزان
                $this->clearStudentAveragesCache(array_keys($_POST['grades']));
                
                // ثبت فعالیت
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
            } else {
                $_SESSION['error'] = 'خطا در ثبت نمرات';
            }
        }
        
        $students = [];
        $existing_grades = [];
        $course_type = '';
        $total_pages = 1;
        $total_records = 0;
        
        if ($selected_course) {
            // 🔥 استفاده از pagination
            $students = $this->gradeModel->getStudentsForGrading($teacher['id'], $selected_course, $selected_class);
            $existing_grades = $this->gradeModel->getGradesByTeacher($teacher['id'], $selected_course, $selected_class, $limit, $page);
            
            // محاسبه pagination
            $total_records = $this->gradeModel->getGradesCount($teacher['id'], $selected_course, $selected_class);
            $total_pages = ceil($total_records / $limit);
            
            // دریافت نوع درس
            $course_info = $this->courseModel->getById($selected_course);
            $course_type = $course_info['course_type'];
        }
        
        $data = [
            'teacher' => $teacher,
            'courses' => $courses,
            'selected_course' => $selected_course,
            'selected_class' => $selected_class,
            'students' => $students,
            'existing_grades' => $existing_grades,
            'course_type' => $course_type,
            'current_page' => $page,
            'total_pages' => $total_pages,
            'total_records' => $total_records,
            'limit' => $limit,
            'user_name' => $_SESSION['first_name'] . ' ' . $_SESSION['last_name']
        ];
        
        $this->view('teacher/grades', $data);
    }
    protected function getCachedData($key, $callback, $ttl = 3600) {
        $cache_dir = 'cache/';
        if (!is_dir($cache_dir)) {
            mkdir($cache_dir, 0755, true);
        }
        
        $cache_file = $cache_dir . md5($key) . '.cache';
        
        // بررسی وجود کش معتبر
        if (file_exists($cache_file) && (time() - filemtime($cache_file)) < $ttl) {
            return unserialize(file_get_contents($cache_file));
        }
        
        // دریافت داده از دیتابیس
        $data = $callback();
        
        // ذخیره در کش
        file_put_contents($cache_file, serialize($data));
        
        return $data;
    }
    
    // 🔥 اضافه کردن متد برای پاک کردن کش معدل‌ها
    private function clearStudentAveragesCache($student_ids) {
        $cache_dir = 'cache/';
        foreach ($student_ids as $student_id) {
            $cache_file = $cache_dir . md5("student_avg_{$student_id}") . '.cache';
            if (file_exists($cache_file)) {
                unlink($cache_file);
            }
        }
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
    private function convertToEnglishNumbers($string) {
        $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $arabic = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        $english = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        
        $string = str_replace($persian, $english, $string);
        $string = str_replace($arabic, $english, $string);
        
        return $string;
    }
}
?>