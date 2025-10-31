<?php
class Assistant extends Model {
    protected $table = 'assistants';
    
    public function getByUserId($user_id) {
        $query = "SELECT a.*, u.first_name, u.last_name, u.mobile, g.name as grade_name
                  FROM assistants a
                  JOIN users u ON a.user_id = u.id
                  JOIN grades g ON a.grade_id = g.id
                  WHERE a.user_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $user_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // اضافه کردن متد جدید
    public function getByAssistantId($assistant_id) {
        $query = "SELECT a.*, u.first_name, u.last_name, u.mobile, g.name as grade_name
                  FROM assistants a
                  JOIN users u ON a.user_id = u.id
                  JOIN grades g ON a.grade_id = g.id
                  WHERE a.id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $assistant_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // اضافه کردن متد getAllWithDetails
    public function getAllWithDetails() {
        $query = "SELECT a.*, u.first_name, u.last_name, u.mobile, 
                         g.name as grade_name, g.level as grade_level
                  FROM assistants a
                  JOIN users u ON a.user_id = u.id
                  JOIN grades g ON a.grade_id = g.id
                  ORDER BY a.id DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function updateProfileCompletion($assistant_id) {
        $query = "UPDATE assistants SET profile_completed = 1 WHERE id = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$assistant_id]);
    }
    
    public function getStudentsByGrade($grade_id, $class_id = null) {
        $query = "SELECT s.*, u.first_name, u.last_name, u.mobile, s.student_number,
                         c.name as class_name, m.name as major_name,
                         ds.current_score as disciplinary_score
                  FROM students s
                  JOIN users u ON s.user_id = u.id
                  JOIN classes c ON s.class_id = c.id
                  JOIN majors m ON c.major_id = m.id
                  LEFT JOIN disciplinary_scores ds ON s.id = ds.student_id
                  WHERE c.grade_id = ?";
        
        $params = [$grade_id];
        
        if ($class_id) {
            $query .= " AND s.class_id = ?";
            $params[] = $class_id;
        }
        
        $query .= " ORDER BY c.name, u.first_name, u.last_name";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getClassesByGrade($grade_id) {
        $query = "SELECT c.*, m.name as major_name, 
                         COUNT(s.id) as student_count
                  FROM classes c
                  JOIN majors m ON c.major_id = m.id
                  LEFT JOIN students s ON c.id = s.class_id
                  WHERE c.grade_id = ?
                  GROUP BY c.id
                  ORDER BY c.name";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$grade_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
// در کلاس Assistant، متد getAttendanceByGrade را اینگونه اصلاح کنید:
public function getAttendanceByGrade($grade_id, $date = null, $class_id = null) {
    if (!$date) {
        $date = date('Y-m-d');
    }
    
    $query = "SELECT sa.*, u.first_name, u.last_name, s.student_number,
                     c.name as class_name, m.name as major_name,
                     cr.name as course_name, cr.course_code,
                     t.user_id as teacher_user_id,
                     tu.first_name as teacher_first_name, tu.last_name as teacher_last_name
              FROM student_attendance sa
              JOIN students s ON sa.student_id = s.id
              JOIN users u ON s.user_id = u.id
              JOIN classes c ON s.class_id = c.id
              JOIN majors m ON c.major_id = m.id
              JOIN courses cr ON sa.course_id = cr.id
              JOIN teachers t ON sa.teacher_id = t.id
              JOIN users tu ON t.user_id = tu.id
              WHERE c.grade_id = ? AND sa.attendance_date = ?";
    
    $params = [$grade_id, $date];
    
    if ($class_id) {
        $query .= " AND c.id = ?";
        $params[] = $class_id;
    }
    
    $query .= " ORDER BY c.name, u.first_name, u.last_name, cr.name";
    
    $stmt = $this->db->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// همچنین متد getCompleteAttendanceByGrade را نیز اصلاح کنید:
public function getCompleteAttendanceByGrade($grade_id, $start_date = null, $end_date = null, $class_id = null) {
    if (!$start_date) {
        $start_date = date('Y-m-d', strtotime('-30 days'));
    }
    if (!$end_date) {
        $end_date = date('Y-m-d');
    }
    
    $query = "SELECT sa.*, u.first_name, u.last_name, s.student_number,
                     c.name as class_name, m.name as major_name,
                     cr.name as course_name, cr.course_code,
                     t.user_id as teacher_user_id,
                     tu.first_name as teacher_first_name, tu.last_name as teacher_last_name
              FROM student_attendance sa
              JOIN students s ON sa.student_id = s.id
              JOIN users u ON s.user_id = u.id
              JOIN classes c ON s.class_id = c.id
              JOIN majors m ON c.major_id = m.id
              JOIN courses cr ON sa.course_id = cr.id
              JOIN teachers t ON sa.teacher_id = t.id
              JOIN users tu ON t.user_id = tu.id
              WHERE c.grade_id = ? AND sa.attendance_date BETWEEN ? AND ?";
    
    $params = [$grade_id, $start_date, $end_date];
    
    if ($class_id) {
        $query .= " AND c.id = ?";
        $params[] = $class_id;
    }
    
    $query .= " ORDER BY sa.attendance_date DESC, c.name, u.first_name, u.last_name, cr.name";
    
    $stmt = $this->db->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
    
public function getGradesByGrade($grade_id) {
    $query = "SELECT sg.*, u.first_name, u.last_name, s.student_number,
                     c.name as class_name, m.name as major_name,
                     cr.name as course_name, cr.course_code, cr.course_type,
                     t.user_id as teacher_user_id,
                     tu.first_name as teacher_first_name, tu.last_name as teacher_last_name
              FROM student_grades sg
              JOIN students s ON sg.student_id = s.id
              JOIN users u ON s.user_id = u.id
              JOIN classes c ON s.class_id = c.id
              JOIN majors m ON c.major_id = m.id
              JOIN courses cr ON sg.course_id = cr.id
              JOIN teachers t ON sg.teacher_id = t.id
              JOIN users tu ON t.user_id = tu.id
              WHERE c.grade_id = ?
              ORDER BY c.name, u.first_name, u.last_name, cr.name";
    
    $stmt = $this->db->prepare($query);
    $stmt->execute([$grade_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // ❌ محاسبه معدل حذف شد (باید در کنترلر باشد)
}









   /**
    * دریافت لیست روزهای دارای حضور و غیاب
    */
   public function getAttendanceDays($grade_id, $start_date = null, $end_date = null) {
       if (!$start_date) {
           $start_date = date('Y-m-d', strtotime('-30 days'));
       }
       if (!$end_date) {
           $end_date = date('Y-m-d');
       }
       
       $query = "SELECT DISTINCT attendance_date 
                 FROM student_attendance sa
                 JOIN students s ON sa.student_id = s.id
                 JOIN classes c ON s.class_id = c.id
                 WHERE c.grade_id = ? AND sa.attendance_date BETWEEN ? AND ?
                 ORDER BY sa.attendance_date DESC";
       
       $stmt = $this->db->prepare($query);
       $stmt->execute([$grade_id, $start_date, $end_date]);
       return $stmt->fetchAll(PDO::FETCH_ASSOC);
   }
   
   /**
    * دریافت آمار حضور و غیاب
    */
   public function getAttendanceStatistics($grade_id, $start_date = null, $end_date = null) {
       if (!$start_date) {
           $start_date = date('Y-m-d', strtotime('-30 days'));
       }
       if (!$end_date) {
           $end_date = date('Y-m-d');
       }
       
       $query = "SELECT 
                 sa.attendance_date,
                 c.name as class_name,
                 COUNT(*) as total_records,
                 SUM(CASE WHEN sa.status = 'present' THEN 1 ELSE 0 END) as present_count,
                 SUM(CASE WHEN sa.status = 'absent' THEN 1 ELSE 0 END) as absent_count,
                 SUM(CASE WHEN sa.status = 'late' THEN 1 ELSE 0 END) as late_count,
                 SUM(CASE WHEN sa.status = 'excused' THEN 1 ELSE 0 END) as excused_count
                 FROM student_attendance sa
                 JOIN students s ON sa.student_id = s.id
                 JOIN classes c ON s.class_id = c.id
                 WHERE c.grade_id = ? AND sa.attendance_date BETWEEN ? AND ?
                 GROUP BY sa.attendance_date, c.name
                 ORDER BY sa.attendance_date DESC, c.name";
       
       $stmt = $this->db->prepare($query);
       $stmt->execute([$grade_id, $start_date, $end_date]);
       return $stmt->fetchAll(PDO::FETCH_ASSOC);
   }
   
   /**
    * دریافت دانش‌آموزان غایب در یک بازه زمانی
    */
   public function getAbsentStudents($grade_id, $start_date = null, $end_date = null) {
       if (!$start_date) {
           $start_date = date('Y-m-d', strtotime('-7 days'));
       }
       if (!$end_date) {
           $end_date = date('Y-m-d');
       }
       
       $query = "SELECT 
                 u.first_name, u.last_name, s.student_number,
                 c.name as class_name, m.name as major_name,
                 sa.attendance_date, sa.status, cr.name as course_name,
                 COUNT(*) as absent_days
                 FROM student_attendance sa
                 JOIN students s ON sa.student_id = s.id
                 JOIN users u ON s.user_id = u.id
                 JOIN classes c ON s.class_id = c.id
                 JOIN majors m ON c.major_id = m.id
                 JOIN courses cr ON sa.course_id = cr.id
                 WHERE c.grade_id = ? 
                 AND sa.attendance_date BETWEEN ? AND ?
                 AND sa.status = 'absent'
                 GROUP BY s.id, u.first_name, u.last_name, s.student_number, c.name, m.name
                 HAVING absent_days >= 2
                 ORDER BY absent_days DESC, c.name, u.first_name, u.last_name";
       
       $stmt = $this->db->prepare($query);
       $stmt->execute([$grade_id, $start_date, $end_date]);
       return $stmt->fetchAll(PDO::FETCH_ASSOC);
   }
}
?>