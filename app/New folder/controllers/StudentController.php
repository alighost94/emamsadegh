<?php
class ParentController extends Controller {
    
    public function __construct() {
        parent::__construct();
        if (!$this->isLoggedIn() || $_SESSION['role'] != 'parent') {
            $this->redirect('auth/login');
        }
        
        $this->parentModel = $this->model('ParentModel');
        $this->studentModel = $this->model('Student');
        $this->attendanceModel = $this->model('StudentAttendance');
        $this->gradeModel = $this->model('StudentGrade');
        $this->disciplinaryRecordModel = $this->model('DisciplinaryRecord');
        $this->disciplinaryScoreModel = $this->model('DisciplinaryScore');
        
        // دریافت اطلاعات دانش‌آموز مرتبط
        $this->student_info = $this->getStudentInfo();
    }
    
    private function getStudentInfo() {
        $parent_info = $this->parentModel->getByUserId($_SESSION['user_id']);
        
        if (!$parent_info) {
            // اگر اطلاعات اولیا پیدا نشد، صفحه خالی نشان بده
            return null;
        }
        
        $student_info = $this->studentModel->getByUserId($parent_info['student_user_id']);
        return $student_info;
    }
    
    public function index() {
        if (!$this->student_info) {
            $this->showNoStudentPage();
            return;
        }
        
        // آمار کلی
        $attendance_stats = $this->getAttendanceStats();
        $grade_stats = $this->getGradeStats();
        $disciplinary_score = $this->disciplinaryScoreModel->getByStudent($this->student_info['id']);
        
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
    }
    
    private function getGradeStats() {
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
    }
    
    public function attendance() {
        if (!$this->student_info) {
            $this->showNoStudentPage();
            return;
        }
        
        $selected_date = $_GET['date'] ?? date('Y-m-d');
        $selected_month = $_GET['month'] ?? date('Y-m');
        
        // حضور و غیاب روز جاری
        $daily_attendance = $this->attendanceModel->getAttendanceByTeacher(null, null, $selected_date);
        $student_daily_attendance = array_filter($daily_attendance, function($record) {
            return $record['student_id'] == $this->student_info['id'];
        });
        
        // حضور و غیاب ماه جاری
        $month_start = $selected_month . '-01';
        $month_end = date('Y-m-t', strtotime($month_start));
        
        $query = "SELECT sa.attendance_date, sa.status, sa.notes, sa.created_at,
                         c.name as course_name, c.course_code,
                         u.first_name as teacher_first_name, u.last_name as teacher_last_name
                  FROM student_attendance sa
                  JOIN courses c ON sa.course_id = c.id
                  JOIN teachers t ON sa.teacher_id = t.id
                  JOIN users u ON t.user_id = u.id
                  WHERE sa.student_id = ? AND sa.attendance_date BETWEEN ? AND ?
                  ORDER BY sa.attendance_date DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$this->student_info['id'], $month_start, $month_end]);
        $monthly_attendance = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $data = [
            'student_info' => $this->student_info,
            'selected_date' => $selected_date,
            'selected_month' => $selected_month,
            'daily_attendance' => $student_daily_attendance,
            'monthly_attendance' => $monthly_attendance,
            'user_name' => $_SESSION['first_name'] . ' ' . $_SESSION['last_name']
        ];
        
        $this->view('parent/attendance', $data);
    }
    
    
    public function grades() {
        if (!$this->student_info) {
            $this->showNoStudentPage();
            return;
        }
        
        $grades = $this->gradeModel->getGradesByTeacher(null, $this->student_info['id']);
        
        // محاسبه نمره هر درس
        foreach ($grades as &$grade) {
            $grade['calculated_grade'] = $this->calculateCourseGrade($grade);
        }
        
        // گروه‌بندی بر اساس نوع درس
        $poodmani_grades = array_filter($grades, function($g) { return $g['course_type'] == 'poodmani'; });
        $non_poodmani_grades = array_filter($grades, function($g) { return $g['course_type'] == 'non_poodmani'; });
        
        $data = [
            'student_info' => $this->student_info,
            'grades' => $grades,
            'poodmani_grades' => $poodmani_grades,
            'non_poodmani_grades' => $non_poodmani_grades,
            'user_name' => $_SESSION['first_name'] . ' ' . $_SESSION['last_name']
        ];
        
        $this->view('parent/grades', $data);
    }
    
    public function disciplinary() {
        if (!$this->student_info) {
            $this->showNoStudentPage();
            return;
        }
        
        $disciplinary_records = $this->disciplinaryRecordModel->getByStudent($this->student_info['id']);
        $disciplinary_score = $this->disciplinaryScoreModel->getByStudent($this->student_info['id']);
        
        $data = [
            'student_info' => $this->student_info,
            'disciplinary_records' => $disciplinary_records,
            'disciplinary_score' => $disciplinary_score,
            'user_name' => $_SESSION['first_name'] . ' ' . $_SESSION['last_name']
        ];
        
        $this->view('parent/disciplinary', $data);
    }
    
    public function profile() {
        $parent_info = $this->parentModel->getByUserId($_SESSION['user_id']);
        
        $data = [
            'parent_info' => $parent_info,
            'student_info' => $this->student_info,
            'user_name' => $_SESSION['first_name'] . ' ' . $_SESSION['last_name']
        ];
        
        $this->view('parent/profile', $data);
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
                    <i class='bi bi-exclamation-triangle'></i>
                    <strong>توجه!</strong><br>
                    هیچ دانش‌آموزی به حساب شما مرتبط نشده است.
                </div>
                <p>لطفاً با مدیریت هنرستان تماس بگیرید تا دانش‌آموز مربوطه را به حساب شما مرتبط کنند.</p>
                <a href='" . BASE_URL . "auth/logout' class='btn'>خروج</a>
            </div>
        </body>
        </html>";
    }
}
?>