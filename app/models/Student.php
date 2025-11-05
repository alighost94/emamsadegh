<?php
class Student extends Model {
    protected $table = 'students';
    
    public function getCount() {
        $query = "SELECT COUNT(*) as count FROM students";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }
    
    public function create($data) {
        $query = "INSERT INTO students (user_id, class_id, student_number, birth_date, father_name, address) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            $data['user_id'],
            $data['class_id'],
            $data['student_number'],
            $data['birth_date'],
            $data['father_name'],
            $data['address']
        ]);
    }
    

    

    
    // متد جدید برای گرفتن دانش‌آموزان بر اساس رشته و پایه
    public function getStudentsByMajorAndGrade($major_id, $grade_id) {
        $query = "SELECT s.*, u.first_name, u.last_name, u.mobile,
                         c.name as class_name
                  FROM students s
                  JOIN users u ON s.user_id = u.id
                  JOIN classes c ON s.class_id = c.id
                  WHERE c.major_id = ? AND c.grade_id = ?
                  ORDER BY u.first_name, u.last_name";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$major_id, $grade_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



    public function getByUserId($user_id) {
        $query = "SELECT s.*, u.first_name, u.last_name, u.mobile,
                         c.name as class_name, c.major_id, c.grade_id,
                         m.name as major_name, g.name as grade_name
                  FROM students s
                  JOIN users u ON s.user_id = u.id
                  LEFT JOIN classes c ON s.class_id = c.id
                  LEFT JOIN majors m ON c.major_id = m.id
                  LEFT JOIN grades g ON c.grade_id = g.id
                  WHERE s.user_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $user_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    


    public function getAllWithDetails() {
        $query = "SELECT s.*, u.first_name, u.last_name, u.national_code, 
                         c.name as class_name, m.name as major_name
                  FROM students s 
                  JOIN users u ON s.user_id = u.id 
                  JOIN classes c ON s.class_id = c.id 
                  JOIN majors m ON c.major_id = m.id 
                  ORDER BY s.id DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // متد جدید برای دریافت دانش‌آموزان بر اساس رشته
    public function getStudentsByMajor($major_id) {
        $query = "SELECT s.*, u.first_name, u.last_name, u.national_code,
                         c.name as current_class_name
                  FROM students s 
                  JOIN users u ON s.user_id = u.id 
                  LEFT JOIN classes c ON s.class_id = c.id 
                  JOIN classes c2 ON c2.major_id = ?
                  WHERE s.class_id IS NULL OR c.major_id = ?
                  GROUP BY s.id";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$major_id, $major_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // متد برای تخصیص دانش‌آموز به کلاس
    public function assignToClass($student_id, $class_id) {
        $query = "UPDATE students SET class_id = ? WHERE id = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$class_id, $student_id]);
    }
    
    // متد برای دریافت دانش‌آموزان یک کلاس خاص
public function getStudentsByClass($class_id) {
    $query = "SELECT s.*, u.first_name, u.last_name, u.national_code
              FROM students s 
              JOIN users u ON s.user_id = u.id 
              WHERE s.class_id = ? 
              ORDER BY u.last_name, u.first_name"; // تغییر: اول بر اساس نام خانوادگی سپس نام
    $stmt = $this->db->prepare($query);
    $stmt->execute([$class_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}
?>