<?php
class TeacherCourse extends Model {
    protected $table = 'teacher_courses';
    
    public function assignCourse($data) {
        $query = "INSERT INTO " . $this->table . " (teacher_id, course_id, class_id) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($query);
        
        return $stmt->execute([
            $data['teacher_id'],
            $data['course_id'],
            $data['class_id']
        ]);
    }
    
    public function checkAssignmentExists($teacher_id, $course_id, $class_id = null) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table . " 
                  WHERE teacher_id = ? AND course_id = ?";
        
        $params = [$teacher_id, $course_id];
        
        if ($class_id === null) {
            $query .= " AND class_id IS NULL";
        } else {
            $query .= " AND class_id = ?";
            $params[] = $class_id;
        }
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }
    
    public function getByTeacher($teacher_id) {
        $query = "SELECT tc.*, c.course_code, c.name as course_name, 
                         m.name as major_name, g.name as grade_name,
                         cls.name as class_name
                  FROM " . $this->table . " tc
                  JOIN courses c ON tc.course_id = c.id
                  JOIN majors m ON c.major_id = m.id
                  JOIN grades g ON c.grade_id = g.id
                  LEFT JOIN classes cls ON tc.class_id = cls.id
                  WHERE tc.teacher_id = ?
                  ORDER BY c.name, cls.name";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$teacher_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // اضافه کردن این متد به کلاس TeacherCourse
public function assignMultipleCourses($teacher_id, $courses_data) {
    $this->db->beginTransaction();
    
    try {
        $assigned_count = 0;
        foreach ($courses_data as $course_data) {
            // بررسی تکراری نبودن
            if (!$this->checkAssignmentExists($teacher_id, $course_data['course_id'], $course_data['class_id'])) {
                $query = "INSERT INTO teacher_courses (teacher_id, course_id, class_id) VALUES (?, ?, ?)";
                $stmt = $this->db->prepare($query);
                $stmt->execute([
                    $teacher_id,
                    $course_data['course_id'],
                    $course_data['class_id']
                ]);
                $assigned_count++;
            }
        }
        
        $this->db->commit();
        return $assigned_count;
    } catch (Exception $e) {
        $this->db->rollBack();
        return false;
    }
}
}
?>