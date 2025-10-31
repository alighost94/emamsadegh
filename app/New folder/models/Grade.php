<?php
class Grade extends Model {
    protected $table = 'grades';
    public function getGradesByStudent($student_id) {
        $query = "SELECT sg.*, c.name as course_name, c.course_code, c.course_type,
                         c.major_id, c.grade_id, m.name as major_name, g.name as grade_name,
                         t.user_id as teacher_user_id,
                         u.first_name as teacher_first_name, u.last_name as teacher_last_name
                  FROM student_grades sg
                  JOIN courses c ON sg.course_id = c.id
                  JOIN majors m ON c.major_id = m.id
                  JOIN grades g ON c.grade_id = g.id
                  JOIN teachers t ON sg.teacher_id = t.id
                  JOIN users u ON t.user_id = u.id
                  WHERE sg.student_id = ?
                  ORDER BY c.course_type, c.name";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([$student_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>