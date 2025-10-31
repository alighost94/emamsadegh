<?php
class TeacherProfile extends Model {
    protected $table = 'teacher_profiles';
    
    public function getByTeacherId($teacher_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE teacher_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $teacher_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function createOrUpdate($data) {
        // بررسی وجود پروفایل
        $existing = $this->getByTeacherId($data['teacher_id']);
        
        if ($existing) {
            // آپدیت
            $query = "UPDATE " . $this->table . " SET 
                      personnel_code = ?, national_card_image = ?, birth_certificate_image = ?, 
                      decree_file = ?, resume_file = ?, profile_image = ?, 
                      bank_account_number = ?, bank_card_number = ?, education_degree = ?, 
                      major = ?, postal_code = ?, address = ?, presence_days = ?, 
                      terms_accepted = ?, profile_completed = ?, updated_at = NOW() 
                      WHERE teacher_id = ?";
            $stmt = $this->db->prepare($query);
            return $stmt->execute([
                $data['personnel_code'],
                $data['national_card_image'],
                $data['birth_certificate_image'],
                $data['decree_file'],
                $data['resume_file'],
                $data['profile_image'],
                $data['bank_account_number'],
                $data['bank_card_number'],
                $data['education_degree'],
                $data['major'],
                $data['postal_code'],
                $data['address'],
                $data['presence_days'],
                $data['terms_accepted'],
                $data['profile_completed'],
                $data['teacher_id']
            ]);
        } else {
            // ایجاد جدید
            $query = "INSERT INTO " . $this->table . " 
                      (teacher_id, personnel_code, national_card_image, birth_certificate_image, 
                       decree_file, resume_file, profile_image, bank_account_number, bank_card_number, 
                       education_degree, major, postal_code, address, presence_days, terms_accepted, profile_completed) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($query);
            return $stmt->execute([
                $data['teacher_id'],
                $data['personnel_code'],
                $data['national_card_image'],
                $data['birth_certificate_image'],
                $data['decree_file'],
                $data['resume_file'],
                $data['profile_image'],
                $data['bank_account_number'],
                $data['bank_card_number'],
                $data['education_degree'],
                $data['major'],
                $data['postal_code'],
                $data['address'],
                $data['presence_days'],
                $data['terms_accepted'],
                $data['profile_completed']
            ]);
        }
    }
    
    public function completeProfile($teacher_id) {
        $query = "UPDATE " . $this->table . " SET profile_completed = 1 WHERE teacher_id = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$teacher_id]);
    }
}
?>