<?php
class StudentAttendance extends Model {
    protected $table = 'student_attendance';
    
    public function recordAttendance($data) {
        // ۱. تبدیل تاریخ شمسی به میلادی برای ذخیره در فیلد attendance_date
        $gregorian_date = date('Y-m-d'); // تاریخ میلادی پیش فرض (امروز)
        
        if (isset($data['jalali_date']) && class_exists('JalaliDate')) {
            // از متد شما برای تبدیل شمسی به میلادی استفاده می‌شود.
            // متد شما داخلی "convertToEnglishNumbers" و "explode" را هندل می‌کند.
            $gregorian_date_result = JalaliDate::jalaliToGregorian($data['jalali_date'], '/');
            
            // اگر تبدیل موفقیت‌آمیز بود و یک رشته تاریخ میلادی برگرداند:
            if (!empty($gregorian_date_result) && $gregorian_date_result != $data['jalali_date']) {
                $gregorian_date = $gregorian_date_result;
            }
        }
        
        // تنظیم فیلد میلادی برای ذخیره‌سازی
        $data['attendance_date'] = $gregorian_date;
        
        $query = "INSERT INTO " . $this->table . " 
                  (teacher_id, student_id, course_id, attendance_date, jalali_date, status, notes) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            $data['teacher_id'],
            $data['student_id'],
            $data['course_id'],
            $data['attendance_date'], // فیلد میلادی صحیح
            $data['jalali_date'],     // فیلد شمسی
            $data['status'],
            $data['notes']
        ]);
    }
public function getAttendanceByTeacher($teacher_id, $course_id = null, $date = null, $limit = 100, $page = 1) {
    $offset = ($page - 1) * $limit;
    
    // کوئری بهینه‌شده با Subquery
    $query = "
        SELECT 
            sa.id,
            sa.student_id,
            sa.attendance_date,
            sa.jalali_date, 
            sa.status,
            sa.notes,
            sa.created_at,
            s.student_number,
            (SELECT CONCAT(first_name, ' ', last_name) FROM users WHERE id = s.user_id) as student_name,
            c.course_code,
            c.name AS course_name,
            (SELECT CONCAT(first_name, ' ', last_name) FROM users WHERE id = 
                (SELECT user_id FROM teachers WHERE id = sa.teacher_id)
            ) as teacher_name
        FROM {$this->table} AS sa
        INNER JOIN students AS s ON sa.student_id = s.id
        INNER JOIN courses AS c ON sa.course_id = c.id
        WHERE sa.teacher_id = ?
    ";
    
    $params = [$teacher_id];
    
    if ($course_id !== null) {
        $query .= " AND sa.course_id = ?";
        $params[] = $course_id;
    }
    
    if ($date !== null) {
        $query .= " AND sa.attendance_date = ?";
        $params[] = $date;
    }
    
    $query .= " ORDER BY sa.attendance_date DESC, sa.id DESC
                LIMIT $limit OFFSET $offset";
    
    $stmt = $this->db->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
    
public function getStudentsForAttendance($teacher_id, $course_id, $class_id = null) {
    // استفاده از کش برای اطلاعات درس
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
// این متد جدید رو اضافه کن
public function recordBatchAttendance($attendance_data) {
    if (empty($attendance_data)) {
        return false;
    }
    
    $query = "INSERT INTO " . $this->table . " 
              (teacher_id, student_id, course_id, attendance_date, jalali_date, status, notes) 
              VALUES ";
    
    $placeholders = [];
    $values = [];
    
    foreach ($attendance_data as $data) {
        $placeholders[] = "(?, ?, ?, ?, ?, ?, ?)";
        $values = array_merge($values, [
            $data['teacher_id'],
            $data['student_id'], 
            $data['course_id'],
            $data['attendance_date'],
            $data['jalali_date'],
            $data['status'],
            $data['notes'] ?? ''
        ]);
    }
    
    $query .= implode(', ', $placeholders);
    
    // آپدیت رکوردهای تکراری
    $query .= " ON DUPLICATE KEY UPDATE 
                status = VALUES(status), 
                notes = VALUES(notes),
                created_at = NOW()";
    
    $stmt = $this->db->prepare($query);
    return $stmt->execute($values);
}

    public function getMonthlyAttendanceByStudent($student_id, $start_date, $end_date) {
        $query = "SELECT sa.attendance_date, sa.status, sa.notes, sa.created_at,
                         c.name as course_name, c.course_code,
                         u.first_name as teacher_first_name, u.last_name as teacher_last_name
                  FROM " . $this->table . " sa
                  JOIN courses c ON sa.course_id = c.id
                  JOIN teachers t ON sa.teacher_id = t.id
                  JOIN users u ON t.user_id = u.id
                  WHERE sa.student_id = ? AND sa.attendance_date BETWEEN ? AND ?
                  ORDER BY sa.attendance_date DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$student_id, $start_date, $end_date]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getAttendanceByDateForStudent($student_id, $date) {
        $query = "SELECT sa.attendance_date, sa.status, sa.notes, sa.created_at,
                         c.name as course_name, c.course_code,
                         u.first_name as teacher_first_name, u.last_name as teacher_last_name
                  FROM " . $this->table . " sa
                  JOIN courses c ON sa.course_id = c.id
                  JOIN teachers t ON sa.teacher_id = t.id
                  JOIN users u ON t.user_id = u.id
                  WHERE sa.student_id = ? AND sa.attendance_date = ?
                  ORDER BY c.name";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$student_id, $date]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getStudentAttendance($student_id, $course_id, $gregorian_date) {
        $this->db->query("SELECT * FROM attendance 
                          WHERE student_id = :student_id 
                            AND course_id = :course_id 
                            AND attendance_date = :date");
        $this->db->bind(':student_id', $student_id);
        $this->db->bind(':course_id', $course_id);
        $this->db->bind(':date', $gregorian_date);
        return $this->db->resultSet();
    }
}
?>