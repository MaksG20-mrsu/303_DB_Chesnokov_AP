CREATE TABLE IF NOT EXISTS employees (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    first_name TEXT NOT NULL,
    last_name TEXT NOT NULL,
    middle_name TEXT,
    phone TEXT UNIQUE NOT NULL,
    email TEXT UNIQUE,
    position TEXT NOT NULL CHECK(position IN ('master', 'manager', 'administrator')),
    hire_date DATE NOT NULL DEFAULT (DATE('now')),
    dismissal_date DATE,
    salary_percentage REAL NOT NULL CHECK(salary_percentage BETWEEN 0 AND 100),
    is_active BOOLEAN NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS clients (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    first_name TEXT NOT NULL,
    last_name TEXT NOT NULL,
    phone TEXT UNIQUE NOT NULL,
    email TEXT,
    car_model TEXT,
    car_plate TEXT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS services (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL UNIQUE,
    description TEXT,
    duration_minutes INTEGER NOT NULL CHECK(duration_minutes > 0),
    price REAL NOT NULL CHECK(price >= 0),
    is_active BOOLEAN NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS appointments (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    client_id INTEGER NOT NULL,
    employee_id INTEGER NOT NULL,
    appointment_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    status TEXT NOT NULL DEFAULT 'scheduled' 
        CHECK(status IN ('scheduled', 'completed', 'cancelled', 'no_show')),
    total_price REAL NOT NULL DEFAULT 0 CHECK(total_price >= 0),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE RESTRICT,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE RESTRICT
);

CREATE TABLE IF NOT EXISTS appointment_services (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    appointment_id INTEGER NOT NULL,
    service_id INTEGER NOT NULL,
    quantity INTEGER NOT NULL DEFAULT 1 CHECK(quantity > 0),
    price_at_time REAL NOT NULL CHECK(price_at_time >= 0),
    
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE RESTRICT,
    UNIQUE(appointment_id, service_id)
);

CREATE TABLE IF NOT EXISTS service_records (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    appointment_id INTEGER NOT NULL,
    employee_id INTEGER NOT NULL,
    service_id INTEGER NOT NULL,
    actual_duration_minutes INTEGER NOT NULL CHECK(actual_duration_minutes > 0),
    actual_price REAL NOT NULL CHECK(actual_price >= 0),
    completion_date DATE NOT NULL DEFAULT (DATE('now')),
    notes TEXT,
    
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE RESTRICT,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE RESTRICT,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE RESTRICT
);

CREATE INDEX IF NOT EXISTS idx_employees_active ON employees(is_active, dismissal_date);
CREATE INDEX IF NOT EXISTS idx_appointments_date ON appointments(appointment_date, status);
CREATE INDEX IF NOT EXISTS idx_appointments_employee ON appointments(employee_id, appointment_date);
CREATE INDEX IF NOT EXISTS idx_service_records_date ON service_records(completion_date);
CREATE INDEX IF NOT EXISTS idx_service_records_employee ON service_records(employee_id, completion_date);
CREATE INDEX IF NOT EXISTS idx_clients_phone ON clients(phone);

CREATE TRIGGER IF NOT EXISTS update_appointment_total_price
AFTER INSERT ON appointment_services
BEGIN
    UPDATE appointments
    SET total_price = (
        SELECT SUM(price_at_time * quantity)
        FROM appointment_services
        WHERE appointment_id = NEW.appointment_id
    )
    WHERE id = NEW.appointment_id;
END;

CREATE TRIGGER IF NOT EXISTS update_employee_status
AFTER UPDATE OF dismissal_date ON employees
WHEN NEW.dismissal_date IS NOT NULL AND OLD.dismissal_date IS NULL
BEGIN
    UPDATE employees
    SET is_active = 0
    WHERE id = NEW.id;
END;

INSERT INTO employees (first_name, last_name, phone, email, position, salary_percentage) VALUES
('Ivan', 'Petrov', '+79161234567', 'ivan.petrov@garage.ru', 'master', 30.0),
('Anna', 'Sidorova', '+79167654321', 'anna.sidorova@garage.ru', 'master', 28.5),
('Sergey', 'Kuznetsov', '+79162345678', 'sergey.kuznetsov@garage.ru', 'master', 32.0),
('Maria', 'Ivanova', '+79168765432', 'maria.ivanova@garage.ru', 'manager', 15.0),
('Alexey', 'Smirnov', '+79163456789', 'alexey.smirnov@garage.ru', 'administrator', 12.0);

UPDATE employees 
SET dismissal_date = DATE('now', '-30 days'), is_active = 0 
WHERE id = 5;

INSERT INTO clients (first_name, last_name, phone, car_model, car_plate) VALUES
('Oleg', 'Volkov', '+79161112233', 'Toyota Camry', 'A123BC77'),
('Elena', 'Zaytseva', '+79162223344', 'Honda Civic', 'B456UK77'),
('Dmitry', 'Medvedev', '+79163334455', 'BMW X5', 'C789MP77'),
('Svetlana', 'Lebedeva', '+79164445566', 'Lada Vesta', 'E012OR77'),
('Artem', 'Sokolov', '+79165556677', 'Kia Rio', 'H345TU77');

INSERT INTO services (name, description, duration_minutes, price) VALUES
('Oil change', 'Complete engine oil and filter replacement', 60, 2500.00),
('Brake pad replacement', 'Front/rear brake pad replacement', 90, 4000.00),
('Wheel alignment', 'Wheel alignment adjustment', 120, 3500.00),
('Engine diagnostics', 'Computer engine diagnostics', 45, 2000.00),
('Battery replacement', 'Car battery replacement', 30, 1500.00),
('Tire service', 'Tire replacement and balancing (4 pcs)', 60, 3000.00),
('Spark plug replacement', 'Spark plug set replacement', 40, 2500.00);

INSERT INTO appointments (client_id, employee_id, appointment_date, start_time, end_time, status) VALUES
(1, 1, DATE('now', '+1 days'), '09:00', '10:00', 'scheduled'),
(2, 2, DATE('now', '+1 days'), '10:30', '12:00', 'scheduled'),
(3, 1, DATE('now', '+2 days'), '14:00', '15:30', 'scheduled'),
(4, 3, DATE('now'), '11:00', '12:00', 'completed'),
(5, 2, DATE('now', '-1 days'), '13:00', '14:30', 'completed');

INSERT INTO appointment_services (appointment_id, service_id, quantity, price_at_time) VALUES
(1, 1, 1, 2500.00),
(1, 7, 1, 2500.00),
(2, 2, 1, 4000.00),
(2, 6, 1, 3000.00),
(3, 3, 1, 3500.00),
(4, 4, 1, 2000.00),
(4, 5, 1, 1500.00),
(5, 1, 1, 2500.00);

INSERT INTO service_records (appointment_id, employee_id, service_id, actual_duration_minutes, actual_price, completion_date) VALUES
(4, 3, 4, 50, 2000.00, DATE('now')),
(4, 3, 5, 35, 1500.00, DATE('now')),
(5, 2, 1, 65, 2500.00, DATE('now', '-1 days'));