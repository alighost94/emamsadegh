<?php
class ActivityLog extends Model {
    protected $table = 'activity_logs';
    
    public function logActivity($data) {
        $query = "INSERT INTO " . $this->table . " 
                  (user_id, user_type, action, description, ip_address, user_agent, related_id, related_table) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            $data['user_id'],
            $data['user_type'],
            $data['action'],
            $data['description'],
            $data['ip_address'],
            $data['user_agent'],
            $data['related_id'],
            $data['related_table']
        ]);
    }
    
    public function getRecentActivities($limit = 10) {
        $query = "SELECT al.*, u.first_name, u.last_name, u.role_id, r.name as role_name
                  FROM " . $this->table . " al
                  JOIN users u ON al.user_id = u.id
                  JOIN roles r ON u.role_id = r.id
                  ORDER BY al.created_at DESC
                  LIMIT ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getActivitiesAfterId($last_id, $limit = 20) {
        $query = "SELECT al.*, u.first_name, u.last_name, u.role_id, r.name as role_name
                  FROM " . $this->table . " al
                  JOIN users u ON al.user_id = u.id
                  JOIN roles r ON u.role_id = r.id
                  WHERE al.id > ?
                  ORDER BY al.created_at DESC
                  LIMIT ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $last_id, PDO::PARAM_INT);
        $stmt->bindParam(2, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // متد جدید برای دریافت آخرین فعالیت
    public function getLatestActivity() {
        $query = "SELECT al.*, u.first_name, u.last_name, u.role_id, r.name as role_name
                  FROM " . $this->table . " al
                  JOIN users u ON al.user_id = u.id
                  JOIN roles r ON u.role_id = r.id
                  ORDER BY al.created_at DESC
                  LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>