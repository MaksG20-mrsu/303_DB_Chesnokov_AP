-- Группы
CREATE TABLE groups (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    group_number VARCHAR(10) NOT NULL UNIQUE,
    study_direction VARCHAR(100) NOT NULL
);

INSERT INTO groups (group_number, study_direction) VALUES
('ПИ-101', 'Программная инженерия'),
('ПИ-102', 'Программная инженерия'),
('ИВТ-201', 'Информатика и вычислительная техника'),
('ИВТ-202', 'Информатика и вычислительная техника'),
('ФИ-301', 'Фундаментальная информатика');

-- Студенты
CREATE TABLE students (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    last_name VARCHAR(50) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    middle_name VARCHAR(50),
    group_id INTEGER NOT NULL,
    gender VARCHAR(1) NOT NULL CHECK (gender IN ('M', 'F')),
    birth_date DATE,
    admission_year INTEGER NOT NULL,
    FOREIGN KEY (group_id) REFERENCES groups(id)
);

-- Дисциплины
CREATE TABLE disciplines (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE,
    study_direction VARCHAR(100) NOT NULL,
    course INTEGER NOT NULL CHECK (course BETWEEN 1 AND 6)
);

INSERT INTO disciplines (name, study_direction, course) VALUES
('Математический анализ', 'Программная инженерия', 1),
('Программирование', 'Программная инженерия', 1),
('Базы данных', 'Программная инженерия', 2),
('Web-разработка', 'Программная инженерия', 3),
('Теория алгоритмов', 'Информатика и вычислительная техника', 2),
('Операционные системы', 'Информатика и вычислительная техника', 3),
('Дискретная математика', 'Фундаментальная информатика', 1);

-- Экзамены
CREATE TABLE exams (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    student_id INTEGER NOT NULL,
    discipline_id INTEGER NOT NULL,
    exam_date DATE NOT NULL,
    grade INTEGER NOT NULL CHECK (grade BETWEEN 2 AND 5),
    course INTEGER NOT NULL CHECK (course BETWEEN 1 AND 6),
    semester INTEGER NOT NULL CHECK (semester BETWEEN 1 AND 12),
    FOREIGN KEY (student_id) REFERENCES students(id),
    FOREIGN KEY (discipline_id) REFERENCES disciplines(id)
);