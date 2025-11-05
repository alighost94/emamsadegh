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
	// اضافه کردن این متد به کلاس Course
public function searchCourses($filters = []) {
    $query = "SELECT c.*, m.name as major_name, g.name as grade_name 
              FROM courses c 
              JOIN majors m ON c.major_id = m.id 
              JOIN grades g ON c.grade_id = g.id 
              WHERE 1=1";
    
    $params = [];
    
    if (!empty($filters['search'])) {
        $query .= " AND (c.name LIKE ? OR c.course_code LIKE ?)";
        $searchTerm = "%{$filters['search']}%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }
    
    if (!empty($filters['major_id'])) {
        $query .= " AND c.major_id = ?";
        $params[] = $filters['major_id'];
    }
    
    if (!empty($filters['grade_id'])) {
        $query .= " AND c.grade_id = ?";
        $params[] = $filters['grade_id'];
    }
    
    $query .= " ORDER BY c.name";
    
    $stmt = $this->db->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
// اضافه کردن این متد به کلاس Course
public function getCourseWithDetails($course_id) {
    $query = "SELECT c.*, m.name as major_name, g.name as grade_name 
              FROM courses c 
              JOIN majors m ON c.major_id = m.id 
              JOIN grades g ON c.grade_id = g.id 
              WHERE c.id = ?";
    $stmt = $this->db->prepare($query);
    $stmt->execute([$course_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
}
?>