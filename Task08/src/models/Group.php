<?php
class Group {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getAll() {
        $sql = "SELECT * FROM groups ORDER BY group_number";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    public function getByNumber($group_number) {
        $sql = "SELECT * FROM groups WHERE group_number = :group_number";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':group_number', $group_number);
        $stmt->execute();
        
        return $stmt->fetch();
    }
}
?>