#!/usr/bin/env php
<?php
// task7_cli.php

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
    
    if ($selectedGroup) {
        $query .= " AND sg.group_number = '$selectedGroup'";
    }
    
    $query .= " ORDER BY sg.group_number, s.last_name, s.first_name";
    
    return mysqli_query($conn, $query);
}

// Валидация номера группы
function validateGroup($group, $activeGroups) {
    if (empty($group)) {
        return true; // Пустое значение - вывод всех групп
    }
    
    return in_array($group, $activeGroups);
}

// Вывод таблицы с псевдографикой
function printTable($students) {
    if (mysqli_num_rows($students) === 0) {
        echo "Нет данных для отображения.\n";
        return;
    }
    
    // Определяем ширину колонок
    $widths = [
        'group' => 12,
        'direction' => 30,
        'fio' => 25,
        'gender' => 6,
        'birth_date' => 12,
        'student_id' => 18
    ];
    
    // Верхняя граница таблицы
    echo "┌" . str_repeat("─", $widths['group'] + 2) . 
         "┬" . str_repeat("─", $widths['direction'] + 2) .
         "┬" . str_repeat("─", $widths['fio'] + 2) .
         "┬" . str_repeat("─", $widths['gender'] + 2) .
         "┬" . str_repeat("─", $widths['birth_date'] + 2) .
         "┬" . str_repeat("─", $widths['student_id'] + 2) . "┐\n";
    
    // Заголовки
    printf("│ %-{$widths['group']}s │ %-{$widths['direction']}s │ %-{$widths['fio']}s │ %-{$widths['gender']}s │ %-{$widths['birth_date']}s │ %-{$widths['student_id']}s │\n",
           "Группа", "Направление", "ФИО", "Пол", "Дата рождения", "№ студ. билета");
    
    // Разделитель заголовков
    echo "├" . str_repeat("─", $widths['group'] + 2) . 
         "┼" . str_repeat("─", $widths['direction'] + 2) .
         "┼" . str_repeat("─", $widths['fio'] + 2) .
         "┼" . str_repeat("─", $widths['gender'] + 2) .
         "┼" . str_repeat("─", $widths['birth_date'] + 2) .
         "┼" . str_repeat("─", $widths['student_id'] + 2) . "┤\n";
    
    // Данные
    while ($row = mysqli_fetch_assoc($students)) {
        $fio = $row['last_name'] . ' ' . $row['first_name'] . ' ' . $row['middle_name'];
        $birthDate = date('d.m.Y', strtotime($row['birth_date']));
        $gender = $row['gender'] == 'M' ? 'М' : 'Ж';
        
        printf("│ %-{$widths['group']}s │ %-{$widths['direction']}s │ %-{$widths['fio']}s │ %-{$widths['gender']}s │ %-{$widths['birth_date']}s │ %-{$widths['student_id']}s │\n",
               $row['group_number'], 
               mb_substr($row['direction'], 0, $widths['direction']),
               mb_substr($fio, 0, $widths['fio']),
               $gender,
               $birthDate,
               $row['student_id']);
    }
    
    // Нижняя граница таблицы
    echo "└" . str_repeat("─", $widths['group'] + 2) . 
         "┴" . str_repeat("─", $widths['direction'] + 2) .
         "┴" . str_repeat("─", $widths['fio'] + 2) .
         "┴" . str_repeat("─", $widths['gender'] + 2) .
         "┴" . str_repeat("─", $widths['birth_date'] + 2) .
         "┴" . str_repeat("─", $widths['student_id'] + 2) . "┘\n";
}

// Основная логика программы
$conn = connectDB();
$activeGroups = getActiveGroups($conn);

echo "Доступные группы: " . implode(", ", $activeGroups) . "\n";
echo "Введите номер группы для фильтрации (или нажмите Enter для всех групп): ";
$input = trim(fgets(STDIN));

if (!validateGroup($input, $activeGroups)) {
    echo "Ошибка: указан неверный номер группы.\n";
    mysqli_close($conn);
    exit(1);
}

$students = getStudents($conn, $input ?: null);
printTable($students);

mysqli_close($conn);
?>