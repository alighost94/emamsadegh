<?php
class AssistantStudent extends Model {
    protected $table = 'students';
    
    public function createStudentWithParent($studentData, $parentData) {
        try {
            $this->db->beginTransaction();
            
            // 1. ایجاد کاربر دانش‌آموز
            // 🔥 بارگذاری مدل User
            require_once 'app/models/User.php';
            $userModel = new User($this->db);
            
            $userData = [
                'mobile' => $studentData['mobile'],
                'national_code' => $studentData['national_code'],
                'role_id' => 2, // دانش‌آموز
                'first_name' => $studentData['first_name'],
                'last_name' => $studentData['last_name']
            ];
            
            if (!$userModel->create($userData)) {
                throw new Exception('خطا در ایجاد کاربر دانش‌آموز');
            }
            
            $student_user_id = $this->db->lastInsertId();
            
            // 2. ایجاد رکورد دانش‌آموز
            $studentRecordData = [
                'user_id' => $student_user_id,
                'class_id' => $studentData['class_id'],
                'student_number' => $this->generateStudentNumber($student_user_id),
                'birth_date' => $studentData['birth_date'],
                'father_name' => $studentData['father_name'],
                'address' => $studentData['address']
            ];
            
            $query = "INSERT INTO students (user_id, class_id, student_number, birth_date, father_name, address) 
                      VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($query);
            
            if (!$stmt->execute([
                $studentRecordData['user_id'],
                $studentRecordData['class_id'],
                $studentRecordData['student_number'],
                $studentRecordData['birth_date'],
                $studentRecordData['father_name'],
                $studentRecordData['address']
            ])) {
                throw new Exception('خطا در ایجاد رکورد دانش‌آموز');
            }
            
            $student_id = $this->db->lastInsertId();
            
            // 3. ایجاد اولیا (اگر اطلاعات وارد شده)
            if (!empty($parentData['parent_mobile']) && !empty($parentData['parent_first_name'])) {
                $parentCreated = $this->createParentForStudent($parentData, $student_id);
                if (!$parentCreated) {
                    throw new Exception('خطا در ایجاد اولیای دانش‌آموز');
                }
            }
            
            $this->db->commit();
            return [
                'success' => true,
                'student_id' => $student_id,
                'student_user_id' => $student_user_id
            ];
            
        } catch (Exception $e) {
            $this->db->rollBack();
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    private function createParentForStudent($parentData, $student_id) {
        // 🔥 بارگذاری مدل‌های مورد نیاز
        require_once 'app/models/User.php';
        require_once 'app/models/ParentModel.php';
        
        $userModel = new User($this->db);
        $parentModel = new ParentModel($this->db);
        
        // ایجاد کاربر اولیا
        $parentUserData = [
            'mobile' => $parentData['parent_mobile'],
            'national_code' => $parentData['parent_national_code'],
            'role_id' => 4, // اولیا
            'first_name' => $parentData['parent_first_name'],
            'last_name' => $parentData['parent_last_name']
        ];
        
        if (!$userModel->create($parentUserData)) {
            return false;
        }
        
        $parent_user_id = $this->db->lastInsertId();
        
        // ایجاد رکورد اولیا
        $parentRecordData = [
            'user_id' => $parent_user_id,
            'student_id' => $student_id,
            'relation_type' => $parentData['relation_type'] ?? 'father'
        ];
        
        return $parentModel->create($parentRecordData);
    }
    
    private function generateStudentNumber($user_id) {
        return 'STU' . date('Y') . str_pad($user_id, 4, '0', STR_PAD_LEFT);
    }
    
    // 🔥 اضافه کردن متد برای دریافت کلاس‌های تحت مدیریت معاون
    public function getClassesByAssistantGrade($grade_id) {
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
}
?>