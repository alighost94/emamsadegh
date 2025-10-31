<?php
class PollingController extends Controller {
    
    public function __construct() {
        parent::__construct();
    }
    
    // متد ساده برای دریافت فعالیت‌های جدید
    public function getNewActivities() {
        // فقط مدیران می‌توانند دسترسی داشته باشند
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
            'last_id' => $last_id,
            'timestamp' => time()
        ]);
    }
}
?>