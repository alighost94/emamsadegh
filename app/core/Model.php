<?php
class Model {
    protected $db;
    protected $table;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    // اضافه کردن این متد برای کشینگ
    protected function getCachedData($key, $callback, $ttl = 3600) {
        $cache_dir = 'cache/';
        if (!is_dir($cache_dir)) {
            mkdir($cache_dir, 0755, true);
        }
        
        $cache_file = $cache_dir . md5($key) . '.cache';
        
        // بررسی وجود کش معتبر
        if (file_exists($cache_file) && (time() - filemtime($cache_file)) < $ttl) {
            return unserialize(file_get_contents($cache_file));
        }
        
        // دریافت داده از دیتابیس
        $data = $callback();
        
        // ذخیره در کش
        file_put_contents($cache_file, serialize($data));
        
        return $data;
    }

    public function getAll() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY id DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt;
    }
    
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $id);
        return $stmt->execute();
    }
}
?>