<?php
$exam = $exam ?? null;
$disciplines = $disciplines ?? [];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактировать экзамен</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>Редактировать экзамен</h1>
        <p>Студент: <?= htmlspecialchars($exam['last_name']) ?> <?= htmlspecialchars($exam['first_name']) ?></p>
        
        <form method="POST" action="index.php?action=exams&subaction=update&id=<?= $exam['id'] ?>">
            <div class="form-group">
                <label>Дисциплина:</label>
                <select name="discipline_id" required>
                    <option value="">Выберите дисциплину</option>
                    <?php foreach ($disciplines as $discipline): ?>
                        <option value="<?= $discipline['id'] ?>" 
                            <?= $exam['discipline_id'] == $discipline['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($discipline['name']) ?> 
                            (<?= $discipline['study_direction'] ?>, курс <?= $discipline['course'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Дата экзамена:</label>
                <input type="date" name="exam_date" value="<?= htmlspecialchars($exam['exam_date']) ?>" required>
            </div>
            
            <div class="form-group">
                <label>Оценка:</label>
                <select name="grade" required>
                    <option value="5" <?= $exam['grade'] == 5 ? 'selected' : '' ?>>5 (Отлично)</option>
                    <option value="4" <?= $exam['grade'] == 4 ? 'selected' : '' ?>>4 (Хорошо)</option>
                    <option value="3" <?= $exam['grade'] == 3 ? 'selected' : '' ?>>3 (Удовлетворительно)</option>
                    <option value="2" <?= $exam['grade'] == 2 ? 'selected' : '' ?>>2 (Неудовлетворительно)</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Курс (на момент сдачи):</label>
                <select name="course" required>
                    <?php for ($i = 1; $i <= 6; $i++): ?>
                        <option value="<?= $i ?>" <?= $exam['course'] == $i ? 'selected' : '' ?>><?= $i ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Семестр:</label>
                <select name="semester" required>
                    <?php for ($i = 1; $i <= 12; $i++): ?>
                        <option value="<?= $i ?>" <?= $exam['semester'] == $i ? 'selected' : '' ?>><?= $i ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Сохранить изменения</button>
                <a href="index.php?action=exams&student_id=<?= $exam['student_id'] ?>" class="btn btn-secondary">Отмена</a>
            </div>
        </form>
    </div>
</body>
</html>