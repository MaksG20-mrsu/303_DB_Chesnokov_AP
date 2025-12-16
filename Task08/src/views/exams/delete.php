<?php
$exam = $exam ?? null;
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Удалить экзамен</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>Удаление записи об экзамене</h1>
        
        <div class="confirmation-box">
            <p>Вы уверены, что хотите удалить запись об экзамене:</p>
            <p class="exam-info">
                <strong>Студент:</strong> <?= htmlspecialchars($exam['last_name']) ?> <?= htmlspecialchars($exam['first_name']) ?>
                <br>
                <strong>Дисциплина:</strong> <?= htmlspecialchars($exam['discipline_name']) ?>
                <br>
                <strong>Дата:</strong> <?= htmlspecialchars($exam['exam_date']) ?>
                <br>
                <strong>Оценка:</strong> <?= $exam['grade'] ?>
            </p>
            <p class="warning">Это действие нельзя отменить.</p>
            
            <form method="POST" action="index.php?action=exams&subaction=delete&id=<?= $exam['id'] ?>">
                <div class="form-actions">
                    <button type="submit" name="confirm" value="1" class="btn btn-danger">Да, удалить</button>
                    <a href="index.php?action=exams&student_id=<?= $exam['student_id'] ?>" class="btn btn-secondary">Отмена</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>