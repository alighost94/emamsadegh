<?php
class DisciplinaryScore extends Model {
    protected $table = 'disciplinary_scores';
    
    public function getByStudent($student_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE student_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$student_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function updateScore($student_id, $deduction = 0) {
        $existing = $this->getByStudent($student_id);
        
        if ($existing) {
            $new_score = max(0, 20 - $deduction);
            $query = "UPDATE " . $this->table . " SET 
                      current_score = ?, total_deductions = ?, last_updated = NOW() 
                      WHERE student_id = ?";
            $stmt = $this->db->prepare($query);
            return $stmt->execute([$new_score, $deduction, $student_id]);
        } else {
            $new_score = max(0, 20 - $deduction);
            $query = "INSERT INTO " . $this->table . " 
                      (student_id, current_score, total_deductions) 
                      VALUES (?, ?, ?)";
            $stmt = $this->db->prepare($query);
            return $stmt->execute([$student_id, $new_score, $deduction]);
        }
    }
    
    public function getScoresByGrade($grade_id) {
        $query = "SELECT ds.*, u.first_name, u.last_name, s.student_number,
                         c.name as class_name, m.name as major_name
                  FROM " . $this->table . " ds
                  JOIN students s ON ds.student_id = s.id
                  JOIN users u ON s.user_id = u.id
                  JOIN classes c ON s.class_id = c.id
                  JOIN majors m ON c.major_id = m.id
                  WHERE c.grade_id = ?
                  ORDER BY c.name, u.first_name, u.last_name";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$grade_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>