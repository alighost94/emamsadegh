<?php
class Parent_old extends Model {
    protected $table = 'parents';
    
    public function create($data) {
        $query = "INSERT INTO parents (user_id, student_id, relation_type) 
                  VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            $data['user_id'],
            $data['student_id'],
            $data['relation_type']
        ]);
    }
    
    public function getByUserId($user_id) {
        $query = "SELECT p.*, u.first_name, u.last_name, u.mobile,
                         s.student_number, s2.first_name as student_first_name, 
                         s2.last_name as student_last_name
                  FROM parents p
                  JOIN users u ON p.user_id = u.id
                  JOIN students s ON p.student_id = s.id
                  JOIN users s2 ON s.user_id = s2.id
                  WHERE p.user_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $user_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>