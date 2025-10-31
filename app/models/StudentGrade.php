<?php
class StudentGrade extends Model {
    protected $table = 'student_grades';
    
    public function recordGrade($data) {
        // 🔥 بررسی دقیق‌تر وجود رکورد
        $existing = $this->getGradeByAllFields($data['student_id'], $data['course_id'], $data['teacher_id']);
        
        if ($existing) {
            // 🔥 آپدیت رکورد موجود با منطق هوشمند
            return $this->updateGradeSmart($existing['id'], $data);
        } else {
            // ایجاد جدید
            return $this->insertGrade($data);
        }
    }
    
    // 🔥 اضافه کردن متد جدید برای پیدا کردن رکورد دقیق
    private function getGradeByAllFields($student_id, $course_id, $teacher_id) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE student_id = ? AND course_id = ? AND teacher_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$student_id, $course_id, $teacher_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // 🔥 آپدیت هوشمند - فقط فیلدهای پر شده رو آپدیت کن
    private function updateGradeSmart($grade_id, $data) {
        $update_fields = [];
        $update_values = [];
        
        // فیلدهای پودمانی
        for ($i = 1; $i <= 5; $i++) {
            $field = 'poodman' . $i;
            if (isset($data[$field]) && $data[$field] !== null && $data[$field] !== '') {
                $update_fields[] = "{$field} = ?";
                $update_values[] = $data[$field];
            }
        }
        
        // فیلدهای غیر پودمانی
        $non_poodmani_fields = ['continuous1', 'term1', 'continuous2', 'term2'];
        foreach ($non_poodmani_fields as $field) {
            if (isset($data[$field]) && $data[$field] !== null && $data[$field] !== '') {
                $update_fields[] = "{$field} = ?";
                $update_values[] = $data[$field];
            }
        }
        
        // اگر هیچ فیلدی برای آپدیت نیست، خروج
        if (empty($update_fields)) {
            return true;
        }
        
        $update_values[] = $grade_id;
        
        $query = "UPDATE " . $this->table . " SET 
                  " . implode(', ', $update_fields) . ",
                  updated_at = NOW()
                  WHERE id = ?";
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute($update_values);
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
        // 🔥 کوئری بهینه‌شده برای نمایش نمرات
        $query = "SELECT sg.*, 
                         s.student_number,
                         (SELECT CONCAT(first_name, ' ', last_name) FROM users WHERE id = s.user_id) as student_name,
                         c.name as course_name, c.course_code, c.course_type,
                         (SELECT name FROM classes WHERE id = s.class_id) as class_name
                  FROM " . $this->table . " sg
                  INNER JOIN students s ON sg.student_id = s.id
                  INNER JOIN courses c ON sg.course_id = c.id
                  WHERE sg.teacher_id = ?";
        
        $params = [$teacher_id];
        
        if ($course_id) {
            $query .= " AND sg.course_id = ?";
            $params[] = $course_id;
        }
        
        if ($class_id) {
            $query .= " AND s.class_id = ?";
            $params[] = $class_id;
        }
        
        // 🔥 گروه‌بندی برای اطمینان از عدم نمایش رکوردهای تکراری
        $query .= " GROUP BY sg.student_id, sg.course_id, sg.teacher_id
                    ORDER BY class_name, student_name";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getStudentsForGrading($teacher_id, $course_id, $class_id = null) {
        // 🔥 استفاده از کش برای اطلاعات درس
        $course = $this->getCachedData(
            "course_{$course_id}", 
            function() use ($course_id) {
                $query = "SELECT major_id, grade_id FROM courses WHERE id = ?";
                $stmt = $this->db->prepare($query);
                $stmt->execute([$course_id]);
                return $stmt->fetch(PDO::FETCH_ASSOC);
            }, 
            7200 // 2 ساعت کش
        );
        
        if (!$course) {
            return [];
        }
        
        $query = "SELECT s.id, s.user_id, u.first_name, u.last_name, s.student_number,
                         cl.name as class_name, cl.id as class_id
                  FROM students s
                  INNER JOIN users u ON s.user_id = u.id
                  INNER JOIN classes cl ON s.class_id = cl.id
                  WHERE cl.major_id = ? AND cl.grade_id = ?";
        
        $params = [$course['major_id'], $course['grade_id']];
        
        if ($class_id) {
            $query .= " AND s.class_id = ?";
            $params[] = $class_id;
        } else {
            $query .= " AND EXISTS (
                SELECT 1 FROM teacher_courses tc 
                WHERE tc.teacher_id = ? AND tc.course_id = ? 
                AND tc.class_id = s.class_id
            )";
            $params[] = $teacher_id;
            $params[] = $course_id;
        }
        
        $query .= " ORDER BY cl.name, u.first_name, u.last_name";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // 🔥 این متدها رو به انتهای کلاس اضافه کن

/**
 * دریافت تعداد رکوردهای نمرات برای pagination
 */
public function getGradesCount($teacher_id, $course_id = null, $class_id = null) {
    $query = "SELECT COUNT(*) as total 
              FROM " . $this->table . " sg
              INNER JOIN students s ON sg.student_id = s.id
              WHERE sg.teacher_id = ?";
    
    $params = [$teacher_id];
    
    if ($course_id) {
        $query .= " AND sg.course_id = ?";
        $params[] = $course_id;
    }
    
    if ($class_id) {
        $query .= " AND s.class_id = ?";
        $params[] = $class_id;
    }
    
    $stmt = $this->db->prepare($query);
    $stmt->execute($params);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total'];
}

/**
 * محاسبه معدل دانش‌آموز - نسخه بهینه‌شده
 */
public function calculateStudentAverage($student_id, $course_type = null) {
    $cache_key = "student_avg_{$student_id}_{$course_type}";
    
    return $this->getCachedData($cache_key, function() use ($student_id, $course_type) {
        $query = "SELECT course_type, 
                         CASE 
                             WHEN course_type = 'poodmani' THEN 
                                 (COALESCE(poodman1,0) + COALESCE(poodman2,0) + COALESCE(poodman3,0) + 
                                  COALESCE(poodman4,0) + COALESCE(poodman5,0)) / 
                                 NULLIF((poodman1 IS NOT NULL) + (poodman2 IS NOT NULL) + 
                                        (poodman3 IS NOT NULL) + (poodman4 IS NOT NULL) + 
                                        (poodman5 IS NOT NULL), 0)
                             ELSE 
                                 ((COALESCE(continuous1,0) + COALESCE(term1,0)) / 2 + 
                                  (COALESCE(continuous2,0) + COALESCE(term2,0)) / 2) / 2
                         END as course_avg
                  FROM " . $this->table . " 
                  WHERE student_id = ?";
        
        $params = [$student_id];
        
        if ($course_type) {
            $query .= " AND course_type = ?";
            $params[] = $course_type;
        }
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $grades = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $total = 0;
        $count = 0;
        
        foreach ($grades as $grade) {
            if ($grade['course_avg'] !== null && $grade['course_avg'] > 0) {
                $total += $grade['course_avg'];
                $count++;
            }
        }
        
        return $count > 0 ? round($total / $count, 2) : 0;
    }, 1800); // 30 دقیقه کش
}

// 🔥 این متد جدید رو بعد از recordGrade اضافه کن
public function recordBatchGrades($grades_data) {
    if (empty($grades_data)) {
        return false;
    }
    
    $query = "INSERT INTO " . $this->table . " 
              (teacher_id, student_id, course_id, course_type, 
               poodman1, poodman2, poodman3, poodman4, poodman5,
               continuous1, term1, continuous2, term2) 
              VALUES ";
    
    $placeholders = [];
    $values = [];
    
    foreach ($grades_data as $data) {
        $placeholders[] = "(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $values = array_merge($values, [
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
    
    $query .= implode(', ', $placeholders);
    
    // 🔥 آپدیت رکوردهای تکراری
    $query .= " ON DUPLICATE KEY UPDATE 
                poodman1 = VALUES(poodman1),
                poodman2 = VALUES(poodman2), 
                poodman3 = VALUES(poodman3),
                poodman4 = VALUES(poodman4),
                poodman5 = VALUES(poodman5),
                continuous1 = VALUES(continuous1),
                term1 = VALUES(term1),
                continuous2 = VALUES(continuous2), 
                term2 = VALUES(term2),
                updated_at = NOW()";
    
    $stmt = $this->db->prepare($query);
    return $stmt->execute($values);
}
}
?>