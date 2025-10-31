<?php
class AssistantController extends Controller {
    
    public function __construct() {
        parent::__construct();
        if (!$this->isLoggedIn() || $_SESSION['role'] != 'assistant') {
            $this->redirect('auth/login');
        }
        
        $this->assistantModel = $this->model('Assistant');
        $this->assistantProfileModel = $this->model('AssistantProfile');
        $this->disciplinaryRecordModel = $this->model('DisciplinaryRecord');
        $this->disciplinaryScoreModel = $this->model('DisciplinaryScore');
        $this->studentModel = $this->model('Student');
        $this->gradeModel = $this->model('Grade');
        $this->classModel = $this->model('ClassModel');
        $this->messageLogModel = $this->model('MessageLog');
        
        // بررسی تکمیل پروفایل
        $this->checkProfileCompletion();
    }
    
    private function checkProfileCompletion() {
        $assistant = $this->assistantModel->getByUserId($_SESSION['user_id']);
        
        if ($assistant && !$assistant['profile_completed']) {
            $profile = $this->assistantProfileModel->getByAssistantId($assistant['id']);
            
            // اگر پروفایل کامل شده، آپدیت وضعیت
            if ($profile && $profile['profile_completed']) {
                $this->assistantModel->updateProfileCompletion($assistant['id']);
            } else {
                // اگر پروفایل کامل نشده و در صفحه پروفایل نیست، هدایت به پروفایل
                $current_url = $_SERVER['REQUEST_URI'];
                if (strpos($current_url, 'assistant/profile') === false) {
                    $this->redirect('assistant/profile');
                }
            }
        }
    }
    
    public function index() {
        $assistant = $this->assistantModel->getByUserId($_SESSION['user_id']);
        $profile = $this->assistantProfileModel->getByAssistantId($assistant['id']);
        
        // آمار کلی
        $students_count = count($this->assistantModel->getStudentsByGrade($assistant['grade_id']));
        $classes_count = count($this->assistantModel->getClassesByGrade($assistant['grade_id']));
        $today_absent_count = $this->getTodayAbsentCount($assistant['grade_id']);
        
        $data = [
            'assistant' => $assistant,
            'profile' => $profile,
            'students_count' => $students_count,
            'classes_count' => $classes_count,
            'today_absent_count' => $today_absent_count,
            'user_name' => $_SESSION['first_name'] . ' ' . $_SESSION['last_name']
        ];
        
        $this->view('assistant/dashboard', $data);
    }
    
    private function getTodayAbsentCount($grade_id) {
        $attendance = $this->assistantModel->getAttendanceByGrade($grade_id, date('Y-m-d'));
        $absent_count = 0;
        
        foreach ($attendance as $record) {
            if ($record['status'] == 'absent') {
                $absent_count++;
            }
        }
        
        return $absent_count;
    }
    
    public function profile() {
        $assistant = $this->assistantModel->getByUserId($_SESSION['user_id']);
        $profile = $this->assistantProfileModel->getByAssistantId($assistant['id']);
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // آپلود فایل‌ها
            $upload_dir = 'uploads/assistants/' . $assistant['id'] . '/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $profile_data = [
                'assistant_id' => $assistant['id'],
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
            
            if ($this->assistantProfileModel->createOrUpdate($profile_data)) {
                // آپدیت وضعیت تکمیل پروفایل در جدول assistants
                $this->assistantModel->updateProfileCompletion($assistant['id']);
                
                // 🔥 ثبت فعالیت در سیستم
                $this->logActivity(
                    'complete_profile', 
                    'تکمیل پروفایل معاون',
                    $assistant['id'],
                    'assistant_profiles'
                );
                
                $_SESSION['success'] = 'پروفایل با موفقیت تکمیل شد';
                $this->redirect('assistant');
            } else {
                $data['error'] = 'خطا در ذخیره اطلاعات پروفایل';
            }
        }
        
        $data['assistant'] = $assistant;
        $data['profile'] = $profile;
        $data['user_name'] = $_SESSION['first_name'] . ' ' . $_SESSION['last_name'];
        
        $this->view('assistant/profile', $data);
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
    
    public function students() {
        $assistant = $this->assistantModel->getByUserId($_SESSION['user_id']);
        $class_id = $_GET['class_id'] ?? null;
        
        $students = $this->assistantModel->getStudentsByGrade($assistant['grade_id'], $class_id);
        $classes = $this->assistantModel->getClassesByGrade($assistant['grade_id']);
        
        $data = [
            'assistant' => $assistant,
            'students' => $students,
            'classes' => $classes,
            'selected_class' => $class_id,
            'user_name' => $_SESSION['first_name'] . ' ' . $_SESSION['last_name']
        ];
        
        $this->view('assistant/students', $data);
    }
    
    public function studentDetail($student_id = null) {
        // اگر student_id از URL دریافت نشد، از GET بگیر
        if (!$student_id) {
            $student_id = $_GET['student_id'] ?? null;
        }
        
        if (!$student_id) {
            $this->redirect('assistant/students');
        }
        
        $assistant = $this->assistantModel->getByUserId($_SESSION['user_id']);
        $student = $this->studentModel->getById($student_id);
        
        if (!$student) {
            $this->redirect('assistant/students');
        }
        
        // بررسی اینکه دانش‌آموز در پایه تحت مسئولیت معاون باشد
        $student_with_details = $this->studentModel->getByUserId($student['user_id']);
        if ($student_with_details['grade_id'] != $assistant['grade_id']) {
            $this->redirect('assistant/students');
        }
        
        // اطلاعات پرونده انضباطی
        $disciplinary_records = $this->disciplinaryRecordModel->getByStudent($student_id);
        $disciplinary_score = $this->disciplinaryScoreModel->getByStudent($student_id);
        
        // اطلاعات حضور و غیاب (آخرین 30 رکورد)
        $attendance = $this->assistantModel->getAttendanceByGrade($assistant['grade_id']);
        $student_attendance = array_slice(array_filter($attendance, function($record) use ($student_id) {
            return $record['student_id'] == $student_id;
        }), 0, 30); // فقط 30 رکورد آخر
        
        // اطلاعات نمرات
        $grades = $this->assistantModel->getGradesByGrade($assistant['grade_id']);
        $student_grades = array_filter($grades, function($record) use ($student_id) {
            return $record['student_id'] == $student_id;
        });
        
        $data = [
            'assistant' => $assistant,
            'student' => $student_with_details,
            'disciplinary_records' => $disciplinary_records,
            'disciplinary_score' => $disciplinary_score,
            'student_attendance' => $student_attendance,
            'student_grades' => $student_grades,
            'user_name' => $_SESSION['first_name'] . ' ' . $_SESSION['last_name']
        ];
        
        $this->view('assistant/student_detail', $data);
    }


    public function disciplinary() {
        $assistant = $this->assistantModel->getByUserId($_SESSION['user_id']);
        
        // دریافت student_id از پارامتر GET
        $selected_student_id = $_GET['student_id'] ?? null;
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // استفاده از فیلد مخفی که تاریخ میلادی را دارد
            $violation_date_gregorian = $_POST['violation_date_gregorian'];
            $violation_date_jalali = $_POST['violation_date']; // تاریخ شمسی
            
            $disciplinary_data = [
                'student_id' => $_POST['student_id'],
                'assistant_id' => $assistant['id'],
                'violation_date' => $violation_date_gregorian, // استفاده از تاریخ میلادی
                'jalali_date' => $violation_date_jalali,      // استفاده از تاریخ شمسی
                'violation_type' => $_POST['violation_type'],
                'description' => $_POST['description'],
                'point_deduction' => $_POST['point_deduction'],
                'status' => 'approved'
            ];
            
            // بررسی معتبر بودن تاریخ میلادی
            if (empty($violation_date_gregorian) || $violation_date_gregorian === '0000-00-00') {
                $data['error'] = 'تاریخ انتخاب شده معتبر نیست. لطفا تاریخ را دوباره انتخاب کنید.';
            } else {
                if ($this->disciplinaryRecordModel->create($disciplinary_data)) {
                    // آپدیت نمره انضباطی
                    $total_deductions = $this->disciplinaryRecordModel->getTotalDeductions($_POST['student_id']);
                    $this->disciplinaryScoreModel->updateScore($_POST['student_id'], $total_deductions);
                    
                    // 🔥 ارسال پیام به اولیاء از طریق بله (منطق جدید جایگزین شده)
                    $message_sent = $this->sendMessageToParents($_POST['student_id'], $disciplinary_data);

                    // 🔥 ثبت فعالیت در سیستم
                    $student = $this->studentModel->getById($_POST['student_id']);
                    $student_name = 'نامشخص';
                    
                    if ($student) {
                        $student_details = $this->studentModel->getByUserId($student['user_id']);
                        if ($student_details) {
                            $student_name = $student_details['first_name'] . ' ' . $student_details['last_name'];
                        }
                    }
                    
                    $message_status = $message_sent ? " و پیام ارسال شد" : " اما ارسال پیام ناموفق بود";
                    
                    $this->logActivity(
                        'disciplinary_record', 
                        "ثبت مورد انضباطی برای دانش‌آموز {$student_name}: {$disciplinary_data['violation_type']} (کسر {$disciplinary_data['point_deduction']} نمره){$message_status}",
                        $this->db->lastInsertId(),
                        'disciplinary_records'
                    );
                    
                    if ($message_sent) {
                        $_SESSION['success'] = 'تخلف انضباطی با موفقیت ثبت شد و به اولیاء اطلاع رسانی گردید';
                    } else {
                        $_SESSION['success'] = 'تخلف انضباطی با موفقیت ثبت شد اما ارسال پیام برای اولیاء ناموفق بود';
                    }
                    
                    $this->redirect('assistant/disciplinary?student_id=' . $_POST['student_id']);
                } else {
                    $data['error'] = 'خطا در ثبت تخلف انضباطی';
                }
            }
        }
        
        // توجه: کد تکراری که قبلا در اینجا بود حذف شد.
        
        
        $students = $this->assistantModel->getStudentsByGrade($assistant['grade_id']);
        $disciplinary_records = $this->disciplinaryRecordModel->getByAssistant($assistant['id']);
        $disciplinary_scores = $this->disciplinaryScoreModel->getScoresByGrade($assistant['grade_id']);
        
        // اگر student_id مشخص شده، فقط تخلفات آن دانش‌آموز را نشان بده
        if ($selected_student_id) {
            $disciplinary_records = array_filter($disciplinary_records, function($record) use ($selected_student_id) {
                return $record['student_id'] == $selected_student_id;
            });
            
            // پیدا کردن نام دانش‌آموز انتخاب شده
            $selected_student = null;
            foreach ($students as $student) {
                if ($student['id'] == $selected_student_id) {
                    $selected_student = $student;
                    break;
                }
            }
        }
        
        $data = [
            'assistant' => $assistant,
            'students' => $students,
            'disciplinary_records' => $disciplinary_records,
            'disciplinary_scores' => $disciplinary_scores,
            'selected_student_id' => $selected_student_id,
            'selected_student' => $selected_student ?? null,
            'user_name' => $_SESSION['first_name'] . ' ' . $_SESSION['last_name']
        ];
        
        $this->view('assistant/disciplinary', $data);
    }
    // در کلاس AssistantController، بعد از متد disciplinary این متدها رو اضافه کنید:

/**
 * ویرایش مورد انضباطی
 */
public function editDisciplinary($record_id = null) {
    if (!$record_id) {
        $record_id = $_GET['record_id'] ?? null;
    }
    
    if (!$record_id) {
        $_SESSION['error'] = 'مورد انضباطی یافت نشد';
        $this->redirect('assistant/disciplinary');
    }
    
    $assistant = $this->assistantModel->getByUserId($_SESSION['user_id']);
    
    // بررسی مالکیت مورد انضباطی
    if (!$this->disciplinaryRecordModel->isOwnedByAssistant($record_id, $assistant['id'])) {
        $_SESSION['error'] = 'شما مجوز ویرایش این مورد انضباطی را ندارید';
        $this->redirect('assistant/disciplinary');
    }
    
    // دریافت اطلاعات مورد انضباطی
    $record = $this->disciplinaryRecordModel->getByIdWithDetails($record_id);
    if (!$record) {
        $_SESSION['error'] = 'مورد انضباطی یافت نشد';
        $this->redirect('assistant/disciplinary');
    }
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $old_deduction = $record['point_deduction']; // نمره کسر شده قبلی
        
        $disciplinary_data = [
            'violation_date' => $_POST['violation_date_gregorian'],
            'jalali_date' => $_POST['violation_date'],
            'violation_type' => $_POST['violation_type'],
            'description' => $_POST['description'],
            'point_deduction' => $_POST['point_deduction'],
            'status' => 'approved'
        ];
        
        if ($this->disciplinaryRecordModel->updateRecord($record_id, $disciplinary_data)) {
            // اگر نمره کسر شده تغییر کرده، نمره انضباطی رو آپدیت کن
            if ($old_deduction != $disciplinary_data['point_deduction']) {
                $total_deductions = $this->disciplinaryRecordModel->getTotalDeductions($record['student_id']);
                $this->disciplinaryScoreModel->updateScore($record['student_id'], $total_deductions);
            }
            
            // ثبت فعالیت
            $this->logActivity(
                'disciplinary_edit', 
                "ویرایش مورد انضباطی برای دانش‌آموز {$record['first_name']} {$record['last_name']}: {$disciplinary_data['violation_type']} (کسر {$disciplinary_data['point_deduction']} نمره)",
                $record_id,
                'disciplinary_records'
            );
            
            $_SESSION['success'] = 'مورد انضباطی با موفقیت ویرایش شد';
            $this->redirect('assistant/disciplinary?student_id=' . $record['student_id']);
        } else {
            $data['error'] = 'خطا در ویرایش مورد انضباطی';
        }
    }
    
    // دریافت لیست دانش‌آموزان برای نمایش
    $students = $this->assistantModel->getStudentsByGrade($assistant['grade_id']);
    
    $data = [
        'assistant' => $assistant,
        'record' => $record,
        'students' => $students,
        'user_name' => $_SESSION['first_name'] . ' ' . $_SESSION['last_name']
    ];
    
    $this->view('assistant/edit_disciplinary', $data);
}

/**
 * حذف مورد انضباطی
 */
public function deleteDisciplinary($record_id = null) {
    if (!$record_id) {
        $record_id = $_GET['record_id'] ?? null;
    }
    
    if (!$record_id) {
        $_SESSION['error'] = 'مورد انضباطی یافت نشد';
        $this->redirect('assistant/disciplinary');
    }
    
    $assistant = $this->assistantModel->getByUserId($_SESSION['user_id']);
    
    // بررسی مالکیت مورد انضباطی
    if (!$this->disciplinaryRecordModel->isOwnedByAssistant($record_id, $assistant['id'])) {
        $_SESSION['error'] = 'شما مجوز حذف این مورد انضباطی را ندارید';
        $this->redirect('assistant/disciplinary');
    }
    
    // دریافت اطلاعات مورد انضباطی قبل از حذف
    $record = $this->disciplinaryRecordModel->getByIdWithDetails($record_id);
    if (!$record) {
        $_SESSION['error'] = 'مورد انضباطی یافت نشد';
        $this->redirect('assistant/disciplinary');
    }
    
    // حذف مورد انضباطی
    if ($this->disciplinaryRecordModel->deleteRecord($record_id)) {
        // آپدیت نمره انضباطی
        $total_deductions = $this->disciplinaryRecordModel->getTotalDeductions($record['student_id']);
        $this->disciplinaryScoreModel->updateScore($record['student_id'], $total_deductions);
        
        // ثبت فعالیت
        $this->logActivity(
            'disciplinary_delete', 
            "حذف مورد انضباطی برای دانش‌آموز {$record['first_name']} {$record['last_name']}: {$record['violation_type']}",
            $record_id,
            'disciplinary_records'
        );
        
        $_SESSION['success'] = 'مورد انضباطی با موفقیت حذف شد';
    } else {
        $_SESSION['error'] = 'خطا در حذف مورد انضباطی';
    }
    
    $this->redirect('assistant/disciplinary?student_id=' . $record['student_id']);
}
    public function attendance() {
        $assistant = $this->assistantModel->getByUserId($_SESSION['user_id']);
        
        // پارامترهای فیلتر
        $selected_date = $_GET['date'] ?? '';
        $selected_class = $_GET['class_id'] ?? '';
        $start_date = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
        $end_date = $_GET['end_date'] ?? date('Y-m-d');
        $view_type = $_GET['view'] ?? 'daily'; // daily, weekly, monthly
        
        // دریافت داده‌ها بر اساس نوع نمایش
        if ($view_type === 'daily' && $selected_date) {
            // نمایش روزانه
            $attendance = $this->assistantModel->getAttendanceByGrade($assistant['grade_id'], $selected_date, $selected_class);
        } else {
            // نمایش کلی در بازه زمانی
            $attendance = $this->assistantModel->getCompleteAttendanceByGrade(
                $assistant['grade_id'], 
                $start_date, 
                $end_date, 
                $selected_class
            );
        }
        
        // دریافت آمار
        $attendance_stats = $this->assistantModel->getAttendanceStatistics($assistant['grade_id'], $start_date, $end_date);
        $absent_students = $this->assistantModel->getAbsentStudents($assistant['grade_id'], $start_date, $end_date);
        $attendance_days = $this->assistantModel->getAttendanceDays($assistant['grade_id'], $start_date, $end_date);
        
        // گروه‌بندی داده‌ها
        $attendance_by_date = [];
        $attendance_by_class = [];
        $attendance_by_student = [];
        
        foreach ($attendance as $record) {
            // گروه‌بندی بر اساس تاریخ
            $date_key = $record['attendance_date'];
            if (!isset($attendance_by_date[$date_key])) {
                $attendance_by_date[$date_key] = [];
            }
            $attendance_by_date[$date_key][] = $record;
            
            // گروه‌بندی بر اساس کلاس
            $class_key = $record['class_name'];
            if (!isset($attendance_by_class[$class_key])) {
                $attendance_by_class[$class_key] = [];
            }
            $attendance_by_class[$class_key][] = $record;
            
            // گروه‌بندی بر اساس دانش‌آموز
            $student_key = $record['student_id'];
            if (!isset($attendance_by_student[$student_key])) {
                $attendance_by_student[$student_key] = [
                    'student_info' => [
                        'first_name' => $record['first_name'],
                        'last_name' => $record['last_name'],
                        'student_number' => $record['student_number'],
                        'class_name' => $record['class_name']
                    ],
                    'records' => []
                ];
            }
            $attendance_by_student[$student_key]['records'][] = $record;
        }
        
        // دریافت لیست کلاس‌ها
        $classes = $this->assistantModel->getClassesByGrade($assistant['grade_id']);
        
        $data = [
            'assistant' => $assistant,
            'attendance' => $attendance,
            'attendance_by_date' => $attendance_by_date,
            'attendance_by_class' => $attendance_by_class,
            'attendance_by_student' => $attendance_by_student,
            'attendance_stats' => $attendance_stats,
            'absent_students' => $absent_students,
            'attendance_days' => $attendance_days,
            'classes' => $classes,
            'selected_date' => $selected_date,
            'selected_class' => $selected_class,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'view_type' => $view_type,
            'user_name' => $_SESSION['first_name'] . ' ' . $_SESSION['last_name']
        ];
        
        $this->view('assistant/attendance', $data);
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
            
            // 🔥 محاسبه نمره هر درس و اضافه کردن به رکورد
            $record['course_grade'] = $this->calculateCourseGrade($record);
            $grades_by_student[$student_id]['courses'][] = $record;
        }
        
        // 🔥 محاسبه معدل هر دانش‌آموز
        foreach ($grades_by_student as &$student_data) {
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
    public function getStudentReportCard($student_id) {
        $assistant = $this->assistantModel->getByUserId($_SESSION['user_id']);
        
        // دریافت اطلاعات دانش‌آموز
        $student = $this->studentModel->getById($student_id);
        $student_details = $this->studentModel->getByUserId($student['user_id']);
        
        // بررسی دسترسی
        if ($student_details['grade_id'] != $assistant['grade_id']) {
            die('دسترسی غیر مجاز');
        }
        
        // دریافت نمرات دانش‌آموز
        $grades = $this->gradeModel->getGradesByStudent($student_id);
        
        // تفکیک دروس
        $poodmani_courses = array_filter($grades, fn($g) => $g['course_type'] == 'poodmani');
        $non_poodmani_courses = array_filter($grades, fn($g) => $g['course_type'] != 'poodmani');
        
        $data = [
            'student' => $student_details,
            'poodmani_courses' => $poodmani_courses,
            'non_poodmani_courses' => $non_poodmani_courses
        ];
        
        $this->view('assistant/partials/report_card', $data, false); // false برای عدم استفاده از layout
    }
    // 🔥 این متد در کنترلر می‌ماند (منطق business)
    private function calculateGradeAverage($grade) {
        if ($grade['course_type'] == 'poodmani') {
            // محاسبه معدل برای دروس پودمانی
            $poodman_grades = [];
            for ($i = 1; $i <= 5; $i++) {
                if (isset($grade['poodman' . $i]) && $grade['poodman' . $i] !== null) {
                    $poodman_grades[] = $grade['poodman' . $i];
                }
            }
            return !empty($poodman_grades) ? round(array_sum($poodman_grades) / count($poodman_grades), 2) : null;
        } else {
            // محاسبه معدل برای دروس غیر پودمانی
            $term_grades = [];
            if (isset($grade['term1']) && $grade['term1'] !== null) {
                $term_grades[] = $grade['term1'];
            }
            if (isset($grade['term2']) && $grade['term2'] !== null) {
                $term_grades[] = $grade['term2'];
            }
            return !empty($term_grades) ? round(array_sum($term_grades) / count($term_grades), 2) : null;
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

    // در کلاس AssistantController اضافه کنید:
    private function calculateCourseGrade($course) {
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
            $term1 = (($course['continuous1'] ?? 0) + ($course['term1'] ?? 0)) / 2;
            $term2 = (($course['continuous2'] ?? 0) + ($course['term2'] ?? 0)) / 2;
            return ($term1 + $term2) / 2;
        }
    }

    private function calculateStudentAverage($courses) {
        $total_grade = 0;
        $course_count = 0;
        
        foreach ($courses as $course) {
            $course_grade = $this->calculateCourseGrade($course);
            if ($course_grade > 0) {
                $total_grade += $course_grade;
                $course_count++;
            }
        }
        
        return $course_count > 0 ? $total_grade / $course_count : 0;
    }

    public function teacherRecords() {
        $assistant = $this->assistantModel->getByUserId($_SESSION['user_id']);
        
        // دریافت رکوردهای معلمان پایه تحت مدیریت این معاون
        $staff_records = $this->model('StaffRecord')->getTeacherRecordsForAssistant($assistant['grade_id']);
        
        // دریافت امتیازات معلمان
        $staff_scores = $this->model('StaffScore')->getTeachersScoresForAssistant($assistant['grade_id']);
        
        $data = [
            'assistant' => $assistant,
            'staff_records' => $staff_records,
            'staff_scores' => $staff_scores,
            'user_name' => $_SESSION['first_name'] . ' ' . $_SESSION['last_name']
        ];
        
        $this->view('assistant/teacher_records', $data);
    }
    // در کلاس AssistantController این متد رو اضافه کنید:


private function sendMessageToParents($student_id, $disciplinary_data) {
    try {
        // دریافت اطلاعات والدین
        $parentModel = $this->model('ParentModel');
        $parents = $parentModel->getByStudentId($student_id);
        
        if (empty($parents)) {
            // 🔥 ثبت فعالیت - عدم وجود والدین
            $this->logActivity(
                'message_send_failed', 
                "ارسال پیام ناموفق: والدینی برای دانش‌آموز پیدا نشد",
                $student_id,
                'students'
            );
            return false;
        }
        
        // دریافت اطلاعات دانش‌آموز
        $student = $this->studentModel->getById($student_id);
        $student_details = $this->studentModel->getByUserId($student['user_id']);
        
        // ایجاد متن پیام
        $message_text = $this->createMessageText($student_details, $disciplinary_data);
        
        // ارسال پیام به هر یک از والدین
        $baleMessenger = new BaleMessenger('360698616:jlQfKPAKUeOfzoD3foxlaYuIWXI_l-RT4mM');
        
        $disciplinary_record_id = $this->db->lastInsertId();
        $sent_count = 0;
        $failed_count = 0;
        
        foreach ($parents as $parent) {
            // دریافت chat_id والد از جدول users
            $userModel = $this->model('User');
            $parent_user = $userModel->getById($parent['user_id']);
            
            if ($parent_user && !empty($parent_user['bale_chat_id'])) {
                // ارسال پیام
                $result = $baleMessenger->sendMessage($parent_user['bale_chat_id'], $message_text);
                
                // ذخیره لاگ پیام
                $log_data = [
                    'disciplinary_record_id' => $disciplinary_record_id,
                    'parent_id' => $parent['id'],
                    'message_text' => $message_text,
                    'bale_message_id' => $result['success'] ? $result['message_id'] : null,
                    'status' => $result['success'] ? 'sent' : 'failed',
                    'sent_at' => $result['success'] ? date('Y-m-d H:i:s') : null
                ];
                
                $this->messageLogModel->create($log_data);
                
                if ($result['success']) {
                    $sent_count++;
                    // 🔥 ثبت فعالیت - ارسال موفق
                    $this->logActivity(
                        'message_sent', 
                        "پیام انضباطی با موفقیت برای {$parent_user['first_name']} {$parent_user['last_name']} ارسال شد",
                        $disciplinary_record_id,
                        'message_logs'
                    );
                } else {
                    $failed_count++;
                    // 🔥 ثبت فعالیت - ارسال ناموفق
                    $this->logActivity(
                        'message_send_failed', 
                        "ارسال پیام برای {$parent_user['first_name']} {$parent_user['last_name']} ناموفق بود",
                        $disciplinary_record_id,
                        'message_logs'
                    );
                }
            } else {
                $failed_count++;
                // 🔥 ثبت فعالیت - عدم وجود chat_id
                $this->logActivity(
                    'message_send_failed', 
                    "chat_id برای والد {$parent_user['first_name']} {$parent_user['last_name']} تنظیم نشده",
                    $parent['user_id'],
                    'users'
                );
            }
        }
        
        // 🔥 ثبت فعالیت - خلاصه ارسال
        $this->logActivity(
            'message_summary', 
            "ارسال پیام انضباطی: {$sent_count} موفق، {$failed_count} ناموفق",
            $disciplinary_record_id,
            'disciplinary_records'
        );
        
        return $sent_count > 0;
        
    } catch (Exception $e) {
        // در صورت خطا، فقط لاگ کنیم و ادامه بدیم
        error_log("Error sending message to parents: " . $e->getMessage());
        
        // 🔥 ثبت فعالیت - خطای سیستمی
        $this->logActivity(
            'message_system_error', 
            "خطای سیستمی در ارسال پیام: " . $e->getMessage(),
            $student_id,
            'students'
        );
        
        return false;
    }
}

private function createMessageText($student, $disciplinary_data) {
    $text = "📢 <b>اطلاعیه انضباطی - هنرستان امام صادق (ع)</b>\n\n";
    $text .= "👤 <b>دانش‌آموز:</b> {$student['first_name']} {$student['last_name']}\n";
    $text .= "🔢 <b>شماره دانش‌آموزی:</b> {$student['student_number']}\n";
    $text .= "🎯 <b>نوع تخلف:</b> {$disciplinary_data['violation_type']}\n";
    $text .= "📅 <b>تاریخ:</b> {$disciplinary_data['jalali_date']}\n";
    $text .= "📝 <b>شرح تخلف:</b>\n{$disciplinary_data['description']}\n";
    $text .= "⚠️ <b>نمره کسر شده:</b> {$disciplinary_data['point_deduction']}\n\n";
    $text .= "🔔 <i>لطفاً جهت پیگیری و دریافت اطلاعات بیشتر با واحد انضباطی هنرستان تماس حاصل فرمایید.</i>\n\n";
    $text .= "📞 <b>تلفن هنرستان:</b> ۰۲۱-۱۲۳۴۵۶۷۸\n";
    $text .= "🏫 <b>هنرستان فنی و حرفه‌ای امام صادق (ع)</b>";
    
    return $text;
}


public function exportStudentsPDF($class_id = null) {
    // پاک کردن هرگونه خروجی قبلی
    if (ob_get_length()) {
        ob_end_clean();
    }
    
    if (!$class_id) {
        $class_id = $_GET['class_id'] ?? null;
    }
    
    if (!$class_id) {
        $_SESSION['error'] = 'کلاس مشخص نشده است';
        $this->redirect('assistant/students');
    }
    
    try {
        $assistant = $this->assistantModel->getByUserId($_SESSION['user_id']);
        
        // بررسی اینکه کلاس متعلق به پایه معاون باشد
        $class = $this->classModel->getById($class_id);
        if (!$class || $class['grade_id'] != $assistant['grade_id']) {
            $_SESSION['error'] = 'شما دسترسی به این کلاس را ندارید';
            $this->redirect('assistant/students');
        }
        
        // دریافت دانش‌آموزان کلاس
        $students = $this->studentModel->getStudentsByClass($class_id);
        
        // اطلاعات کلاس
        $class_info = [
            'name' => $class['name'],
            'grade_name' => $class['grade_name'],
            'major_name' => $class['major_name'],
            'student_count' => count($students)
        ];
        
        // هدر جدول - به ترتیب راست به چپ
        $header = array(
            'ردیف',
            'شماره دانش‌آموزی', 
            'نام و نام خانوادگی',
            'نام پدر',
            'توضیحات'
        );
        
        // ایجاد PDF
        $pdf = new PDFHelper();
        $pdf->AddPage();
        
        // ایجاد جدول
        $pdf->createStudentsTable($header, $students, $class_info);
        
        // اطلاعات پایانی
        $assistant_name = $_SESSION['first_name'] . ' ' . $_SESSION['last_name'];
        $pdf->addFooterInfo($assistant_name);
        
        // پاک کردن بافر خروجی و ارسال PDF
        $pdf->cleanOutput();
        
        // خروجی
        $filename = 'students_' . $class['name'] . '_' . date('Y-m-d') . '.pdf';
        $pdf->Output($filename, 'I');
        
        // ثبت فعالیت
        $this->logActivity(
            'export_pdf', 
            "خروجی PDF از لیست دانش‌آموزان کلاس {$class['name']}",
            $class_id,
            'classes'
        );
        
        exit;
        
    } catch (Exception $e) {
        // پاک کردن بافر در صورت خطا
        if (ob_get_length()) {
            ob_end_clean();
        }
        $_SESSION['error'] = 'خطا در ایجاد فایل PDF: ' . $e->getMessage();
        $this->redirect('assistant/students');
    }
}

/**
 * خروجی Excel از لیست دانش‌آموزان
 */
public function exportStudentsExcel($class_id = null) {
    if (!$class_id) {
        $class_id = $_GET['class_id'] ?? null;
    }
    
    if (!$class_id) {
        $_SESSION['error'] = 'کلاس مشخص نشده است';
        $this->redirect('assistant/students');
    }
    
    try {
        $assistant = $this->assistantModel->getByUserId($_SESSION['user_id']);
        
        // بررسی دسترسی
        $class = $this->classModel->getById($class_id);
        if (!$class || $class['grade_id'] != $assistant['grade_id']) {
            $_SESSION['error'] = 'شما دسترسی به این کلاس را ندارید';
            $this->redirect('assistant/students');
        }
        
        // دریافت دانش‌آموزان
        $students = $this->studentModel->getStudentsByClass($class_id);
        
        // هدر فایل Excel
        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="students_class_' . $class_id . '_' . date('Y-m-d') . '.xls"');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        // محتوای Excel
        echo "<html dir='rtl'>";
        echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>";
        echo "<table border='1'>";
        echo "<tr><th colspan='6'>لیست دانش‌آموزان کلاس " . $class['name'] . " - " . $class['major_name'] . " - پایه " . $class['grade_name'] . "</th></tr>";
        echo "<tr style='background-color: #f0f0f0; font-weight: bold;'>";
        echo "<th>ردیف</th>";
        echo "<th>نام و نام خانوادگی</th>";
        echo "<th>شماره دانش‌آموزی</th>";
        echo "<th>نام پدر</th>";
        echo "<th>موبایل</th>";
        echo "<th>تاریخ تولد</th>";
        echo "</tr>";
        
        $counter = 1;
        foreach ($students as $student) {
            echo "<tr>";
            echo "<td>" . $counter . "</td>";
            echo "<td>" . $student['first_name'] . " " . $student['last_name'] . "</td>";
            echo "<td>" . $student['student_number'] . "</td>";
            echo "<td>" . ($student['father_name'] ?? '-') . "</td>";
            echo "<td>" . $student['mobile'] . "</td>";
            echo "<td>" . ($student['birth_date'] ?? '-') . "</td>";
            echo "</tr>";
            $counter++;
        }
        
        echo "</table>";
        echo "<p>تعداد دانش‌آموزان: " . count($students) . "</p>";
        echo "<p>تاریخ استخراج: " . JalaliDate::now('Y/m/d') . "</p>";
        echo "<p>معاون: " . $_SESSION['first_name'] . " " . $_SESSION['last_name'] . "</p>";
        echo "</html>";
        
        // ثبت فعالیت
        $this->logActivity(
            'export_excel', 
            "خروجی Excel از لیست دانش‌آموزان کلاس {$class['name']}",
            $class_id,
            'classes'
        );
        
        exit;
        
    } catch (Exception $e) {
        $_SESSION['error'] = 'خطا در ایجاد فایل Excel: ' . $e->getMessage();
        $this->redirect('assistant/students');
    }
}

}
?>