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
        $this->classModel = $this->model('User');

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
            // دیباگ داده‌های دریافتی
            error_log('=== ASSISTANT POST DATA DEBUG ===');
            error_log('Birth Date: ' . ($_POST['birth_date'] ?? 'NOT SET'));
            error_log('Is Retired: ' . ($_POST['is_retired'] ?? 'NOT SET'));
            error_log('All POST data: ' . print_r($_POST, true));
            
            // آپلود فایل‌ها
            $upload_dir = 'uploads/assistants/' . $assistant['id'] . '/';
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
            
            $profile_data = [
                'assistant_id' => $assistant['id'],
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
                'is_retired' => isset($_POST['is_retired']) ? 1 : 0,
                'profile_completed' => 1
            ];
            
            error_log('Assistant profile data to save: ' . print_r($profile_data, true));
            
            // آپلود عکس پروفایل (اجباری)
            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
                $profile_image = $this->uploadFile($_FILES['profile_image'], $upload_dir, 'profile');
                if ($profile_image) {
                    $profile_data['profile_image'] = $profile_image;
                }
            } elseif (!empty($profile['profile_image'])) {
                // اگر فایل جدید آپلود نشده، فایل قبلی رو نگه دار
                $profile_data['profile_image'] = $profile['profile_image'];
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
                } elseif (!empty($profile[$field])) {
                    // اگر فایل جدید آپلود نشده، فایل قبلی رو نگه دار
                    $profile_data[$field] = $profile[$field];
                }
            }
            
            // آپلود فایل سوابق (اگر ارسال شده)
            if (isset($_FILES['resume_file']) && $_FILES['resume_file']['error'] == 0) {
                $resume_file = $this->uploadFile($_FILES['resume_file'], $upload_dir, 'resume');
                if ($resume_file) {
                    $profile_data['resume_file'] = $resume_file;
                }
            } elseif (!empty($profile['resume_file'])) {
                // اگر فایل جدید آپلود نشده، فایل قبلی رو نگه دار
                $profile_data['resume_file'] = $profile['resume_file'];
            }
            
            // مدیریت حذف فایل‌ها
            $remove_fields = ['profile_image', 'national_card_image', 'birth_certificate_image', 'decree_file', 'resume_file'];
            foreach ($remove_fields as $field) {
                if (isset($_POST['remove_' . $field]) && $_POST['remove_' . $field] == '1') {
                    // حذف فایل از سرور
                    if (!empty($profile[$field]) && file_exists('uploads/assistants/' . $assistant['id'] . '/' . $profile[$field])) {
                        unlink('uploads/assistants/' . $assistant['id'] . '/' . $profile[$field]);
                    }
                    // حذف از داده‌های پروفایل
                    $profile_data[$field] = null;
                }
            }
            
            if ($this->assistantProfileModel->createOrUpdate($profile_data)) {
                // آپدیت وضعیت تکمیل پروفایل در جدول assistants
                $this->assistantModel->updateProfileCompletion($assistant['id']);
                
                // ثبت فعالیت در سیستم
                $this->logActivity(
                    'complete_profile', 
                    'تکمیل پروفایل معاون',
                    $assistant['id'],
                    'assistant_profiles'
                );
                
                $_SESSION['success'] = 'پروفایل با موفقیت تکمیل شد';
                
                // رفرش داده‌های پروفایل
                $profile = $this->assistantProfileModel->getByAssistantId($assistant['id']);
            } else {
                $data['error'] = 'خطا در ذخیره اطلاعات پروفایل';
            }
        }
        
        $data['assistant'] = $assistant;
        $data['profile'] = $profile;
        $data['user_name'] = $_SESSION['first_name'] . ' ' . $_SESSION['last_name'];
        
        // دیباگ داده‌های پروفایل
        error_log('Assistant profile data: ' . print_r($profile, true));
        
        $this->view('assistant/profile', $data);
    }
    
    // تابع uploadFile برای معاونین (همانند معلمان)
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
    
    // تابع compressImage برای معاونین (همانند معلمان)
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
    
    // تابع تبدیل اعداد فارسی به انگلیسی
    private function convertToEnglishNumbers($string) {
        $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $arabic = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        $english = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        
        $string = str_replace($persian, $english, $string);
        $string = str_replace($arabic, $english, $string);
        
        return $string;
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
    $view_type = $_GET['view'] ?? 'daily';
    $page = $_GET['page'] ?? 1;
    $limit = 50; // تعداد رکورد در هر صفحه
    
    // 🔥 استفاده از کش برای داده‌های ثابت
    $classes = $this->getCachedData(
        "classes_grade_{$assistant['grade_id']}", 
        function() use ($assistant) {
            return $this->assistantModel->getClassesByGrade($assistant['grade_id']);
        },
        3600 // 1 ساعت کش
    );
    
    // دریافت داده‌ها بر اساس نوع نمایش
    if ($view_type === 'daily' && $selected_date) {
        // 🔥 استفاده از pagination برای نمایش روزانه
        $attendance = $this->assistantModel->getAttendanceByGradeWithPagination(
            $assistant['grade_id'], 
            $selected_date, 
            $selected_class,
            $limit,
            $page
        );
        
        // محاسبه تعداد کل رکوردها برای pagination
        $total_records = $this->assistantModel->getAttendanceCount(
            $assistant['grade_id'], 
            $selected_date, 
            $selected_date, 
            $selected_class
        );
        $total_pages = ceil($total_records / $limit);
    } else {
        // نمایش کلی در بازه زمانی
        $attendance = $this->assistantModel->getCompleteAttendanceByGrade(
            $assistant['grade_id'], 
            $start_date, 
            $end_date, 
            $selected_class
        );
        $total_pages = 1;
        $total_records = count($attendance);
    }
    
    // 🔥 استفاده از کش برای آمار
    $attendance_stats = $this->assistantModel->getAttendanceStatistics(
        $assistant['grade_id'], 
        $start_date, 
        $end_date
    );
    
    $absent_students = $this->assistantModel->getAbsentStudents($assistant['grade_id'], $start_date, $end_date);
    $attendance_days = $this->assistantModel->getAttendanceDays($assistant['grade_id'], $start_date, $end_date);
    
    // گروه‌بندی داده‌ها (فقط اگر داده کم است)
    $attendance_by_date = [];
    $attendance_by_class = [];
    
    if (count($attendance) <= 1000) { // فقط برای داده‌های کم
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
        }
    }
    
    $data = [
        'assistant' => $assistant,
        'attendance' => $attendance,
        'attendance_by_date' => $attendance_by_date,
        'attendance_by_class' => $attendance_by_class,
        'attendance_stats' => $attendance_stats,
        'absent_students' => $absent_students,
        'attendance_days' => $attendance_days,
        'classes' => $classes,
        'selected_date' => $selected_date,
        'selected_class' => $selected_class,
        'start_date' => $start_date,
        'end_date' => $end_date,
        'view_type' => $view_type,
        'current_page' => $page,
        'total_pages' => $total_pages,
        'total_records' => $total_records,
        'limit' => $limit,
        'user_name' => $_SESSION['first_name'] . ' ' . $_SESSION['last_name']
    ];
    
    $this->view('assistant/attendance', $data);
}
    // 🔥 اضافه کردن این متد برای کشینگ
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
public function grades() {
    $assistant = $this->assistantModel->getByUserId($_SESSION['user_id']);
    
    // دریافت نمرات
    $grades = $this->assistantModel->getGradesByGrade($assistant['grade_id']);
    
    // گروه‌بندی نمرات بر اساس کلاس و دانش‌آموز
    $grades_by_student = [];
    $grades_by_class = [];
    
    foreach ($grades as $record) {
        $student_id = $record['student_id'];
        $class_name = $record['class_name'];
        
        // گروه‌بندی بر اساس دانش‌آموز
        if (!isset($grades_by_student[$student_id])) {
            $grades_by_student[$student_id] = [
                'student_info' => [
                    'first_name' => $record['first_name'],
                    'last_name' => $record['last_name'],
                    'student_number' => $record['student_number'],
                    'class_name' => $record['class_name']
                ],
                'courses' => [],
                'poodmani_courses' => [],
                'non_poodmani_courses' => []
            ];
        }
        
        // محاسبه نمره هر درس
        $record['course_grade'] = $this->calculateCourseGrade($record);
        $grades_by_student[$student_id]['courses'][] = $record;
        
        // تفکیک دروس پودمانی و غیر پودمانی
        if ($record['course_type'] == 'poodmani') {
            $grades_by_student[$student_id]['poodmani_courses'][] = $record;
        } else {
            $grades_by_student[$student_id]['non_poodmani_courses'][] = $record;
        }
        
        // گروه‌بندی بر اساس کلاس
        if (!isset($grades_by_class[$class_name])) {
            $grades_by_class[$class_name] = [];
        }
        $grades_by_class[$class_name][] = $record;
    }
    
    // محاسبه معدل هر دانش‌آموز
    foreach ($grades_by_student as &$student_data) {
        $student_data['average'] = $this->calculateStudentAverage($student_data['courses']);
        
        // 🔥 محاسبه معدل جداگانه برای دروس پودمانی و غیر پودمانی
        $student_data['poodmani_average'] = $this->calculateStudentAverage($student_data['poodmani_courses']);
        $student_data['non_poodmani_average'] = $this->calculateStudentAverage($student_data['non_poodmani_courses']);
        
        // 🔥 تعداد دروس گذرانده شده
        $student_data['total_courses'] = count($student_data['courses']);
        $student_data['passed_courses'] = count(array_filter($student_data['courses'], function($course) {
            return $course['course_grade'] >= 10;
        }));
    }
    
    // 🔥 آمار کلی
    $stats = [
        'total_students' => count($grades_by_student),
        'total_courses' => count($grades),
        'average_grade' => 0,
        'success_rate' => 0
    ];
    
    $total_grade_sum = 0;
    $total_passed = 0;
    $total_courses = 0;
    
    foreach ($grades_by_student as $student) {
        foreach ($student['courses'] as $course) {
            if ($course['course_grade'] > 0) {
                $total_grade_sum += $course['course_grade'];
                $total_courses++;
                
                if ($course['course_grade'] >= 10) {
                    $total_passed++;
                }
            }
        }
    }
    
    if ($total_courses > 0) {
        $stats['average_grade'] = round($total_grade_sum / $total_courses, 2);
        $stats['success_rate'] = round(($total_passed / $total_courses) * 100, 2);
    }
    
    $data = [
        'assistant' => $assistant,
        'grades' => $grades,
        'grades_by_student' => $grades_by_student,
        'grades_by_class' => $grades_by_class,
        'stats' => $stats,
        'user_name' => $_SESSION['first_name'] . ' ' . $_SESSION['last_name']
    ];
    
    $this->view('assistant/grades', $data);
}

private function calculateStudentAverage($courses) {
    $total_grade = 0;
    $course_count = 0;
    
    foreach ($courses as $course) {
        $course_grade = $this->calculateCourseGrade($course);
        
        // 🔥 فقط دروسی که نمره معتبر دارند رو محاسبه کن
        if ($course_grade > 0 && $course_grade !== null) {
            $total_grade += $course_grade;
            $course_count++;
        }
    }
    
    return $course_count > 0 ? round($total_grade / $course_count, 2) : 0;
}

// متد calculateCourseGrade که از قبل داری رو نگه دار
// 🔥 این متد رو در AssistantController پیدا کن و با نسخه جدید جایگزین کن
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
        
        // فقط نمرات معتبر (بزرگتر از 0) رو در نظر بگیر
        $valid_grades = array_filter($grades, function($g) { 
            return $g > 0 && $g !== null; 
        });
        
        return count($valid_grades) > 0 ? round(array_sum($valid_grades) / count($valid_grades), 2) : 0;
        
    } else {
        // 🔥 محاسبه صحیح برای دروس غیر پودمانی
        $term1_continuous = $course['continuous1'] ?? 0;
        $term1_final = $course['term1'] ?? 0;
        $term2_continuous = $course['continuous2'] ?? 0;
        $term2_final = $course['term2'] ?? 0;
        
        // محاسبه نمره نیمسال اول
        $term1_grade = 0;
        $term1_count = 0;
        
        if ($term1_continuous > 0) {
            $term1_grade += $term1_continuous;
            $term1_count++;
        }
        if ($term1_final > 0) {
            $term1_grade += $term1_final;
            $term1_count++;
        }
        $term1_average = ($term1_count > 0) ? ($term1_grade / $term1_count) : 0;
        
        // محاسبه نمره نیمسال دوم
        $term2_grade = 0;
        $term2_count = 0;
        
        if ($term2_continuous > 0) {
            $term2_grade += $term2_continuous;
            $term2_count++;
        }
        if ($term2_final > 0) {
            $term2_grade += $term2_final;
            $term2_count++;
        }
        $term2_average = ($term2_count > 0) ? ($term2_grade / $term2_count) : 0;
        
        // محاسبه نمره نهایی (میانگین دو نیمسال)
        if ($term1_average > 0 && $term2_average > 0) {
            return round(($term1_average + $term2_average) / 2, 2);
        } elseif ($term1_average > 0) {
            return round($term1_average, 2);
        } elseif ($term2_average > 0) {
            return round($term2_average, 2);
        } else {
            return 0;
        }
    }
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
    
    // 🔥 تابع محاسبه نمره هر درس - نسخه تصحیح شده
    function calculateCourseGrade($course) {
        if ($course['course_type'] == 'poodmani') {
            // محاسبه برای دروس پودمانی
            $poodman_grades = [];
            for ($i = 1; $i <= 5; $i++) {
                $grade_field = 'poodman' . $i;
                if (isset($course[$grade_field]) && $course[$grade_field] !== null && $course[$grade_field] > 0) {
                    $poodman_grades[] = floatval($course[$grade_field]);
                }
            }
            return !empty($poodman_grades) ? round(array_sum($poodman_grades) / count($poodman_grades), 2) : 0;
        } else {
            // 🔥 محاسبه برای دروس غیر پودمانی - نسخه تصحیح شده
            $continuous1 = isset($course['continuous1']) && $course['continuous1'] !== null ? floatval($course['continuous1']) : 0;
            $term1 = isset($course['term1']) && $course['term1'] !== null ? floatval($course['term1']) : 0;
            $continuous2 = isset($course['continuous2']) && $course['continuous2'] !== null ? floatval($course['continuous2']) : 0;
            $term2 = isset($course['term2']) && $course['term2'] !== null ? floatval($course['term2']) : 0;
            
            // محاسبه نمره نیمسال اول
            $term1_grade = 0;
            $term1_count = 0;
            
            if ($continuous1 > 0) {
                $term1_grade += $continuous1;
                $term1_count++;
            }
            if ($term1 > 0) {
                $term1_grade += $term1;
                $term1_count++;
            }
            $term1_average = ($term1_count > 0) ? ($term1_grade / $term1_count) : 0;
            
            // محاسبه نمره نیمسال دوم
            $term2_grade = 0;
            $term2_count = 0;
            
            if ($continuous2 > 0) {
                $term2_grade += $continuous2;
                $term2_count++;
            }
            if ($term2 > 0) {
                $term2_grade += $term2;
                $term2_count++;
            }
            $term2_average = ($term2_count > 0) ? ($term2_grade / $term2_count) : 0;
            
            // محاسبه نمره نهایی
            if ($term1_average > 0 && $term2_average > 0) {
                return round(($term1_average + $term2_average) / 2, 2);
            } elseif ($term1_average > 0) {
                return round($term1_average, 2);
            } elseif ($term2_average > 0) {
                return round($term2_average, 2);
            } else {
                return 0;
            }
        }
    }
    
    // محاسبه نمره برای هر درس
    foreach ($grades as &$grade) {
        $grade['calculated_grade'] = calculateCourseGrade($grade);
    }
    
    // تفکیک دروس
    $poodmani_courses = array_filter($grades, fn($g) => $g['course_type'] == 'poodmani');
    $non_poodmani_courses = array_filter($grades, fn($g) => $g['course_type'] != 'poodmani');
    
    // 🔥 محاسبه معدل جداگانه
    function calculateAverage($courses) {
        $total = 0;
        $count = 0;
        foreach ($courses as $course) {
            if ($course['calculated_grade'] > 0) {
                $total += $course['calculated_grade'];
                $count++;
            }
        }
        return $count > 0 ? round($total / $count, 2) : 0;
    }
    
    $poodmani_average = calculateAverage($poodmani_courses);
    $non_poodmani_average = calculateAverage($non_poodmani_courses);
    $total_average = calculateAverage($grades);
    
    $data = [
        'student' => $student_details,
        'poodmani_courses' => $poodmani_courses,
        'non_poodmani_courses' => $non_poodmani_courses,
        'poodmani_average' => $poodmani_average,
        'non_poodmani_average' => $non_poodmani_average,
        'total_average' => $total_average
    ];
    
    $this->view('assistant/partials/report_card', $data, false);
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
public function addStudent() {
    // بررسی دسترسی معاون
    $assistant = $this->assistantModel->getByUserId($_SESSION['user_id']);
    if (!$assistant) {
        $this->redirect('assistant');
        return;
    }
    
    $data = [
        'assistant' => $assistant,
        // 🔥 استفاده از classModel
        'classes' => $this->classModel->getByGrade($assistant['grade_id']),
        'user_name' => $_SESSION['first_name'] . ' ' . $_SESSION['last_name']
    ];
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $studentData = [
            'first_name' => trim($_POST['first_name']),
            'last_name' => trim($_POST['last_name']),
            'mobile' => trim($_POST['mobile']),
            'national_code' => trim($_POST['national_code']),
            'birth_date' => $_POST['birth_date'] ?: null,
            'father_name' => trim($_POST['father_name'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'class_id' => $_POST['class_id']
        ];
        
        $parentData = [
            'parent_first_name' => trim($_POST['parent_first_name'] ?? ''),
            'parent_last_name' => trim($_POST['parent_last_name'] ?? ''),
            'parent_mobile' => trim($_POST['parent_mobile'] ?? ''),
            'parent_national_code' => trim($_POST['parent_national_code'] ?? ''),
            'relation_type' => $_POST['relation_type'] ?? 'father'
        ];
        
        // 🔥 اعتبارسنجی داده‌ها
        $validation = $this->validateStudentData($studentData);
        if (!$validation['valid']) {
            $data['error'] = $validation['message'];
        }
        // 🔥 اعتبارسنجی کلاس انتخابی
        elseif (!$this->validateClassForAssistant($studentData['class_id'], $assistant['grade_id'])) {
            $data['error'] = 'کلاس انتخاب شده معتبر نیست یا به پایه شما تعلق ندارد.';
        } 
        // 🔥 بررسی ظرفیت کلاس
        elseif (!$this->classModel->hasCapacity($studentData['class_id'])) {
            $data['error'] = 'ظرفیت کلاس انتخاب شده تکمیل است. لطفاً کلاس دیگری انتخاب کنید.';
        } else {
            // ایجاد دانش‌آموز و اولیا
            $result = $this->createStudentWithParent($studentData, $parentData);
            
            if ($result['success']) {
                // ثبت لاگ فعالیت
                $this->logActivity(
                    'create_student_with_parent', 
                    'ثبت دانش‌آموز جدید و اولیا: ' . $studentData['first_name'] . ' ' . $studentData['last_name'],
                    $result['student_id'],
                    'students'
                );
                
                $_SESSION['success'] = 'دانش‌آموز و اولیا با موفقیت ثبت شدند';
                $this->redirect('assistant/students');
                return;
            } else {
                $data['error'] = $result['error'];
            }
        }
    }
    
    $this->view('assistant/students', $data);
}

// 🔥 متد اعتبارسنجی داده‌های دانش‌آموز
private function validateStudentData($data) {
    // بررسی فیلدهای ضروری
    if (empty($data['first_name']) || empty($data['last_name']) || 
        empty($data['mobile']) || empty($data['national_code']) || 
        empty($data['class_id'])) {
        return ['valid' => false, 'message' => 'لطفاً تمام فیلدهای ضروری را پر کنید.'];
    }
    
    // بررسی فرمت موبایل
    if (!preg_match('/^09[0-9]{9}$/', $data['mobile'])) {
        return ['valid' => false, 'message' => 'فرمت شماره موبایل صحیح نیست.'];
    }
    
    // بررسی فرمت کد ملی
    if (!preg_match('/^[0-9]{10}$/', $data['national_code'])) {
        return ['valid' => false, 'message' => 'فرمت کد ملی صحیح نیست.'];
    }
    
    // بررسی تکراری نبودن موبایل
    if ($this->isMobileExists($data['mobile'])) {
        return ['valid' => false, 'message' => 'این شماره موبایل قبلاً ثبت شده است.'];
    }
    
    // بررسی تکراری نبودن کد ملی
    if ($this->isNationalCodeExists($data['national_code'])) {
        return ['valid' => false, 'message' => 'این کد ملی قبلاً ثبت شده است.'];
    }
    
    return ['valid' => true, 'message' => ''];
}

// 🔥 بررسی وجود موبایل در سیستم
private function isMobileExists($mobile) {
    $query = "SELECT id FROM users WHERE mobile = ?";
    $stmt = $this->db->prepare($query);
    $stmt->execute([$mobile]);
    return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
}

// 🔥 بررسی وجود کد ملی در سیستم
private function isNationalCodeExists($national_code) {
    $query = "SELECT id FROM users WHERE national_code = ?";
    $stmt = $this->db->prepare($query);
    $stmt->execute([$national_code]);
    return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
}

// 🔥 متد ایجاد دانش‌آموز و اولیا
private function createStudentWithParent($studentData, $parentData) {
    try {
        $this->db->beginTransaction();
        
        // 1. ایجاد کاربر دانش‌آموز
        $userData = [
            'mobile' => $studentData['mobile'],
            'national_code' => $studentData['national_code'],
            'role_id' => 2, // دانش‌آموز
            'first_name' => $studentData['first_name'],
            'last_name' => $studentData['last_name']
        ];
        
        if (!$this->userModel->create($userData)) {
            throw new Exception('خطا در ایجاد کاربر دانش‌آموز');
        }
        
        $student_user_id = $this->db->lastInsertId();
        
        // 2. ایجاد رکورد دانش‌آموز
        $student_number = 'STU' . date('Y') . str_pad($student_user_id, 4, '0', STR_PAD_LEFT);
        
        $studentRecordData = [
            'user_id' => $student_user_id,
            'class_id' => $studentData['class_id'],
            'student_number' => $student_number,
            'birth_date' => $studentData['birth_date'],
            'father_name' => $studentData['father_name'],
            'address' => $studentData['address']
        ];
        
        if (!$this->studentModel->create($studentRecordData)) {
            throw new Exception('خطا در ایجاد رکورد دانش‌آموز');
        }
        
        $student_id = $this->db->lastInsertId();
        
        // 3. ایجاد اولیا (اگر اطلاعات وارد شده)
        if (!empty($parentData['parent_mobile']) && !empty($parentData['parent_first_name'])) {
            $parentCreated = $this->createParentForStudent($parentData, $student_id);
            if (!$parentCreated) {
                // اگر ایجاد اولیا با مشکل مواجه شد، خطا ندهیم (اختیاری است)
                // فقط در لاگ ثبت کنیم
                error_log("خطا در ایجاد اولیا برای دانش‌آموز: " . $student_id);
            }
        }
        
        $this->db->commit();
        return [
            'success' => true,
            'student_id' => $student_id,
            'student_user_id' => $student_user_id
        ];
        
    } catch (Exception $e) {
        $this->db->rollBack();
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

// 🔥 متد ایجاد اولیا
private function createParentForStudent($parentData, $student_id) {
    // بررسی تکراری نبودن موبایل اولیا
    if ($this->isMobileExists($parentData['parent_mobile'])) {
        return false;
    }
    
    // ایجاد کاربر اولیا
    $parentUserData = [
        'mobile' => $parentData['parent_mobile'],
        'national_code' => $parentData['parent_national_code'],
        'role_id' => 4, // اولیا
        'first_name' => $parentData['parent_first_name'],
        'last_name' => $parentData['parent_last_name']
    ];
    
    if (!$this->userModel->create($parentUserData)) {
        return false;
    }
    
    $parent_user_id = $this->db->lastInsertId();
    
    // ایجاد رکورد اولیا
    $parentRecordData = [
        'user_id' => $parent_user_id,
        'student_id' => $student_id,
        'relation_type' => $parentData['relation_type'] ?? 'father'
    ];
    
    return $this->parentModel->create($parentRecordData);
}

// 🔥 متد اعتبارسنجی کلاس
private function validateClassForAssistant($class_id, $assistant_grade_id) {
    $query = "SELECT id FROM classes WHERE id = ? AND grade_id = ?";
    $stmt = $this->db->prepare($query);
    $stmt->execute([$class_id, $assistant_grade_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
}
}
?>