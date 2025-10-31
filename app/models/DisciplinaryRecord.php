<?php
class DisciplinaryRecord extends Model {
    protected $table = 'disciplinary_records';
    
    public function create($data) {
        $query = "INSERT INTO " . $this->table . " 
                  (student_id, assistant_id, violation_date, jalali_date, violation_type, description, point_deduction, status) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            $data['student_id'],
            $data['assistant_id'],
            $data['violation_date'],
            $data['jalali_date'],
            $data['violation_type'],
            $data['description'],
            $data['point_deduction'],
            $data['status']
        ]);
    }
    
    public function getByStudent($student_id) {
        $query = "SELECT dr.*, u.first_name, u.last_name, a.user_id as assistant_user_id,
                         au.first_name as assistant_first_name, au.last_name as assistant_last_name
                  FROM " . $this->table . " dr
                  JOIN students s ON dr.student_id = s.id
                  JOIN users u ON s.user_id = u.id
                  JOIN assistants a ON dr.assistant_id = a.id
                  JOIN users au ON a.user_id = au.id
                  WHERE dr.student_id = ?
                  ORDER BY dr.violation_date DESC, dr.created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$student_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getByAssistant($assistant_id, $grade_id = null, $start_date = null, $end_date = null) {
        $query = "SELECT dr.*, u.first_name, u.last_name, s.student_number,
                         c.name as class_name, m.name as major_name, g.name as grade_name
                  FROM " . $this->table . " dr
                  JOIN students s ON dr.student_id = s.id
                  JOIN users u ON s.user_id = u.id
                  JOIN classes c ON s.class_id = c.id
                  JOIN majors m ON c.major_id = m.id
                  JOIN grades g ON c.grade_id = g.id
                  WHERE dr.assistant_id = ?";
        
        $params = [$assistant_id];
        
        if ($grade_id) {
            $query .= " AND c.grade_id = ?";
            $params[] = $grade_id;
        }
        
        if ($start_date) {
            $query .= " AND dr.violation_date >= ?";
            $params[] = $start_date;
        }
        
        if ($end_date) {
            $query .= " AND dr.violation_date <= ?";
            $params[] = $end_date;
        }
        
        $query .= " ORDER BY dr.violation_date DESC, dr.created_at DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getTotalDeductions($student_id) {
        $query = "SELECT SUM(point_deduction) as total_deductions 
                  FROM " . $this->table . " 
                  WHERE student_id = ? AND status = 'approved'";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$student_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total_deductions'] ?? 0;
    }
    public function updateRecord($id, $data) {
        $query = "UPDATE " . $this->table . " SET 
                  violation_date = ?, jalali_date = ?, violation_type = ?, 
                  description = ?, point_deduction = ?, status = ?
                  WHERE id = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            $data['violation_date'],
            $data['jalali_date'],
            $data['violation_type'],
            $data['description'],
            $data['point_deduction'],
            $data['status'],
            $id
        ]);
    }
    
    /**
     * حذف مورد انضباطی
     */
    public function deleteRecord($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$id]);
    }
    
    /**
     * دریافت مورد انضباطی با جزئیات کامل
     */
    public function getByIdWithDetails($id) {
        $query = "SELECT dr.*, u.first_name, u.last_name, s.student_number,
                         c.name as class_name, m.name as major_name, g.name as grade_name,
                         a.user_id as assistant_user_id,
                         au.first_name as assistant_first_name, au.last_name as assistant_last_name
                  FROM " . $this->table . " dr
                  JOIN students s ON dr.student_id = s.id
                  JOIN users u ON s.user_id = u.id
                  JOIN classes c ON s.class_id = c.id
                  JOIN majors m ON c.major_id = m.id
                  JOIN grades g ON c.grade_id = g.id
                  JOIN assistants a ON dr.assistant_id = a.id
                  JOIN users au ON a.user_id = au.id
                  WHERE dr.id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * بررسی مالکیت - آیا این مورد انضباطی متعلق به این معاون است؟
     */
    public function isOwnedByAssistant($record_id, $assistant_id) {
        $query = "SELECT id FROM " . $this->table . " 
                  WHERE id = ? AND assistant_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$record_id, $assistant_id]);
        return $stmt->fetch() !== false;
    }
}
?>