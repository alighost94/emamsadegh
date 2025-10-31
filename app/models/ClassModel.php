<?php
class ClassModel extends Model {
    protected $table = 'classes';
    
    public function create($data) {
        $query = "INSERT INTO classes (name, major_id, grade_id, capacity) 
                  VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            $data['name'],
            $data['major_id'],
            $data['grade_id'],
            $data['capacity']
        ]);
    }
    
    public function getAllWithDetails() {
        $query = "SELECT c.*, m.name as major_name, g.name as grade_name 
                  FROM classes c 
                  JOIN majors m ON c.major_id = m.id 
                  JOIN grades g ON c.grade_id = g.id 
                  ORDER BY c.id DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // به کلاس ClassModel این متد را اضافه کنید
public function getById($id) {
    $query = "SELECT c.*, m.name as major_name, g.name as grade_name 
              FROM classes c 
              JOIN majors m ON c.major_id = m.id 
              JOIN grades g ON c.grade_id = g.id 
              WHERE c.id = ?";
    $stmt = $this->db->prepare($query);
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}


public function getByMajorAndGrade($major_id, $grade_id) {
    $query = "SELECT * FROM " . $this->table . " 
              WHERE major_id = ? AND grade_id = ? 
              ORDER BY name";
    $stmt = $this->db->prepare($query);
    $stmt->execute([$major_id, $grade_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
// اگر این متد وجود ندارد، اضافه کنید
public function getClassName($class_id) {
    if (!$class_id) return 'همه کلاس‌ها';
    
    $query = "SELECT name FROM classes WHERE id = ?";
    $stmt = $this->db->prepare($query);
    $stmt->execute([$class_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['name'] : 'نامشخص';
}


// 🔥 متد جدید برای دریافت کلاس‌های یک پایه خاص با جزئیات
public function getClassesByGradeWithDetails($grade_id) {
    $query = "SELECT c.*, m.name as major_name, g.name as grade_name,
                     (SELECT COUNT(*) FROM students WHERE class_id = c.id) as student_count,
                     c.capacity
              FROM classes c
              JOIN majors m ON c.major_id = m.id
              JOIN grades g ON c.grade_id = g.id
              WHERE c.grade_id = ?
              ORDER BY m.name, c.name";
    $stmt = $this->db->prepare($query);
    $stmt->execute([$grade_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 🔥 متد جدید برای بررسی ظرفیت کلاس
public function hasCapacity($class_id) {
    $query = "SELECT c.capacity, 
                     (SELECT COUNT(*) FROM students WHERE class_id = c.id) as current_count
              FROM classes c
              WHERE c.id = ?";
    $stmt = $this->db->prepare($query);
    $stmt->execute([$class_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $result && ($result['current_count'] < $result['capacity']);
}
public function getByGrade($grade_id) {
    $query = "SELECT c.*, m.name as major_name, g.name as grade_name,
                     (SELECT COUNT(*) FROM students WHERE class_id = c.id) as student_count
              FROM classes c
              JOIN majors m ON c.major_id = m.id
              JOIN grades g ON c.grade_id = g.id
              WHERE c.grade_id = ?
              ORDER BY m.name, c.name";
    $stmt = $this->db->prepare($query);
    $stmt->execute([$grade_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}
?>