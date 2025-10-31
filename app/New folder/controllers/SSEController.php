<?php
class SSEController extends Controller {
    
    public function __construct() {
        parent::__construct();
    }
    
    public function activities() {
        // فقط مدیران می‌توانند به SSE دسترسی داشته باشند
        if (!$this->isLoggedIn() || !$this->isAdmin()) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'دسترسی غیرمجاز']);
            return;
        }
        
        $last_id = $_GET['last_id'] ?? 0;
        $timeout = $_GET['timeout'] ?? 25; // 25 ثانیه timeout
        
        $activityLogModel = $this->model('ActivityLog');
        
        if (!$activityLogModel) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'خطای سرور']);
            return;
        }
        
        // تنظیم هدرهای JSON
        header('Content-Type: application/json');
        header('Cache-Control: no-cache');
        header('Access-Control-Allow-Origin: *');
        
        // زمان شروع
        $start_time = time();
        
        // حلقه برای بررسی فعالیت‌های جدید تا timeout
        while ((time() - $start_time) < $timeout) {
            // بررسی فعالیت‌های جدید
            $new_activities = $activityLogModel->getActivitiesAfterId($last_id);
            
            // اگر فعالیت جدیدی وجود دارد، برگردان
            if (!empty($new_activities)) {
                echo json_encode([
                    'success' => true,
                    'activities' => $new_activities,
                    'count' => count($new_activities),
                    'type' => 'new_activities'
                ]);
                return;
            }
            
            // خواب 1 ثانیه قبل از بررسی مجدد
            sleep(1);
            
            // اگر connection قطع شده، خارج شو
            if (connection_aborted()) {
                exit();
            }
        }
        
        // اگر timeout شد و فعالیت جدیدی نبود
        echo json_encode([
            'success' => true,
            'activities' => [],
            'count' => 0,
            'type' => 'timeout'
        ]);
    }
    
    // متد جدید برای دریافت سریع فعالیت‌ها (بدون انتظار)
    public function getActivities() {
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
            'count' => count($activities),
            'type' => 'immediate'
        ]);
    }
}
?>