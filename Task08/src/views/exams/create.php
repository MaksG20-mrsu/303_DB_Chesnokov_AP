<?php
$students = $students ?? [];
$disciplines = $disciplines ?? [];
$student_id = $_GET['student_id'] ?? '';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавить экзамен</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>Добавить новый экзамен</h1>
        
        <form method="POST" action="index.php?action=exams&subaction=create">
            <div class="form-group">
                <label>Студент:</label>
                <select name="student_id" required>
                    <option value="">Выберите студента</option>
                    <?php foreach ($students as $student): ?>
                        <option value="<?= $student['id'] ?>" 
                            <?= $student_id == $student['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($student['last_name']) ?> <?= htmlspecialchars($student['first_name']) ?> 
                            <?= htmlspecialchars($student['middle_name'] ?? '') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Дисциплина:</label>
                <select name="discipline_id" required>
                    <option value="">Выберите дисциплину</option>
                    <?php foreach ($disciplines as $discipline): ?>
                        <option value="<?= $discipline['id'] ?>">
                            <?= htmlspecialchars($discipline['name']) ?> 
                            (<?= $discipline['study_direction'] ?>, курс <?= $discipline['course'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Дата экзамена:</label>
                <input type="date" name="exam_date" required>
            </div>
            
            <div class="form-group">
                <label>Оценка:</label>
                <select name="grade" required>
                    <option value="">Выберите оценку</option>
                    <option value="5">5 (Отлично)</option>
                    <option value="4">4 (Хорошо)</option>
                    <option value="3">3 (Удовлетворительно)</option>
                    <option value="2">2 (Неудовлетворительно)</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Курс (на момент сдачи):</label>
                <select name="course" required>
                    <option value="">Выберите курс</option>
                    <?php for ($i = 1; $i <= 6; $i++): ?>
                        <option value="<?= $i ?>"><?= $i ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Семестр:</label>
                <select name="semester" required>
                    <option value="">Выберите семестр</option>
                    <?php for ($i = 1; $i <= 12; $i++): ?>
                        <option value="<?= $i ?>"><?= $i ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Сохранить</button>
                <a href="index.php?action=exams&student_id=<?= $student_id ?>" class="btn btn-secondary">Отмена</a>
            </div>
        </form>
    </div>
</body>
</html>