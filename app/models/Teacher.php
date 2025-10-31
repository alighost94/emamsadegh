<?php
class Teacher extends Model {
    protected $table = 'teachers';
    
    public function getCount() {
        $query = "SELECT COUNT(*) as count FROM teachers";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }
    
    public function create($data) {
        $query = "INSERT INTO teachers (user_id, expertise, employment_date) 
                  VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            $data['user_id'],
            $data['expertise'],
            $data['employment_date']
        ]);
    }
    
    // این متد باید وجود داشته باشد
    public function getAllWithDetails() {
        $query = "SELECT t.*, u.first_name, u.last_name, u.mobile
                  FROM teachers t
                  JOIN users u ON t.user_id = u.id
                  ORDER BY t.id DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getByUserId($user_id) {
        $query = "SELECT t.*, u.first_name, u.last_name, u.mobile
                  FROM teachers t
                  JOIN users u ON t.user_id = u.id
                  WHERE t.user_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $user_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // اضافه کردن متد getById
    public function getById($teacher_id) {
        $query = "SELECT t.*, u.first_name, u.last_name, u.mobile
                  FROM teachers t
                  JOIN users u ON t.user_id = u.id
                  WHERE t.id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $teacher_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getTeacherCourses($teacher_id) {
        $query = "SELECT tc.*, c.course_code, c.name as course_name, 
                         m.name as major_name, g.name as grade_name, c.course_type,
                         cls.name as class_name, cls.id as class_id,
                         c.id as course_id, c.major_id, c.grade_id  
                  FROM teacher_courses tc
                  JOIN courses c ON tc.course_id = c.id
                  JOIN majors m ON c.major_id = m.id
                  JOIN grades g ON c.grade_id = g.id
                  LEFT JOIN classes cls ON tc.class_id = cls.id
                  WHERE tc.teacher_id = ?
                  ORDER BY c.name, cls.name";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $teacher_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    

// متد جدید برای گرفتن دانش‌آموزان یک درس با در نظر گرفتن کلاس
public function getStudentsByCourseAndClass($teacher_id, $course_id, $class_id = null) {
    if ($class_id) {
        // اگر کلاس مشخص شده، فقط دانش‌آموزان آن کلاس
        $query = "SELECT s.*, u.first_name, u.last_name, cls.name as class_name
                  FROM students s
                  JOIN users u ON s.user_id = u.id
                  JOIN classes cls ON s.class_id = cls.id
                  WHERE s.class_id = ?
                  ORDER BY u.last_name, u.first_name";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$class_id]);
    } else {
        // اگر کلاس مشخص نشده، همه دانش‌آموزان آن درس در آن رشته/پایه
        $query = "SELECT s.*, u.first_name, u.last_name, cls.name as class_name
                  FROM students s
                  JOIN users u ON s.user_id = u.id
                  JOIN classes cls ON s.class_id = cls.id
                  JOIN courses c ON cls.major_id = c.major_id AND cls.grade_id = c.grade_id
                  WHERE c.id = ?
                  ORDER BY u.last_name, u.first_name";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$course_id]);
    }
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    // اضافه کردن متد جدید برای آپدیت وضعیت پروفایل
    public function updateProfileCompletion($teacher_id) {
        $query = "UPDATE teachers SET profile_completed = 1 WHERE id = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$teacher_id]);
    }

// در فایل models/Teacher.php
public function getTodayPresentTeachers() {
    // استفاده از کلاس JalaliDate برای دریافت روز فعلی
    $jalaliDate = new JalaliDate();
    
    // روزهای هفته به فارسی (مطابق با دیتابیس)
    $persian_days = [
        'saturday' => 'شنبه',
        'sunday' => 'یکشنبه', 
        'monday' => 'دوشنبه',
        'tuesday' => 'سه‌شنبه',
        'wednesday' => 'چهارشنبه'
    ];
    
    // دریافت روز فعلی به انگلیسی
    $today_en = $this->getTodayEnglish();
    
    if (!$today_en || !isset($persian_days[$today_en])) {
        return []; // اگر امروز در لیست روزهای کاری نبود
    }
    
    // کوئری برای فیلد SET
    $query = "SELECT t.*, u.first_name, u.last_name, u.mobile, tp.presence_days
              FROM teachers t
              INNER JOIN users u ON t.user_id = u.id
              INNER JOIN teacher_profiles tp ON t.id = tp.teacher_id
              WHERE tp.profile_completed = 1 
              AND FIND_IN_SET(?, tp.presence_days) > 0";
    
    $stmt = $this->db->prepare($query);
    $stmt->execute([$today_en]);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function getWeeklyTeachersPresence() {
    // کوئری برای دریافت تمام معلمان با روزهای حضور
    $query = "SELECT t.*, u.first_name, u.last_name, u.mobile, tp.presence_days
              FROM teachers t
              INNER JOIN users u ON t.user_id = u.id
              INNER JOIN teacher_profiles tp ON t.id = tp.teacher_id
              WHERE tp.profile_completed = 1";
    
    $stmt = $this->db->prepare($query);
    $stmt->execute();
    $teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // روزهای هفته هنرستان
    $week_days = [
        'saturday' => 'شنبه',
        'sunday' => 'یکشنبه', 
        'monday' => 'دوشنبه',
        'tuesday' => 'سه‌شنبه',
        'wednesday' => 'چهارشنبه'
    ];
    
    $weekly_presence = [];
    
    // مقداردهی اولیه برای هر روز
    foreach ($week_days as $en_day => $fa_day) {
        $weekly_presence[$en_day] = [
            'fa_name' => $fa_day,
            'teachers' => []
        ];
    }
    
    // پر کردن اطلاعات معلمان برای هر روز
    foreach ($teachers as $teacher) {
        $presence_days = $teacher['presence_days'];
        
        foreach ($week_days as $en_day => $fa_day) {
            // بررسی وجود روز در فیلد SET
            if (strpos($presence_days, $en_day) !== false) {
                $weekly_presence[$en_day]['teachers'][] = [
                    'id' => $teacher['id'],
                    'name' => $teacher['first_name'] . ' ' . $teacher['last_name'],
                    'mobile' => $teacher['mobile']
                ];
            }
        }
    }
    
    return $weekly_presence;
}

private function getTodayEnglish() {
    $english_days = [
        0 => 'sunday',    // یکشنبه
        1 => 'monday',    // دوشنبه  
        2 => 'tuesday',   // سه‌شنبه
        3 => 'wednesday', // چهارشنبه
        4 => 'thursday',  // پنجشنبه
        5 => 'friday',    // جمعه
        6 => 'saturday'   // شنبه
    ];
    
    return $english_days[date('w')];
}
// اضافه کردن این متد به کلاس Teacher
public function searchTeachers($searchTerm) {
    $query = "SELECT t.*, u.first_name, u.last_name, u.mobile, u.national_code
              FROM teachers t
              JOIN users u ON t.user_id = u.id
              WHERE u.first_name LIKE ? OR u.last_name LIKE ? OR u.mobile LIKE ? OR u.national_code LIKE ?
              ORDER BY u.last_name, u.first_name
              LIMIT 10";
    $stmt = $this->db->prepare($query);
    $searchTerm = "%$searchTerm%";
    $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// اضافه کردن متد بررسی تکراری بودن تخصیص
public function checkAssignmentExists($teacher_id, $course_id, $class_id = null) {
    $query = "SELECT COUNT(*) as count FROM teacher_courses 
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
}
?>