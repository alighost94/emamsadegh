<?php
class MessageLog extends Model {
    protected $table = 'message_logs';
    
    public function create($data) {
        $query = "INSERT INTO " . $this->table . " 
                  (disciplinary_record_id, parent_id, message_text, bale_message_id, status, sent_at) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            $data['disciplinary_record_id'],
            $data['parent_id'],
            $data['message_text'],
            $data['bale_message_id'],
            $data['status'],
            $data['sent_at']
        ]);
    }
    
    public function getByDisciplinaryRecord($disciplinary_record_id) {
        $query = "SELECT ml.*, u.first_name, u.last_name, u.mobile
                  FROM " . $this->table . " ml
                  JOIN parents p ON ml.parent_id = p.id
                  JOIN users u ON p.user_id = u.id
                  WHERE ml.disciplinary_record_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$disciplinary_record_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function updateStatus($id, $status, $bale_message_id = null) {
        $query = "UPDATE " . $this->table . " 
                  SET status = ?, bale_message_id = ?, sent_at = NOW() 
                  WHERE id = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$status, $bale_message_id, $id]);
    }
    
    public function getFailedMessages() {
        $query = "SELECT ml.*, u.first_name, u.last_name, u.mobile,
                         dr.violation_type, dr.jalali_date,
                         s.first_name as student_first_name, s.last_name as student_last_name
                  FROM " . $this->table . " ml
                  JOIN parents p ON ml.parent_id = p.id
                  JOIN users u ON p.user_id = u.id
                  JOIN disciplinary_records dr ON ml.disciplinary_record_id = dr.id
                  JOIN students st ON dr.student_id = st.id
                  JOIN users s ON st.user_id = s.id
                  WHERE ml.status = 'failed'
                  ORDER BY ml.created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>