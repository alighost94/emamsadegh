<?php
require_once 'JalaliDate.php';
class Controller {
    protected $db;
    protected $model;
    
    public function __construct() {
        // اتصال به دیتابیس
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    // بارگذاری مدل
    public function model($model) {
        $modelFile = 'app/models/' . $model . '.php';
        
        if (file_exists($modelFile)) {
            require_once $modelFile;
            return new $model($this->db);
        } else {
            return false;
        }
    }
    
    // بارگذاری ویو
    public function view($view, $data = []) {
        $viewFile = 'app/views/' . $view . '.php';
        
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            echo "View not found: " . $viewFile;
        }
    }
    
    // ریدایرکت به آدرس جدید
    public function redirect($url) {
        header('Location: ' . BASE_URL . $url);
        exit();
    }
    
    // بررسی لاگین بودن
    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    // بررسی ادمین بودن
    public function isAdmin() {
        return isset($_SESSION['role']) && $_SESSION['role'] == 'admin';
    }

    // متد جدید: ثبت فعالیت کاربر
    protected function logActivity($action, $description, $related_id = null, $related_table = null) {
        if (!isset($_SESSION['user_id'])) {
            return false;
        }
        
        $activityLogModel = $this->model('ActivityLog');
        if (!$activityLogModel) {
            return false;
        }
        
        // تعیین نوع کاربر
        $user_type = 'admin'; // پیش‌فرض
        if (isset($_SESSION['role'])) {
            switch ($_SESSION['role']) {
                case 'teacher':
                    $user_type = 'teacher';
                    break;
                case 'assistant':
                    $user_type = 'assistant';
                    break;
                case 'student':
                    $user_type = 'student';
                    break;
            }
        }
        
        $logData = [
            'user_id' => $_SESSION['user_id'],
            'user_type' => $user_type,
            'action' => $action,
            'description' => $description,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'related_id' => $related_id,
            'related_table' => $related_table
        ];
        
        return $activityLogModel->logActivity($logData);
    }

    // متدهای تاریخ شمسی (همان قبلی)
    protected function getJalaliDate($gregorian_date = null, $format = 'Y/m/d') {
        if ($gregorian_date === null) {
            $gregorian_date = date('Y-m-d');
        }
        return JalaliDate::gregorianToJalali($gregorian_date, $format);
    }
    
    protected function getJalaliDateTime($gregorian_date = null, $format = 'Y/m/d H:i:s') {
        if ($gregorian_date === null) {
            $gregorian_date = date('Y-m-d H:i:s');
        }
        return JalaliDate::gregorianToJalali($gregorian_date, $format);
    }
    
    protected function convertToGregorian($jalali_date) {
        return JalaliDate::jalaliToGregorian($jalali_date);
    }
    
    protected function getCurrentJalaliDate($format = 'Y/m/d') {
        return JalaliDate::now($format);
    }
    
    protected function getJalaliMonths() {
        return JalaliDate::getJalaliMonths();
    }
    
    protected function getJalaliYears($start = -5, $end = 5) {
        return JalaliDate::getJalaliYears($start, $end);
    }
}
?>