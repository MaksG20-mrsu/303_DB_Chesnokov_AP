<?php
class Exam {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getByStudent($student_id) {
        $sql = "SELECT e.*, d.name as discipline_name, s.last_name, s.first_name 
                FROM exams e 
                JOIN disciplines d ON e.discipline_id = d.id 
                JOIN students s ON e.student_id = s.id 
                WHERE e.student_id = :student_id 
                ORDER BY e.exam_date";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':student_id', $student_id);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    public function getById($id) {
        $sql = "SELECT e.*, d.name as discipline_name, s.last_name, s.first_name, s.group_id, g.study_direction
                FROM exams e 
                JOIN disciplines d ON e.discipline_id = d.id 
                JOIN students s ON e.student_id = s.id 
                JOIN groups g ON s.group_id = g.id 
                WHERE e.id = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    public function create($data) {
        $sql = "INSERT INTO exams (student_id, discipline_id, exam_date, grade, course, semester) 
                VALUES (:student_id, :discipline_id, :exam_date, :grade, :course, :semester)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }
    
    public function update($id, $data) {
        $sql = "UPDATE exams SET 
                discipline_id = :discipline_id,
                exam_date = :exam_date,
                grade = :grade,
                course = :course,
                semester = :semester
                WHERE id = :id";
        
        $data['id'] = $id;
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }
    
    public function delete($id) {
        $sql = "DELETE FROM exams WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
    
    public function getStudents() {
        $sql = "SELECT id, last_name, first_name, middle_name FROM students ORDER BY last_name";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    public function getDisciplinesByStudent($student_id) {
        $sql = "SELECT d.* 
                FROM disciplines d 
                JOIN students s ON d.study_direction = (
                    SELECT g.study_direction 
                    FROM groups g 
                    WHERE g.id = s.group_id
                )
                WHERE s.id = :student_id 
                AND d.course = :course
                ORDER BY d.name";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':student_id', $student_id);
        
        // Получаем курс студента на момент сдачи экзамена
        // В реальном приложении здесь будет логика определения курса
        
        return $stmt->fetchAll();
    }
    
    public function getAllDisciplines() {
        $sql = "SELECT * FROM disciplines ORDER BY name";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
}
?>