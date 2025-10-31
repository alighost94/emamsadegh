<?php
class StudentGrade extends Model {
    protected $table = 'student_grades';
    
    public function recordGrade($data) {
        // بررسی وجود رکورد
        $existing = $this->getGrade($data['student_id'], $data['course_id']);
        
        if ($existing) {
            // آپدیت
            return $this->updateGrade($existing['id'], $data);
        } else {
            // ایجاد جدید
            return $this->insertGrade($data);
        }
    }
    
    private function insertGrade($data) {
        $query = "INSERT INTO " . $this->table . " 
                  (teacher_id, student_id, course_id, course_type, 
                   poodman1, poodman2, poodman3, poodman4, poodman5,
                   continuous1, term1, continuous2, term2) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            $data['teacher_id'],
            $data['student_id'],
            $data['course_id'],
            $data['course_type'],
            $data['poodman1'] ?? null,
            $data['poodman2'] ?? null,
            $data['poodman3'] ?? null,
            $data['poodman4'] ?? null,
            $data['poodman5'] ?? null,
            $data['continuous1'] ?? null,
            $data['term1'] ?? null,
            $data['continuous2'] ?? null,
            $data['term2'] ?? null
        ]);
    }
    
    private function updateGrade($grade_id, $data) {
        $query = "UPDATE " . $this->table . " SET 
                  poodman1 = ?, poodman2 = ?, poodman3 = ?, poodman4 = ?, poodman5 = ?,
                  continuous1 = ?, term1 = ?, continuous2 = ?, term2 = ?,
                  updated_at = NOW()
                  WHERE id = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            $data['poodman1'] ?? null,
            $data['poodman2'] ?? null,
            $data['poodman3'] ?? null,
            $data['poodman4'] ?? null,
            $data['poodman5'] ?? null,
            $data['continuous1'] ?? null,
            $data['term1'] ?? null,
            $data['continuous2'] ?? null,
            $data['term2'] ?? null,
            $grade_id
        ]);
    }
    
    public function getGrade($student_id, $course_id) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE student_id = ? AND course_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$student_id, $course_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getGradesByTeacher($teacher_id, $course_id = null, $class_id = null) {
        $query = "SELECT sg.*, u.first_name, u.last_name, s.student_number,
                         c.name as course_name, c.course_code, c.course_type,
                         cls.name as class_name, cls.id as class_id
                  FROM " . $this->table . " sg
                  JOIN students s ON sg.student_id = s.id
                  JOIN users u ON s.user_id = u.id
                  JOIN courses c ON sg.course_id = c.id
                  JOIN classes cls ON s.class_id = cls.id  
                  WHERE sg.teacher_id = ?";
        
        $params = [$teacher_id];
        
        if ($course_id) {
            $query .= " AND sg.course_id = ?";
            $params[] = $course_id;
        }
        
        // 🔥 فیلتر بر اساس class_id
        if ($class_id) {
            $query .= " AND s.class_id = ?";
            $params[] = $class_id;
        } else {
            // اگر class_id مشخص نشده، فقط کلاس‌هایی که معلم به آنها تخصیص داده شده
            $query .= " AND s.class_id IN (
                SELECT DISTINCT tc.class_id 
                FROM teacher_courses tc 
                WHERE tc.teacher_id = ? AND tc.course_id = ? AND tc.class_id IS NOT NULL
            )";
            $params[] = $teacher_id;
            $params[] = $course_id;
        }
        
        $query .= " ORDER BY cls.name, u.first_name, u.last_name";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }














    public function getStudentsForGrading($teacher_id, $course_id, $class_id = null) {
        // دریافت اطلاعات درس برای پیدا کردن رشته و پایه
        $course_query = "SELECT major_id, grade_id FROM courses WHERE id = ?";
        $course_stmt = $this->db->prepare($course_query);
        $course_stmt->execute([$course_id]);
        $course = $course_stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$course) {
            return [];
        }
        
        $major_id = $course['major_id'];
        $grade_id = $course['grade_id'];
        
        // 🔥 تغییر اصلی: در نظر گرفتن class_id از teacher_courses
        $query = "SELECT s.id, s.user_id, u.first_name, u.last_name, s.student_number,
                         cl.name as class_name, cl.id as class_id
                  FROM students s
                  JOIN users u ON s.user_id = u.id
                  JOIN classes cl ON s.class_id = cl.id
                  WHERE cl.major_id = ? AND cl.grade_id = ?";
        
        $params = [$major_id, $grade_id];
        
        // اگر class_id مشخص شده، فقط دانش‌آموزان همون کلاس
        if ($class_id) {
            $query .= " AND s.class_id = ?";
            $params[] = $class_id;
        } else {
            // اگر class_id مشخص نشده، فقط کلاس‌هایی که معلم به آنها تخصیص داده شده
            $query .= " AND s.class_id IN (
                SELECT DISTINCT tc.class_id 
                FROM teacher_courses tc 
                WHERE tc.teacher_id = ? AND tc.course_id = ? AND tc.class_id IS NOT NULL
            )";
            $params[] = $teacher_id;
            $params[] = $course_id;
        }
        
        $query .= " ORDER BY cl.name, u.first_name, u.last_name";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>