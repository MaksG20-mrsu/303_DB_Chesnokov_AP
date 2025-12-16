<?php
require_once __DIR__ . '/../models/Exam.php';
require_once __DIR__ . '/../models/Student.php';

class ExamController {
    public function index($student_id) {
        $examModel = new Exam();
        $exams = $examModel->getByStudent($student_id);
        
        $studentModel = new Student();
        $student = $studentModel->getById($student_id);
        
        require_once __DIR__ . '/../views/exams/index.php';
    }
    
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $examModel = new Exam();
            
            $data = [
                ':student_id' => $_POST['student_id'],
                ':discipline_id' => $_POST['discipline_id'],
                ':exam_date' => $_POST['exam_date'],
                ':grade' => $_POST['grade'],
                ':course' => $_POST['course'],
                ':semester' => $_POST['semester']
            ];
            
            $examModel->create($data);
            
            header('Location: index.php?action=exams&student_id=' . $_POST['student_id']);
            exit();
        }
        
        $examModel = new Exam();
        $students = $examModel->getStudents();
        $disciplines = $examModel->getAllDisciplines();
        
        require_once __DIR__ . '/../views/exams/create.php';
    }
    
    public function update($id) {
        $examModel = new Exam();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                ':discipline_id' => $_POST['discipline_id'],
                ':exam_date' => $_POST['exam_date'],
                ':grade' => $_POST['grade'],
                ':course' => $_POST['course'],
                ':semester' => $_POST['semester']
            ];
            
            $examModel->update($id, $data);
            
            $exam = $examModel->getById($id);
            header('Location: index.php?action=exams&student_id=' . $exam['student_id']);
            exit();
        }
        
        $exam = $examModel->getById($id);
        $disciplines = $examModel->getAllDisciplines();
        
        require_once __DIR__ . '/../views/exams/edit.php';
    }
    
    public function delete($id) {
        $examModel = new Exam();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm'])) {
            $exam = $examModel->getById($id);
            $examModel->delete($id);
            
            header('Location: index.php?action=exams&student_id=' . $exam['student_id']);
            exit();
        }
        
        $exam = $examModel->getById($id);
        
        require_once __DIR__ . '/../views/exams/delete.php';
    }
}
?>