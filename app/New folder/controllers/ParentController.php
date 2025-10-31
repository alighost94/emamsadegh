<?php
class ParentController extends Controller {
    
    public function __construct() {
        parent::__construct();
        if (!$this->isLoggedIn() || $_SESSION['role'] != 'parent') {
            $this->redirect('auth/login');
        }
        
        // بارگذاری مدل‌ها با بررسی خطا
        $this->parentModel = $this->model('ParentModel');
        $this->studentModel = $this->model('Student');
        $this->attendanceModel = $this->model('StudentAttendance');
        $this->gradeModel = $this->model('StudentGrade');
        $this->disciplinaryRecordModel = $this->model('DisciplinaryRecord');
        $this->disciplinaryScoreModel = $this->model('DisciplinaryScore');
        
        // بررسی اینکه مدل‌ها به درستی بارگذاری شده‌اند
        if (!$this->parentModel) {
            die("خطا در بارگذاری مدل ParentModel");
        }
        
        // دریافت اطلاعات دانش‌آموز مرتبط
        $this->student_info = $this->getStudentInfo();
    }
    
    private function getStudentInfo() {
        try {
            $parent_info = $this->parentModel->getByUserId($_SESSION['user_id']);
            
            if (!$parent_info) {
                error_log("هیچ اطلاعات اولیایی برای کاربر با شناسه " . $_SESSION['user_id'] . " یافت نشد");
                return null;
            }
            
            // دریافت اطلاعات کامل دانش‌آموز
            $student_info = $this->studentModel->getByUserId($parent_info['student_user_id']);
            
            if (!$student_info) {
                error_log("هیچ اطلاعات دانش‌آموزی برای کاربر با شناسه " . $parent_info['student_user_id'] . " یافت نشد");
                return null;
            }
            
            return $student_info;
            
        } catch (Exception $e) {
            error_log("خطا در دریافت اطلاعات دانش‌آموز: " . $e->getMessage());
            return null;
        }
    }
    
    public function index() {
        if (!$this->student_info) {
            $this->showNoStudentPage();
            return;
        }
        
        // آمار کلی
        $attendance_stats = $this->getAttendanceStats();
        $grade_stats = $this->getGradeStats();
        $disciplinary_score = $this->disciplinaryScoreModel ? $this->disciplinaryScoreModel->getByStudent($this->student_info['id']) : null;
        
        $data = [
            'student_info' => $this->student_info,
            'attendance_stats' => $attendance_stats,
            'grade_stats' => $grade_stats,
            'disciplinary_score' => $disciplinary_score,
            'user_name' => $_SESSION['first_name'] . ' ' . $_SESSION['last_name']
        ];
        
        $this->view('parent/dashboard', $data);
    }
    
    private function getAttendanceStats() {
        if (!$this->attendanceModel) {
            return ['present' => 0, 'absent' => 0, 'late' => 0, 'excused' => 0, 'total' => 0, 'attendance_rate' => 0];
        }
        
        try {
            // حضور و غیاب 30 روز گذشته
            $start_date = date('Y-m-d', strtotime('-30 days'));
            $end_date = date('Y-m-d');
            
            $query = "SELECT status, COUNT(*) as count 
                      FROM student_attendance 
                      WHERE student_id = ? AND attendance_date BETWEEN ? AND ?
                      GROUP BY status";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$this->student_info['id'], $start_date, $end_date]);
            $attendance_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $stats = [
                'present' => 0,
                'absent' => 0,
                'late' => 0,
                'excused' => 0,
                'total' => 0
            ];
            
            foreach ($attendance_data as $row) {
                $stats[$row['status']] = $row['count'];
                $stats['total'] += $row['count'];
            }
            
            if ($stats['total'] > 0) {
                $stats['attendance_rate'] = round(($stats['present'] / $stats['total']) * 100, 1);
            } else {
                $stats['attendance_rate'] = 0;
            }
            
            return $stats;
            
        } catch (Exception $e) {
            error_log("خطا در دریافت آمار حضور و غیاب: " . $e->getMessage());
            return ['present' => 0, 'absent' => 0, 'late' => 0, 'excused' => 0, 'total' => 0, 'attendance_rate' => 0];
        }
    }
    
    private function getGradeStats() {
        if (!$this->gradeModel) {
            return ['average' => 0, 'course_count' => 0, 'excellent_count' => 0, 'good_count' => 0, 'average_count' => 0, 'poor_count' => 0];
        }
        
        try {
            $grades = $this->gradeModel->getGradesByTeacher(null, $this->student_info['id']);
            
            $total_grade = 0;
            $course_count = 0;
            $excellent_count = 0;
            $good_count = 0;
            $average_count = 0;
            $poor_count = 0;
            
            foreach ($grades as $grade) {
                $course_grade = $this->calculateCourseGrade($grade);
                if ($course_grade > 0) {
                    $total_grade += $course_grade;
                    $course_count++;
                    
                    if ($course_grade >= 17) $excellent_count++;
                    elseif ($course_grade >= 15) $good_count++;
                    elseif ($course_grade >= 12) $average_count++;
                    else $poor_count++;
                }
            }
            
            $average = $course_count > 0 ? round($total_grade / $course_count, 2) : 0;
            
            return [
                'average' => $average,
                'course_count' => $course_count,
                'excellent_count' => $excellent_count,
                'good_count' => $good_count,
                'average_count' => $average_count,
                'poor_count' => $poor_count
            ];
            
        } catch (Exception $e) {
            error_log("خطا در دریافت آمار نمرات: " . $e->getMessage());
            return ['average' => 0, 'course_count' => 0, 'excellent_count' => 0, 'good_count' => 0, 'average_count' => 0, 'poor_count' => 0];
        }
    }
    

    
    public function disciplinary() {
        if (!$this->student_info) {
            $this->showNoStudentPage();
            return;
        }
        
        try {
            $disciplinary_records = [];
            $disciplinary_score = null;
            
            if ($this->disciplinaryRecordModel) {
                $disciplinary_records = $this->disciplinaryRecordModel->getByStudent($this->student_info['id']);
            }
            
            if ($this->disciplinaryScoreModel) {
                $disciplinary_score = $this->disciplinaryScoreModel->getByStudent($this->student_info['id']);
            }
            
            $data = [
                'student_info' => $this->student_info,
                'disciplinary_records' => $disciplinary_records,
                'disciplinary_score' => $disciplinary_score,
                'user_name' => $_SESSION['first_name'] . ' ' . $_SESSION['last_name']
            ];
            
            $this->view('parent/disciplinary', $data);
            
        } catch (Exception $e) {
            error_log("خطا در دریافت اطلاعات انضباطی: " . $e->getMessage());
            $this->showErrorPage("خطا در دریافت اطلاعات انضباطی");
        }
    }
    
    public function profile() {
        try {
            $parent_info = $this->parentModel ? $this->parentModel->getByUserId($_SESSION['user_id']) : null;
            
            $data = [
                'parent_info' => $parent_info,
                'student_info' => $this->student_info,
                'user_name' => $_SESSION['first_name'] . ' ' . $_SESSION['last_name']
            ];
            
            $this->view('parent/profile', $data);
            
        } catch (Exception $e) {
            error_log("خطا در دریافت اطلاعات پروفایل: " . $e->getMessage());
            $this->showErrorPage("خطا در دریافت اطلاعات پروفایل");
        }
    }
    
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
            return count($valid_grades) > 0 ? round(array_sum($valid_grades) / count($valid_grades), 2) : 0;
        } else {
            // میانگین دو نیمسال برای دروس غیر پودمانی
            $term1_continuous = $course['continuous1'] ?? 0;
            $term1_final = $course['term1'] ?? 0;
            $term2_continuous = $course['continuous2'] ?? 0;
            $term2_final = $course['term2'] ?? 0;
            
            $term1 = ($term1_continuous + $term1_final) > 0 ? ($term1_continuous + $term1_final) / 2 : 0;
            $term2 = ($term2_continuous + $term2_final) > 0 ? ($term2_continuous + $term2_final) / 2 : 0;
            
            if ($term1 > 0 && $term2 > 0) {
                return round(($term1 + $term2) / 2, 2);
            } elseif ($term1 > 0) {
                return round($term1, 2);
            } elseif ($term2 > 0) {
                return round($term2, 2);
            } else {
                return 0;
            }
        }
    }
    


















    public function attendance() {
        if (!$this->student_info) {
            $this->showNoStudentPage();
            return;
        }
    
        try {
            // دریافت پارامترها
            $selected_date = $_GET['date'] ?? date('Y-m-d');
            $selected_month = $_GET['month'] ?? date('Y-m');
    
            // ====== حالت AJAX: بازگشت جزئیات یک روز برای مودال (نسخه نهایی) ======
            if (isset($_GET['ajax']) && $_GET['ajax'] == 1 && !empty($_GET['date'])) {
                $date = $_GET['date'];
                
                // 1. دریافت تمام رکوردهای آن روز از مدل (برای همه دانش‌آموزان)
                $all_daily_records = $this->attendanceModel->getAttendanceByTeacher(null, null, $date);
    
                // 1.2. فیلتر کردن رکوردها فقط برای دانش‌آموز فعلی
                // این فیلتر تضمین می‌کند که اگر فرزند شما در آن روز در چند درس ثبت‌نام شده، همه نمایش داده شوند.
                $raw_records = array_filter($all_daily_records, function($r) {
                    return isset($r['student_id']) && $r['student_id'] == $this->student_info['id'];
                });
    
                // array_values برای بازاندیس‌گذاری پس از فیلتر کردن
                $raw_records = array_values($raw_records);
    
                // 2. نرمال‌سازی فیلدها برای سازگاری با View
                $records = array_map(function($r) {
                    // تعیین نام درس
                    $lesson_name = $r['lesson_name'] ?? $r['course_name'] ?? ($r['course'] ?? '---');
                    
                    // تعیین وضعیت حضور/غیاب
                    $status = $r['status'] ?? $r['attendance_status'] ?? 'unknown';
                    
                    // تعیین نام معلم (با استفاده از داده‌های جوین شده در مدل)
                    $teacher_name = trim(($r['teacher_first_name'] ?? '') . ' ' . ($r['teacher_last_name'] ?? '')) ?: ($r['teacher_name'] ?? '---');
                    
                    // تعیین زمان ثبت (که در کوئری مدل به صورت created_at برگشت داده شده است)
                    $record_time = $r['created_at'] ?? $r['record_time'] ?? $r['time'] ?? null;
    
                    return [
                        'lesson_name'  => $lesson_name,
                        'status'       => $status,
                        'teacher_name' => $teacher_name,
                        'note'         => $r['notes'] ?? $r['note'] ?? '',
                        'record_time'  => $record_time,
                    ];
                }, $raw_records);
                
                // قراردادن partial و خروج
                include 'app/views/parent/partials/attendance_day_table.php';
                exit;
            }
    
            // ====== حالت معمولی: رندر صفحه حضور و غیاب ======
            // حضور و غیاب روز جاری (تمام معلمان -> سپس فیلتر برای دانش‌آموز)
            $daily_attendance = $this->attendanceModel ? $this->attendanceModel->getAttendanceByTeacher(null, null, $selected_date) : [];
            $student_daily_attendance = array_filter($daily_attendance, function($record) {
                return isset($record['student_id']) && $record['student_id'] == $this->student_info['id'];
            });
    
            // حضور و غیاب ماه جاری برای دانش‌آموز
            $month_start = $selected_month . '-01';
            $month_end = date('Y-m-t', strtotime($month_start));
            $monthly_attendance = $this->attendanceModel ? $this->attendanceModel->getMonthlyAttendanceByStudent(
                $this->student_info['id'],
                $month_start,
                $month_end
            ) : [];
    
            // تبدیل تاریخ‌ها به شمسی (اگر کلاس JalaliDate موجود است)
            $jalali_selected_date = class_exists('JalaliDate') ? JalaliDate::gregorianToJalali($selected_date) : $selected_date;
            $jalali_selected_month = class_exists('JalaliDate') ? JalaliDate::gregorianToJalali($selected_month . '-01', 'Y/m') : $selected_month;
    
            $data = [
                'student_info' => $this->student_info,
                'selected_date' => $selected_date,
                'selected_month' => $selected_month,
                'jalali_selected_date' => $jalali_selected_date,
                'jalali_selected_month' => $jalali_selected_month,
                'daily_attendance' => $student_daily_attendance,
                'monthly_attendance' => $monthly_attendance,
                'user_name' => $_SESSION['first_name'] . ' ' . $_SESSION['last_name']
            ];
    
            $this->view('parent/attendance', $data);
    
        } catch (Exception $e) {
            error_log("خطا در متد attendance: " . $e->getMessage());
            $this->showErrorPage("خطا در دریافت اطلاعات حضور و غیاب");
        }
    }
    public function grades() {
        if (!$this->student_info) {
            $this->showNoStudentPage();
            return;
        }
        
        // کوئری اصلاح شده برای دریافت نمرات دانش‌آموز
        $query = "SELECT sg.*, c.name as course_name, c.course_code, c.course_type, c.unit,
                         u.first_name as teacher_first_name, u.last_name as teacher_last_name
                  FROM student_grades sg
                  JOIN courses c ON sg.course_id = c.id
                  JOIN teachers t ON sg.teacher_id = t.id
                  JOIN users u ON t.user_id = u.id
                  WHERE sg.student_id = ?
                  ORDER BY c.name";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$this->student_info['id']]);
        $grades = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // اگر نمره‌ای وجود ندارد، دروس مرتبط با دانش‌آموز را پیدا کن
        if (empty($grades)) {
            $grades = $this->getRelatedCourses();
        } else {
            // محاسبه نمره هر درس
            foreach ($grades as &$grade) {
                $grade['calculated_grade'] = $this->calculateCourseGrade($grade);
            }
        }
        
        // گروه‌بندی بر اساس نوع درس
        $poodmani_grades = array_filter($grades, function($g) { 
            return isset($g['course_type']) && $g['course_type'] == 'poodmani'; 
        });
        $non_poodmani_grades = array_filter($grades, function($g) { 
            return isset($g['course_type']) && $g['course_type'] == 'non_poodmani'; 
        });
        
        $data = [
            'student_info' => $this->student_info,
            'grades' => $grades,
            'poodmani_grades' => $poodmani_grades,
            'non_poodmani_grades' => $non_poodmani_grades,
            'user_name' => $_SESSION['first_name'] . ' ' . $_SESSION['last_name']
        ];
        
        $this->view('parent/grades', $data);
    }
    
    // تابع جدید برای دریافت دروس مرتبط با دانش‌آموز
    private function getRelatedCourses() {
        $query = "SELECT c.*, '' as teacher_first_name, '' as teacher_last_name
                  FROM courses c
                  JOIN classes cl ON (c.major_id = cl.major_id AND c.grade_id = cl.grade_id)
                  JOIN students s ON s.class_id = cl.id
                  WHERE s.id = ?
                  ORDER BY c.name";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$this->student_info['id']]);
        $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // اضافه کردن ساختار نمرات خالی
        foreach ($courses as &$course) {
            $course['poodman1'] = null;
            $course['poodman2'] = null;
            $course['poodman3'] = null;
            $course['poodman4'] = null;
            $course['poodman5'] = null;
            $course['continuous1'] = null;
            $course['term1'] = null;
            $course['continuous2'] = null;
            $course['term2'] = null;
            $course['calculated_grade'] = 0;
        }
        
        return $courses;
    }



    private function showNoStudentPage() {
        echo "<!DOCTYPE html>
        <html lang='fa' dir='rtl'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>پنل اولیا - هنرستان امام صادق</title>
            <link href='https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v30.1.0/dist/font-face.css' rel='stylesheet'>
            <style>
                * { font-family: 'Vazir', sans-serif; margin: 0; padding: 0; box-sizing: border-box; }
                body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); height: 100vh; display: flex; align-items: center; justify-content: center; }
                .panel { background: white; padding: 40px; border-radius: 15px; box-shadow: 0 15px 35px rgba(0,0,0,0.1); text-align: center; max-width: 500px; width: 90%; }
                h1 { color: #333; margin-bottom: 20px; }
                p { color: #666; margin-bottom: 30px; }
                .btn { background: #667eea; color: white; padding: 12px 30px; border: none; border-radius: 8px; text-decoration: none; display: inline-block; }
                .alert { background: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
            </style>
        </head>
        <body>
            <div class='panel'>
                <h1>پنل اولیا</h1>
                <div class='alert'>
                    <strong>توجه!</strong><br>
                    هیچ دانش‌آموزی به حساب شما مرتبط نشده است.
                </div>
                <p>لطفاً با مدیریت هنرستان تماس بگیرید تا دانش‌آموز مربوطه را به حساب شما مرتبط کنند.</p>
                <a href='" . BASE_URL . "auth/logout' class='btn'>خروج</a>
            </div>
        </body>
        </html>";
    }
    





    private function showErrorPage($message) {
        echo "<!DOCTYPE html>
        <html lang='fa' dir='rtl'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>خطا - پنل اولیا</title>
            <link href='https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v30.1.0/dist/font-face.css' rel='stylesheet'>
            <style>
                * { font-family: 'Vazir', sans-serif; margin: 0; padding: 0; box-sizing: border-box; }
                body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); height: 100vh; display: flex; align-items: center; justify-content: center; }
                .panel { background: white; padding: 40px; border-radius: 15px; box-shadow: 0 15px 35px rgba(0,0,0,0.1); text-align: center; max-width: 500px; width: 90%; }
                h1 { color: #333; margin-bottom: 20px; }
                p { color: #666; margin-bottom: 30px; }
                .btn { background: #667eea; color: white; padding: 12px 30px; border: none; border-radius: 8px; text-decoration: none; display: inline-block; }
                .alert { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
            </style>
        </head>
        <body>
            <div class='panel'>
                <h1>پنل اولیا</h1>
                <div class='alert'>
                    <strong>خطا!</strong><br>
                    {$message}
                </div>
                <p>لطفاً صفحه را Refresh کنید یا با پشتیبانی تماس بگیرید.</p>
                <a href='" . BASE_URL . "parent' class='btn'>بازگشت به داشبورد</a>
            </div>
        </body>
        </html>";
    }



    public function getDayAttendance() {
        if (!$this->student_info) {
            echo json_encode(['success' => false, 'message' => 'دانش آموزی یافت نشد']);
            return;
        }
        
        $date = $_GET['date'] ?? date('Y-m-d');
        
        try {
            // دریافت حضور و غیاب روز خاص برای دانش آموز
            $query = "SELECT sa.*, c.name as course_name, c.course_code,
                             u.first_name as teacher_first_name, u.last_name as teacher_last_name
                      FROM student_attendance sa
                      JOIN courses c ON sa.course_id = c.id
                      JOIN teachers t ON sa.teacher_id = t.id
                      JOIN users u ON t.user_id = u.id
                      WHERE sa.student_id = ? AND sa.attendance_date = ?
                      ORDER BY sa.created_at DESC";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([$this->student_info['id'], $date]);
            $attendance = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'attendance' => $attendance
            ]);
            
        } catch (Exception $e) {
            error_log("خطا در دریافت حضور و غیاب روز: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'خطا در دریافت اطلاعات']);
        }
    }



    
}
?>