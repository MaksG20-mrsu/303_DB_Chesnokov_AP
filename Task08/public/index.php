<?php
session_start();
require_once __DIR__ . '/../src/config/Database.php';

// Установка кодировки
header('Content-Type: text/html; charset=utf-8');

// Определение действия
$action = $_GET['action'] ?? 'students';
$subaction = $_GET['subaction'] ?? '';
$id = $_GET['id'] ?? null;

// Обработка запросов
try {
    switch ($action) {
        case 'exams':
            require_once __DIR__ . '/../src/controllers/ExamController.php';
            $controller = new ExamController();
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                switch ($subaction) {
                    case 'create':
                        $controller->create();
                        break;
                    case 'update':
                        if ($id) {
                            $controller->update($id);
                        }
                        break;
                    case 'delete':
                        if ($id) {
                            $controller->delete($id);
                        }
                        break;
                    default:
                        // По умолчанию показываем список экзаменов
                        $student_id = $_GET['student_id'] ?? null;
                        if ($student_id) {
                            $controller->index($student_id);
                        } else {
                            header('Location: index.php');
                            exit();
                        }
                }
            } else {
                switch ($subaction) {
                    case 'create':
                        $controller->create();
                        break;
                    case 'edit':
                        if ($id) {
                            require_once __DIR__ . '/../src/controllers/ExamController.php';
                            $controller = new ExamController();
                            
                            // Проверяем, есть ли POST-данные для обновления
                            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                                $controller->update($id);
                            } else {
                                // Показываем форму редактирования
                                $exam = $controller->getById($id);
                                $disciplines = $controller->getAllDisciplines();
                                require_once __DIR__ . '/../src/views/exams/edit.php';
                            }
                        }
                        break;
                    case 'delete':
                        if ($id) {
                            $controller->delete($id);
                        }
                        break;
                    default:
                        $student_id = $_GET['student_id'] ?? null;
                        if ($student_id) {
                            $controller->index($student_id);
                        } else {
                            header('Location: index.php');
                            exit();
                        }
                }
            }
            break;
            
        case 'students':
        default:
            require_once __DIR__ . '/../src/controllers/StudentController.php';
            $controller = new StudentController();
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                switch ($subaction) {
                    case 'create':
                        $controller->create();
                        break;
                    case 'update':
                        if ($id) {
                            $controller->update($id);
                        }
                        break;
                    case 'delete':
                        if ($id) {
                            $controller->delete($id);
                        }
                        break;
                }
            } else {
                switch ($subaction) {
                    case 'create':
                        $controller->create();
                        break;
                    case 'edit':
                        if ($id) {
                            // Проверяем, есть ли POST-данные для обновления
                            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                                $controller->update($id);
                            } else {
                                // Показываем форму редактирования
                                $student = $controller->getById($id);
                                $groups = $controller->getGroups();
                                require_once __DIR__ . '/../src/views/students/edit.php';
                            }
                        }
                        break;
                    case 'delete':
                        if ($id) {
                            $controller->delete($id);
                        }
                        break;
                    default:
                        $controller->index();
                }
            }
            break;
    }
} catch (Exception $e) {
    // Обработка ошибок
    error_log('Error: ' . $e->getMessage());
    http_response_code(500);
    echo '<div class="container">';
    echo '<h1>Ошибка</h1>';
    echo '<p>Произошла ошибка при обработке запроса.</p>';
    if (isset($_SESSION['debug']) && $_SESSION['debug']) {
        echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
    }
    echo '<a href="index.php" class="btn btn-secondary">Вернуться на главную</a>';
    echo '</div>';
}
?>