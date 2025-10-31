<?php
class User extends Model {
    protected $table = 'users';
    
    public function login($mobile, $password) {
        $query = "SELECT u.*, r.name AS role_name 
                  FROM users u 
                  JOIN roles r ON u.role_id = r.id 
                  WHERE u.mobile = ? AND u.is_active = 1
                  LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $mobile);
        $stmt->execute();
    
        if ($stmt->rowCount() == 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
            // بررسی قفل موقت
            if ($user['failed_attempts'] >= 5 && strtotime($user['last_failed_login']) > strtotime('-5 minutes')) {
                return ['error' => 'به دلیل تلاش‌های متعدد ناموفق، حساب شما به طور موقت قفل شده است.'];
            }
    
            // بررسی رمز عبور
            if (password_verify($password, $user['password'])) {
                $this->resetFailedAttempts($user['id']);
                return $user;
            } else {
                $this->increaseFailedAttempts($user['id']);
                return false;
            }
        }
    
        return false;
    }
    private function increaseFailedAttempts($userId) {
        $query = "UPDATE users 
                  SET failed_attempts = failed_attempts + 1, 
                      last_failed_login = NOW() 
                  WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $userId);
        $stmt->execute();
    }
    
    private function resetFailedAttempts($userId) {
        $query = "UPDATE users SET failed_attempts = 0 WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $userId);
        $stmt->execute();
    }
    public function updatePassword($userId, $newPassword) {
        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
        $query = "UPDATE users 
                  SET password = ?, must_change_password = 0 
                  WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$hashed, $userId]);
    }
    
    public function clearMustChangeFlag($userId) {
        $query = "UPDATE users SET must_change_password = 0 WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$userId]);
    }
            
    
    private function migrateToHashedPassword($userId, $password) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $query = "UPDATE users SET password = ? WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $hashedPassword);
        $stmt->bindParam(2, $userId);
        $stmt->execute();
    }
    
    public function logLoginAttempt($data) {
        $query = "INSERT INTO login_logs (mobile, ip_address, user_agent, timestamp, status) 
                  VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $data['mobile']);
        $stmt->bindParam(2, $data['ip']);
        $stmt->bindParam(3, $data['user_agent']);
        $stmt->bindParam(4, $data['timestamp']);
        $stmt->bindParam(5, $data['status']);
        $stmt->execute();
    }
    
    public function create($data) {
        try {
            $this->db->beginTransaction();
            
            // ایجاد کاربر در جدول users
            $query = "INSERT INTO users (mobile, national_code, password, role_id, first_name, last_name) 
                      VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($query);
            
            // 🔥 تغییر اینجا - هش کردن رمز عبور
            $password = password_hash($data['national_code'], PASSWORD_DEFAULT);
            
            $stmt->execute([
                $data['mobile'],
                $data['national_code'],
                $password, // 🔥 حالا هش شده است
                $data['role_id'],
                $data['first_name'],
                $data['last_name']
            ]);
            
            $user_id = $this->db->lastInsertId();
            
            // ایجاد رکورد در جدول مربوطه بر اساس نقش
            $role_created = $this->createRoleSpecificRecord($user_id, $data['role_id'], $data);
            
            if ($role_created) {
                $this->db->commit();
                return true;
            } else {
                $this->db->rollBack();
                return false;
            }
            
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
    private function createRoleSpecificRecord($user_id, $role_id, $data) {
        switch ($role_id) {
            case 2: // دانش‌آموز
                return $this->createStudentRecord($user_id, $data);
            case 3: // معلم
                return $this->createTeacherRecord($user_id, $data);
            case 4: // اولیا
                return $this->createParentRecord($user_id, $data);
            case 5: // معاون
                return $this->createAssistantRecord($user_id, $data);
            default: // مدیر - نیاز به رکورد خاصی ندارد
                return true;
        }
    }
    
    private function createStudentRecord($user_id, $data) {
        $query = "INSERT INTO students (user_id, student_number, class_id, birth_date, father_name, address) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        
        // تولید شماره دانش‌آموزی خودکار
        $student_number = 'STU' . date('Y') . str_pad($user_id, 4, '0', STR_PAD_LEFT);
        
        return $stmt->execute([
            $user_id, 
            $student_number,
            $data['class_id'] ?? null,
            $data['birth_date'] ?? null,
            $data['father_name'] ?? '',
            $data['address'] ?? ''
        ]);
    }
    
    private function createTeacherRecord($user_id, $data) {
        $query = "INSERT INTO teachers (user_id, expertise) 
                  VALUES (?, ?)";
        $stmt = $this->db->prepare($query);
        
        $expertise = $data['expertise'] ?? 'در حال تعیین';
        
        return $stmt->execute([$user_id, $expertise]);
    }
    
    private function createParentRecord($user_id, $data) {
        $query = "INSERT INTO parents (user_id, student_id, relation_type) 
                  VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($query);
        
        // برای اولیا، نیاز به انتخاب دانش‌آموز داریم
        // فعلاً با مقدار پیش‌فرض ایجاد می‌کنیم
        $student_id = $data['student_id'] ?? null;
        $relation_type = $data['relation_type'] ?? 'father';
        
        if ($student_id) {
            return $stmt->execute([$user_id, $student_id, $relation_type]);
        } else {
            // اگر دانش‌آموزی انتخاب نشده، فقط کاربر ایجاد شود
            return true;
        }
    }
    
    private function createAssistantRecord($user_id, $data) {
        $query = "INSERT INTO assistants (user_id, grade_id) 
                  VALUES (?, ?)";
        $stmt = $this->db->prepare($query);
        
        $grade_id = $data['grade_id'] ?? 1; // پیش‌فرض پایه دهم
        
        return $stmt->execute([$user_id, $grade_id]);
    }
    
    public function getUsersWithRole() {
        $query = "SELECT u.*, r.name as role_name,
                         s.class_id, s.student_number,
                         c.name as class_name, m.name as major_name, g.name as grade_name
                  FROM users u 
                  JOIN roles r ON u.role_id = r.id 
                  LEFT JOIN students s ON u.id = s.user_id
                  LEFT JOIN classes c ON s.class_id = c.id
                  LEFT JOIN majors m ON c.major_id = m.id
                  LEFT JOIN grades g ON c.grade_id = g.id
                  ORDER BY u.id DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getAllRoles() {
        $query = "SELECT * FROM roles ORDER BY id";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // متد جدید برای دریافت اطلاعات کامل کاربر بر اساس نقش



    public function getUserWithRoleDetails($user_id) {
        $query = "SELECT u.*, r.name as role_name 
                  FROM users u 
                  JOIN roles r ON u.role_id = r.id 
                  WHERE u.id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $user_id);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            // دریافت اطلاعات اضافی بر اساس نقش
            switch ($user['role_id']) {
                case 2: // دانش‌آموز
                    $studentModel = new Student($this->db);
                    $user['student_info'] = $studentModel->getByUserId($user_id);
                    break;
                case 3: // معلم
                    $teacherModel = new Teacher($this->db);
                    $user['teacher_info'] = $teacherModel->getByUserId($user_id);
                    break;
                case 4: // اولیا
                    $parentModel = new ParentModel($this->db);
                    $user['parent_info'] = $parentModel->getByUserId($user_id);
                    break;
                case 5: // معاون
                    $assistantModel = new Assistant($this->db);
                    $user['assistant_info'] = $assistantModel->getByUserId($user_id);
                    break;
            }
        }
        
        return $user;
    }
}
?>