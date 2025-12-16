-- SQLite версия
-- Создание таблицы групп
CREATE TABLE IF NOT EXISTS student_groups (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    group_number TEXT NOT NULL UNIQUE,
    direction TEXT NOT NULL,
    graduation_year INTEGER NOT NULL
);

-- Создание таблицы студентов
CREATE TABLE IF NOT EXISTS students (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    last_name TEXT NOT NULL,
    first_name TEXT NOT NULL,
    middle_name TEXT NOT NULL,
    gender TEXT NOT NULL CHECK (gender IN ('M','F')),
    birth_date TEXT NOT NULL,
    student_id TEXT NOT NULL UNIQUE,
    group_id INTEGER NOT NULL,
    FOREIGN KEY (group_id) REFERENCES student_groups(id) ON DELETE CASCADE
);

-- Вставка данных групп
INSERT INTO student_groups (group_number, direction, graduation_year) VALUES
('ИТ-101', 'Информационные технологии', 2025),
('ИТ-102', 'Информационные технологии', 2025),
('ПМ-201', 'Прикладная математика', 2024),
('ФИ-301', 'Физика', 2026);

-- Вставка данных студентов
INSERT INTO students (last_name, first_name, middle_name, gender, birth_date, student_id, group_id) VALUES
('Иванов', 'Иван', 'Иванович', 'M', '2002-05-15', 'ИТ-2021001', (SELECT id FROM student_groups WHERE group_number = 'ИТ-101')),
('Петрова', 'Мария', 'Сергеевна', 'F', '2003-02-20', 'ИТ-2021002', (SELECT id FROM student_groups WHERE group_number = 'ИТ-101')),
('Сидоров', 'Алексей', 'Петрович', 'M', '2002-11-10', 'ИТ-2021003', (SELECT id FROM student_groups WHERE group_number = 'ИТ-102')),
('Кузнецова', 'Елена', 'Владимировна', 'F', '2001-08-25', 'ПМ-2020001', (SELECT id FROM student_groups WHERE group_number = 'ПМ-201')),
('Смирнов', 'Дмитрий', 'Александрович', 'M', '2000-12-05', 'ФИ-2022001', (SELECT id FROM student_groups WHERE group_number = 'ФИ-301'));