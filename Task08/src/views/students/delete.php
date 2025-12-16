<?php
$student = $student ?? null;
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Удалить студента</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>Удаление студента</h1>
        
        <div class="confirmation-box">
            <p>Вы уверены, что хотите удалить студента:</p>
            <p class="student-info">
                <strong><?= htmlspecialchars($student['last_name']) ?> <?= htmlspecialchars($student['first_name']) ?> <?= htmlspecialchars($student['middle_name'] ?? '') ?></strong>
                <br>
                Группа: <?= htmlspecialchars($student['group_number']) ?>
            </p>
            <p class="warning">Это действие нельзя отменить. Все экзамены студента также будут удалены.</p>
            
            <form method="POST" action="index.php?action=students&subaction=delete&id=<?= $student['id'] ?>">
                <div class="form-actions">
                    <button type="submit" name="confirm" value="1" class="btn btn-danger">Да, удалить</button>
                    <a href="index.php" class="btn btn-secondary">Отмена</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>