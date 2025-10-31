<?php
class AssistantProfile extends Model {
    protected $table = 'assistant_profiles';
    
    public function getByAssistantId($assistant_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE assistant_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $assistant_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function createOrUpdate($data) {
        // بررسی وجود پروفایل
        $existing = $this->getByAssistantId($data['assistant_id']);
        
        if ($existing) {
            // آپدیت - اضافه کردن is_retired
            $query = "UPDATE " . $this->table . " SET 
                      personnel_code = ?, national_card_image = ?, birth_certificate_image = ?, 
                      decree_file = ?, resume_file = ?, profile_image = ?, 
                      bank_account_number = ?, bank_card_number = ?, 
                      sheba_number = ?, father_name = ?, birth_date = ?,
                      education_degree = ?, major = ?, postal_code = ?, address = ?, 
                      presence_days = ?, terms_accepted = ?, is_retired = ?, profile_completed = ?, updated_at = NOW() 
                      WHERE assistant_id = ?";
            $stmt = $this->db->prepare($query);
            return $stmt->execute([
                $data['personnel_code'] ?? null,
                $data['national_card_image'] ?? null,
                $data['birth_certificate_image'] ?? null,
                $data['decree_file'] ?? null,
                $data['resume_file'] ?? null,
                $data['profile_image'] ?? null,
                $data['bank_account_number'] ?? null,
                $data['bank_card_number'] ?? null,
                $data['sheba_number'] ?? null,
                $data['father_name'] ?? null,
                $data['birth_date'] ?? null,
                $data['education_degree'] ?? null,
                $data['major'] ?? null,
                $data['postal_code'] ?? null,
                $data['address'] ?? null,
                $data['presence_days'] ?? null,
                $data['terms_accepted'] ?? 0,
                $data['is_retired'] ?? 0, // اضافه شد
                $data['profile_completed'] ?? 0,
                $data['assistant_id']
            ]);
        } else {
            // ایجاد جدید - اضافه کردن is_retired
            $query = "INSERT INTO " . $this->table . " 
                      (assistant_id, personnel_code, national_card_image, birth_certificate_image, 
                       decree_file, resume_file, profile_image, bank_account_number, bank_card_number, 
                       sheba_number, father_name, birth_date,
                       education_degree, major, postal_code, address, presence_days, terms_accepted, is_retired, profile_completed) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($query);
            return $stmt->execute([
                $data['assistant_id'],
                $data['personnel_code'] ?? null,
                $data['national_card_image'] ?? null,
                $data['birth_certificate_image'] ?? null,
                $data['decree_file'] ?? null,
                $data['resume_file'] ?? null,
                $data['profile_image'] ?? null,
                $data['bank_account_number'] ?? null,
                $data['bank_card_number'] ?? null,
                $data['sheba_number'] ?? null,
                $data['father_name'] ?? null,
                $data['birth_date'] ?? null,
                $data['education_degree'] ?? null,
                $data['major'] ?? null,
                $data['postal_code'] ?? null,
                $data['address'] ?? null,
                $data['presence_days'] ?? null,
                $data['terms_accepted'] ?? 0,
                $data['is_retired'] ?? 0, // اضافه شد
                $data['profile_completed'] ?? 0
            ]);
        }
    }
    
    public function completeProfile($assistant_id) {
        $query = "UPDATE " . $this->table . " SET profile_completed = 1 WHERE assistant_id = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$assistant_id]);
    }
}
?>