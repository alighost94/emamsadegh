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
                u.first_name AS student_first_name,
                u.last_name AS student_last_name,
    
                c.id AS course_id,
                c.name AS course_name,
                c.course_code,
    
                ut.first_name AS teacher_first_name,
                ut.last_name AS teacher_last_name
    
            FROM {$this->table} AS sa
            INNER JOIN students AS s ON sa.student_id = s.id
            INNER JOIN users AS u ON s.user_id = u.id
            INNER JOIN courses AS c ON sa.course_id = c.id
            INNER JOIN teachers AS t ON sa.teacher_id = t.id
            INNER JOIN users AS ut ON t.user_id = ut.id
        ";
    
        $where_clauses = [];
        $params = [];
    
        if ($teacher_id !== null) {
            $where_clauses[] = "sa.teacher_id = ?";
            $params[] = $teacher_id;
        }
    
        if ($course_id !== null) {
            $where_clauses[] = "sa.course_id = ?";
            $params[] = $course_id;
        }
    
        if ($date !== null) {
            // 🔥 جستجو بر اساس تاریخ میلادی
            $where_clauses[] = "sa.attendance_date = ?";
            $params[] = $date;
        }
    
        if (!empty($where_clauses)) {
            $query .= " WHERE " . implode(" AND ", $where_clauses);
        }
    
        $query .= " ORDER BY sa.attendance_date DESC, u.first_name, u.last_name
                    LIMIT $limit OFFSET $offset";
    
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
    
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getStudentsForAttendance($teacher_id, $course_id, $class_id = null) {
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