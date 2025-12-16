<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавить студента</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>Добавить нового студента</h1>
        
        <form method="POST" action="index.php?action=students&subaction=create">
            <div class="form-group">
                <label>Фамилия:</label>
                <input type="text" name="last_name" required>
            </div>
            
            <div class="form-group">
                <label>Имя:</label>
                <input type="text" name="first_name" required>
            </div>
            
            <div class="form-group">
                <label>Отчество:</label>
                <input type="text" name="middle_name">
            </div>
            
            <div class="form-group">
                <label>Группа:</label>
                <select name="group_id" required>
                    <option value="">Выберите группу</option>
                    <?php foreach ($groups as $group): ?>
                        <option value="<?= $group['id'] ?>"><?= $group['group_number'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Пол:</label>
                <div>
                    <label>
                        <input type="radio" name="gender" value="M" required> Мужской
                    </label>
                    <label>
                        <input type="radio" name="gender" value="F" required> Женский
                    </label>
                </div>
            </div>
            
            <div class="form-group">
                <label>Дата рождения:</label>
                <input type="date" name="birth_date">
            </div>
            
            <div class="form-group">
                <label>Год поступления:</label>
                <input type="number" name="admission_year" min="2000" max="2030" required>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Сохранить</button>
                <a href="index.php" class="btn btn-secondary">Отмена</a>
            </div>
        </form>
    </div>
</body>
</html>