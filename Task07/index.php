<?php
// index.php
// Настройки подключения к БД
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'password');
define('DB_NAME', 'university');

// Подключение к базе данных
function connectDB() {
    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if (!$conn) {
        die("Ошибка подключения: " . mysqli_connect_error());
    }
    
    mysqli_set_charset($conn, "utf8");
    return $conn;
}

// Получение списка действующих групп
function getActiveGroups($conn) {
    $currentYear = date('Y');
    $query = "SELECT DISTINCT group_number 
              FROM student_groups 
              WHERE graduation_year >= $currentYear 
              ORDER BY group_number";
    
    $result = mysqli_query($conn, $query);
    $groups = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $groups[] = $row['group_number'];
    }
    
    return $groups;
}

// Получение списка студентов
function getStudents($conn, $selectedGroup = null) {
    $currentYear = date('Y');
    $query = "SELECT 
                sg.group_number,
                sg.direction,
                s.last_name,
                s.first_name,
                s.middle_name,
                s.gender,
                s.birth_date,
                s.student_id
              FROM students s
              JOIN student_groups sg ON s.group_id = sg.id
              WHERE sg.graduation_year >= $currentYear";
    
    if ($selectedGroup && $selectedGroup !== 'all') {
        $query .= " AND sg.group_number = '$selectedGroup'";
    }
    
    $query .= " ORDER BY sg.group_number, s.last_name, s.first_name";
    
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Обработка данных
$conn = connectDB();
$activeGroups = getActiveGroups($conn);
$selectedGroup = $_GET['group'] ?? 'all';
$students = getStudents($conn, $selectedGroup);
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Список студентов</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f5f5;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }
        
        h1 {
            color: #2c3e50;
            margin-bottom: 30px;
            text-align: center;
            border-bottom: 2px solid #3498db;
            padding-bottom: 15px;
        }
        
        .filter-form {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 6px;
            margin-bottom: 30px;
            border: 1px solid #dee2e6;
        }
        
        .filter-group {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        label {
            font-weight: 600;
            color: #495057;
        }
        
        select {
            padding: 8px 12px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-size: 16px;
            flex-grow: 1;
            max-width: 300px;
        }
        
        button {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        
        button:hover {
            background-color: #2980b9;
        }
        
        .students-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .students-table th {
            background-color: #3498db;
            color: white;
            text-align: left;
            padding: 12px 15px;
            position: sticky;
            top: 0;
        }
        
        .students-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #dee2e6;
        }
        
        .students-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .students-table tr:hover {
            background-color: #e9ecef;
        }
        
        .no-data {
            text-align: center;
            padding: 40px;
            color: #6c757d;
            font-style: italic;
        }
        
        .gender-male {
            color: #3498db;
            font-weight: 600;
        }
        
        .gender-female {
            color: #e84393;
            font-weight: 600;
        }
        
        .group-badge {
            display: inline-block;
            background-color: #2ecc71;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 14px;
        }
        
        .student-id {
            font-family: monospace;
            background-color: #f8f9fa;
            padding: 4px 8px;
            border-radius: 4px;
            border: 1px solid #dee2e6;
        }
        
        .stats {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 6px;
            border-left: 4px solid #3498db;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }
            
            .filter-group {
                flex-direction: column;
                align-items: stretch;
            }
            
            select {
                max-width: 100%;
            }
            
            .students-table {
                display: block;
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📚 Список студентов</h1>
        
        <div class="filter-form">
            <form method="GET" action="">
                <div class="filter-group">
                    <label for="group">Выберите группу:</label>
                    <select name="group" id="group">
                        <option value="all" <?php echo ($selectedGroup === 'all') ? 'selected' : ''; ?>>Все группы</option>
                        <?php foreach ($activeGroups as $group): ?>
                            <option value="<?php echo htmlspecialchars($group); ?>" 
                                    <?php echo ($selectedGroup === $group) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($group); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit">Показать</button>
                </div>
            </form>
        </div>
        
        <?php if (!empty($students)): ?>
            <div class="stats">
                Найдено студентов: <strong><?php echo count($students); ?></strong>
                <?php if ($selectedGroup !== 'all'): ?>
                    | Группа: <span class="group-badge"><?php echo htmlspecialchars($selectedGroup); ?></span>
                <?php endif; ?>
            </div>
            
            <table class="students-table">
                <thead>
                    <tr>
                        <th>Группа</th>
                        <th>Направление подготовки</th>
                        <th>ФИО</th>
                        <th>Пол</th>
                        <th>Дата рождения</th>
                        <th>Номер студенческого билета</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $student): ?>
                        <tr>
                            <td>
                                <span class="group-badge"><?php echo htmlspecialchars($student['group_number']); ?></span>
                            </td>
                            <td><?php echo htmlspecialchars($student['direction']); ?></td>
                            <td>
                                <?php 
                                    echo htmlspecialchars(
                                        $student['last_name'] . ' ' . 
                                        $student['first_name'] . ' ' . 
                                        $student['middle_name']
                                    ); 
                                ?>
                            </td>
                            <td>
                                <?php if ($student['gender'] == 'M'): ?>
                                    <span class="gender-male">Мужской</span>
                                <?php else: ?>
                                    <span class="gender-female">Женский</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo date('d.m.Y', strtotime($student['birth_date'])); ?></td>
                            <td>
                                <span class="student-id"><?php echo htmlspecialchars($student['student_id']); ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-data">
                <p>Нет данных для отображения. Студенты не найдены.</p>
                <p>Попробуйте выбрать другую группу.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>