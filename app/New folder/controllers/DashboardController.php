<?php
class DashboardController extends Controller {
    
    public function __construct() {
        parent::__construct();
        if (!$this->isLoggedIn()) {
            $this->redirect('auth/login');
        }
        
        // فقط مدیران می‌توانند به داشبورد اصلی دسترسی داشته باشند
        if (!$this->isAdmin()) {
            $this->redirectToRoleDashboard();
        }
        
        // بارگذاری مدل‌ها
        $this->majorModel = $this->model('Major');
        $this->studentModel = $this->model('Student');
        $this->teacherModel = $this->model('Teacher');
        $this->activityLogModel = $this->model('ActivityLog');
    }
    
    private function redirectToRoleDashboard() {
        switch ($_SESSION['role']) {
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
                $this->redirect('auth/login');
        }
    }
    
    public function index() {
        // دریافت معلمانی که باید امروز حاضر باشند
        $today_teachers = $this->teacherModel->getTodayPresentTeachers();
        
        // دریافت اطلاعات حضور هفتگی معلمان
        $weekly_presence = $this->teacherModel->getWeeklyTeachersPresence();
        
        // دریافت آخرین فعالیت‌های سیستم
        $recent_activities = $this->activityLogModel ? $this->activityLogModel->getRecentActivities(10) : [];
        
        $data = [
            'majors_count' => $this->majorModel->getCount(),
            'students_count' => $this->studentModel->getCount(),
            'teachers_count' => $this->teacherModel->getCount(),
            'user_name' => $_SESSION['first_name'] . ' ' . $_SESSION['last_name'],
            'role' => $_SESSION['role'],
            'today_teachers' => $today_teachers,
            'weekly_presence' => $weekly_presence,
            'today_persian' => $this->getTodayPersian(),
            'today_date' => $this->getTodayJalaliDate(),
            'recent_activities' => $recent_activities // اضافه کردن فعالیت‌ها به داده‌ها
        ];
        
        $this->view('dashboard/index', $data);
    }
    
    private function getTodayPersian() {
        $english_days = [
            0 => 'sunday',    // یکشنبه
            1 => 'monday',    // دوشنبه  
            2 => 'tuesday',   // سه‌شنبه
            3 => 'wednesday', // چهارشنبه
            4 => 'thursday',  // پنجشنبه
            5 => 'friday',    // جمعه
            6 => 'saturday'   // شنبه
        ];
        
        $persian_days = [
            'saturday' => 'شنبه',
            'sunday' => 'یکشنبه', 
            'monday' => 'دوشنبه',
            'tuesday' => 'سه‌شنبه',
            'wednesday' => 'چهارشنبه',
            'thursday' => 'پنجشنبه',
            'friday' => 'جمعه'
        ];
        
        $today_english = $english_days[date('w')];
        
        return $persian_days[$today_english] ?? 'نامشخص';
    }
    
    private function getTodayJalaliDate() {
        // استفاده از کلاس JalaliDate برای دریافت تاریخ شمسی
        $jalaliDate = new JalaliDate();
        return $jalaliDate->now('l j F'); // فرمت: روز عدد ماه
    }
    // اضافه کردن این متد به DashboardController
public function getNewActivities() {
    if (!$this->isLoggedIn() || !$this->isAdmin()) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'دسترسی غیرمجاز']);
        return;
    }
    
    $last_id = $_GET['last_id'] ?? 0;
    
    $activityLogModel = $this->model('ActivityLog');
    if (!$activityLogModel) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'خطای سرور']);
        return;
    }
    
    $activities = $activityLogModel->getActivitiesAfterId($last_id);
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'activities' => $activities,
        'count' => count($activities)
    ]);
}
}
?>