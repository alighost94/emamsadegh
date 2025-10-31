<?php
class StaffRecord extends Model {
    protected $table = 'staff_records';
    
    public function create($data) {
        $query = "INSERT INTO " . $this->table . " 
                  (staff_id, staff_type, record_date, jalali_date, record_type, title, description, points, created_by) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            $data['staff_id'],
            $data['staff_type'],
            $data['record_date'],
            $data['jalali_date'],
            $data['record_type'],
            $data['title'],
            $data['description'],
            $data['points'],
            $data['created_by']
        ]);
    }
    
    public function getByStaff($staff_id, $staff_type) {
        $query = "SELECT sr.*, u.first_name as created_first_name, u.last_name as created_last_name
                  FROM " . $this->table . " sr
                  JOIN users u ON sr.created_by = u.id
                  WHERE sr.staff_id = ? AND sr.staff_type = ?
                  ORDER BY sr.record_date DESC, sr.created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$staff_id, $staff_type]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getAllRecords($filters = []) {
        $query = "SELECT sr.*, 
                         u.first_name as created_first_name, u.last_name as created_last_name,
                         staff_u.first_name as staff_first_name, staff_u.last_name as staff_last_name,
                         staff_u.mobile as staff_mobile
                  FROM " . $this->table . " sr
                  JOIN users u ON sr.created_by = u.id
                  LEFT JOIN teachers t ON (sr.staff_type = 'teacher' AND sr.staff_id = t.id)
                  LEFT JOIN assistants a ON (sr.staff_type = 'assistant' AND sr.staff_id = a.id)
                  LEFT JOIN users staff_u ON (
                      (sr.staff_type = 'teacher' AND t.user_id = staff_u.id) OR 
                      (sr.staff_type = 'assistant' AND a.user_id = staff_u.id)
                  )
                  WHERE 1=1";
        
        $params = [];
        
        if (!empty($filters['staff_type'])) {
            $query .= " AND sr.staff_type = ?";
            $params[] = $filters['staff_type'];
        }
        
        if (!empty($filters['record_type'])) {
            $query .= " AND sr.record_type = ?";
            $params[] = $filters['record_type'];
        }
        
        if (!empty($filters['start_date'])) {
            $query .= " AND sr.record_date >= ?";
            $params[] = $filters['start_date'];
        }
        
        if (!empty($filters['end_date'])) {
            $query .= " AND sr.record_date <= ?";
            $params[] = $filters['end_date'];
        }
        
        if (!empty($filters['status'])) {
            $query .= " AND sr.status = ?";
            $params[] = $filters['status'];
        }
        
        $query .= " ORDER BY sr.record_date DESC, sr.created_at DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getTotalPoints($staff_id, $staff_type) {
        $query = "SELECT 
                  SUM(CASE WHEN record_type = 'encouragement' THEN points ELSE 0 END) as total_encouragement,
                  SUM(CASE WHEN record_type = 'disciplinary' THEN points ELSE 0 END) as total_disciplinary
                  FROM " . $this->table . " 
                  WHERE staff_id = ? AND staff_type = ? AND status = 'approved'";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$staff_id, $staff_type]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }




    public function getByTeacher($teacher_id) {
        $query = "SELECT sr.*, u.first_name as created_first_name, u.last_name as created_last_name
                  FROM " . $this->table . " sr
                  JOIN users u ON sr.created_by = u.id
                  WHERE sr.staff_id = ? AND sr.staff_type = 'teacher'
                  ORDER BY sr.record_date DESC, sr.created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$teacher_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getByAssistantGrade($assistant_id, $grade_id) {
        $query = "SELECT sr.*, 
                         u.first_name as created_first_name, u.last_name as created_last_name,
                         staff_u.first_name as staff_first_name, staff_u.last_name as staff_last_name
                  FROM " . $this->table . " sr
                  JOIN users u ON sr.created_by = u.id
                  JOIN teachers t ON (sr.staff_type = 'teacher' AND sr.staff_id = t.id)
                  JOIN users staff_u ON t.user_id = staff_u.id
                  JOIN teacher_profiles tp ON t.id = tp.teacher_id
                  WHERE sr.staff_type = 'teacher' 
                  AND EXISTS (
                      SELECT 1 FROM assistants a 
                      WHERE a.id = ? AND a.grade_id = ?
                  )
                  ORDER BY sr.record_date DESC, sr.created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$assistant_id, $grade_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getTeacherRecordsForAssistant($grade_id) {
        $query = "SELECT sr.*, 
                         u.first_name as created_first_name, u.last_name as created_last_name,
                         staff_u.first_name as staff_first_name, staff_u.last_name as staff_last_name,
                         staff_u.mobile as staff_mobile,
                         t.expertise
                  FROM " . $this->table . " sr
                  JOIN users u ON sr.created_by = u.id
                  JOIN teachers t ON (sr.staff_type = 'teacher' AND sr.staff_id = t.id)
                  JOIN users staff_u ON t.user_id = staff_u.id
                  WHERE sr.staff_type = 'teacher'
                  ORDER BY sr.record_date DESC, sr.created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>