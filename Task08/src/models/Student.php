<?php
class Student {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getAll($group_filter = null) {
        $sql = "SELECT s.*, g.group_number 
                FROM students s 
                JOIN groups g ON s.group_id = g.id";
        
        if ($group_filter) {
            $sql .= " WHERE g.group_number = :group_number";
        }
        
        $sql .= " ORDER BY g.group_number, s.last_name";
        
        $stmt = $this->db->prepare($sql);
        
        if ($group_filter) {
            $stmt->bindParam(':group_number', $group_filter);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getById($id) {
        $sql = "SELECT s.*, g.group_number 
                FROM students s 
                JOIN groups g ON s.group_id = g.id 
                WHERE s.id = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    public function create($data) {
        $sql = "INSERT INTO students (last_name, first_name, middle_name, group_id, gender, birth_date, admission_year) 
                VALUES (:last_name, :first_name, :middle_name, :group_id, :gender, :birth_date, :admission_year)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }
    
    public function update($id, $data) {
        $sql = "UPDATE students SET 
                last_name = :last_name,
                first_name = :first_name,
                middle_name = :middle_name,
                group_id = :group_id,
                gender = :gender,
                birth_date = :birth_date,
                admission_year = :admission_year
                WHERE id = :id";
        
        $data['id'] = $id;
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }
    
    public function delete($id) {
        $sql = "DELETE FROM students WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
    
    public function getGroups() {
        $sql = "SELECT * FROM groups ORDER BY group_number";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
}
?>