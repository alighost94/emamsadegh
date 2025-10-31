<?php
class Course extends Model {
    protected $table = 'courses';
    
    public function create($data) {
        $query = "INSERT INTO courses (course_code, name, major_id, grade_id, unit, course_type) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            $data['course_code'],
            $data['name'],
            $data['major_id'],
            $data['grade_id'],
            $data['unit'],
            $data['course_type']
        ]);
    }
    
    public function getAllWithDetails() {
        $query = "SELECT c.*, m.name as major_name, g.name as grade_name 
                  FROM courses c 
                  JOIN majors m ON c.major_id = m.id 
                  JOIN grades g ON c.grade_id = g.id 
                  ORDER BY c.id DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>