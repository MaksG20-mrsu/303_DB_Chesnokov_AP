<?php
require_once __DIR__ . '/../models/Student.php';

class StudentController {
    public function index() {
        $studentModel = new Student();
        $group_filter = $_GET['group_filter'] ?? null;
        $students = $studentModel->getAll($group_filter);
        $groups = $studentModel->getGroups();
        
        require_once __DIR__ . '/../views/students/index.php';
    }
    
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $studentModel = new Student();
            
            $data = [
                ':last_name' => $_POST['last_name'],
                ':first_name' => $_POST['first_name'],
                ':middle_name' => $_POST['middle_name'],
                ':group_id' => $_POST['group_id'],
                ':gender' => $_POST['gender'],
                ':birth_date' => $_POST['birth_date'],
                ':admission_year' => $_POST['admission_year']
            ];
            
            $studentModel->create($data);
            
            header('Location: index.php');
            exit();
        }
        
        require_once __DIR__ . '/../views/students/create.php';
    }
    
    public function update($id) {
        $studentModel = new Student();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                ':last_name' => $_POST['last_name'],
                ':first_name' => $_POST['first_name'],
                ':middle_name' => $_POST['middle_name'],
                ':group_id' => $_POST['group_id'],
                ':gender' => $_POST['gender'],
                ':birth_date' => $_POST['birth_date'],
                ':admission_year' => $_POST['admission_year']
            ];
            
            $studentModel->update($id, $data);
            
            header('Location: index.php');
            exit();
        }
        
        $student = $studentModel->getById($id);
        $groups = $studentModel->getGroups();
        
        require_once __DIR__ . '/../views/students/edit.php';
    }
    
    public function delete($id) {
        $studentModel = new Student();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm'])) {
            $studentModel->delete($id);
            header('Location: index.php');
            exit();
        }
        
        $student = $studentModel->getById($id);
        
        require_once __DIR__ . '/../views/students/delete.php';
    }
}
?>