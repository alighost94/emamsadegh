<?php
class Major extends Model {
    protected $table = 'majors';
    
    public function getCount() {
        $query = "SELECT COUNT(*) as count FROM majors";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }
}
?>