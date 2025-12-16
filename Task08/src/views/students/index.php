<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Студенты</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>Список студентов</h1>
        
        <!-- Фильтр по группе -->
        <form method="GET" class="filter-form">
            <select name="group_filter">
                <option value="">Все группы</option>
                <?php foreach ($groups as $group): ?>
                    <option value="<?= $group['group_number'] ?>" 
                        <?= ($_GET['group_filter'] ?? '') == $group['group_number'] ? 'selected' : '' ?>>
                        <?= $group['group_number'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Фильтровать</button>
        </form>
        
        <!-- Таблица студентов -->
        <table class="students-table">
            <thead>
                <tr>
                    <th>Группа</th>
                    <th>Фамилия</th>
                    <th>Имя</th>
                    <th>Отчество</th>
                    <th>Пол</th>
                    <th>Дата рождения</th>
                    <th>Год поступления</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $student): ?>
                    <tr>
                        <td><?= $student['group_number'] ?></td>
                        <td><?= $student['last_name'] ?></td>
                        <td><?= $student['first_name'] ?></td>
                        <td><?= $student['middle_name'] ?? '-' ?></td>
                        <td><?= $student['gender'] == 'M' ? 'Мужской' : 'Женский' ?></td>
                        <td><?= $student['birth_date'] ?></td>
                        <td><?= $student['admission_year'] ?></td>
                        <td class="actions">
                            <a href="index.php?action=exams&student_id=<?= $student['id'] ?>" class="btn btn-info">
                                Результаты экзаменов
                            </a>
                            <a href="index.php?action=students&subaction=edit&id=<?= $student['id'] ?>" class="btn btn-edit">
                                Редактировать
                            </a>
                            <a href="index.php?action=students&subaction=delete&id=<?= $student['id'] ?>" class="btn btn-danger">
                                Удалить
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <!-- Кнопка добавления -->
        <div class="add-button">
            <a href="index.php?action=students&subaction=create" class="btn btn-primary">Добавить студента</a>
        </div>
    </div>
</body>
</html>