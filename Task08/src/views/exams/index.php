<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Экзамены студента</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>Результаты экзаменов: <?= $student['last_name'] ?> <?= $student['first_name'] ?></h1>
        <a href="index.php" class="btn btn-secondary">← Назад к списку студентов</a>
        
        <!-- Таблица экзаменов -->
        <table class="exams-table">
            <thead>
                <tr>
                    <th>Дата экзамена</th>
                    <th>Дисциплина</th>
                    <th>Оценка</th>
                    <th>Курс</th>
                    <th>Семестр</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($exams as $exam): ?>
                    <tr>
                        <td><?= $exam['exam_date'] ?></td>
                        <td><?= $exam['discipline_name'] ?></td>
                        <td><?= $exam['grade'] ?></td>
                        <td><?= $exam['course'] ?></td>
                        <td><?= $exam['semester'] ?></td>
                        <td class="actions">
                            <a href="index.php?action=exams&subaction=edit&id=<?= $exam['id'] ?>" class="btn btn-edit">
                                Редактировать
                            </a>
                            <a href="index.php?action=exams&subaction=delete&id=<?= $exam['id'] ?>" class="btn btn-danger">
                                Удалить
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <!-- Кнопка добавления -->
        <div class="add-button">
            <a href="index.php?action=exams&subaction=create&student_id=<?= $student_id ?>" class="btn btn-primary">
                Добавить экзамен
            </a>
        </div>
    </div>
</body>
</html>