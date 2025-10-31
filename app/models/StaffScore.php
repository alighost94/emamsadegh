<?php
class StaffScore extends Model {
    protected $table = 'staff_scores';
    
    public function getByStaff($staff_id, $staff_type) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE staff_id = ? AND staff_type = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$staff_id, $staff_type]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function updateScore($staff_id, $staff_type, $encouragement_points = 0, $disciplinary_points = 0) {
        $existing = $this->getByStaff($staff_id, $staff_type);
        
        if ($existing) {
            $new_score = 100 + $encouragement_points - $disciplinary_points;
            $query = "UPDATE " . $this->table . " SET 
                      current_score = ?, 
                      total_encouragement = ?,
                      total_disciplinary = ?,
                      last_updated = NOW()
                      WHERE staff_id = ? AND staff_type = ?";
            $stmt = $this->db->prepare($query);
            return $stmt->execute([
                $new_score,
                $encouragement_points,
                $disciplinary_points,
                $staff_id,
                $staff_type
            ]);
        } else {
            $new_score = 100 + $encouragement_points - $disciplinary_points;
            $query = "INSERT INTO " . $this->table . " 
                      (staff_id, staff_type, current_score, total_encouragement, total_disciplinary) 
                      VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($query);
            return $stmt->execute([
                $staff_id,
                $staff_type,
                $new_score,
                $encouragement_points,
                $disciplinary_points
            ]);
        }
    }
    
    public function getAllScores() {
        $query = "SELECT ss.*, 
                         u.first_name, u.last_name, u.mobile,
                         t.id as teacher_id, a.id as assistant_id
                  FROM " . $this->table . " ss
                  LEFT JOIN teachers t ON (ss.staff_type = 'teacher' AND ss.staff_id = t.id)
                  LEFT JOIN assistants a ON (ss.staff_type = 'assistant' AND ss.staff_id = a.id)
                  LEFT JOIN users u ON (
                      (ss.staff_type = 'teacher' AND t.user_id = u.id) OR 
                      (ss.staff_type = 'assistant' AND a.user_id = u.id)
                  )
                  ORDER BY ss.current_score DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getByTeacher($teacher_id) {
        return $this->getByStaff($teacher_id, 'teacher');
    }
    
    public function getTeachersScoresForAssistant($grade_id) {
        $query = "SELECT ss.*, 
                         u.first_name, u.last_name, u.mobile,
                         t.expertise, tp.personnel_code
                  FROM " . $this->table . " ss
                  JOIN teachers t ON (ss.staff_type = 'teacher' AND ss.staff_id = t.id)
                  JOIN users u ON t.user_id = u.id
                  LEFT JOIN teacher_profiles tp ON t.id = tp.teacher_id
                  WHERE ss.staff_type = 'teacher'
                  ORDER BY ss.current_score DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>