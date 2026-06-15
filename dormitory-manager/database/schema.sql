DROP DATABASE IF EXISTS dormitory_manager;

CREATE DATABASE dormitory_manager
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE dormitory_manager;

SET FOREIGN_KEY_CHECKS = 0;

-- =====================================================
-- 1. roles
-- =====================================================

CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 2. users
-- =====================================================

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(150) UNIQUE,
    phone VARCHAR(20),
    role_id INT NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'active',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_users_role
        FOREIGN KEY (role_id) REFERENCES roles(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 3. students
-- =====================================================

CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    student_code VARCHAR(30) NOT NULL UNIQUE,
    full_name VARCHAR(150) NOT NULL,
    gender VARCHAR(20) NOT NULL,
    dob DATE NULL,
    faculty VARCHAR(100),
    program VARCHAR(100),
    priority_type VARCHAR(100),
    address TEXT,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_students_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 4. buildings
-- =====================================================

CREATE TABLE buildings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    building_name VARCHAR(100) NOT NULL UNIQUE,
    total_floors INT NOT NULL,
    manager_id INT NULL,
    description TEXT,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_buildings_manager
        FOREIGN KEY (manager_id) REFERENCES users(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 5. rooms
-- =====================================================

CREATE TABLE rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    building_id INT NOT NULL,
    room_number VARCHAR(30) NOT NULL,
    room_type VARCHAR(50) NOT NULL,
    gender_type VARCHAR(20) NOT NULL,
    capacity INT NOT NULL,
    price_per_month DECIMAL(12,2) NOT NULL,
    status VARCHAR(30) NOT NULL DEFAULT 'available',
    description TEXT,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY uq_rooms_building_room (building_id, room_number),

    CONSTRAINT fk_rooms_building
        FOREIGN KEY (building_id) REFERENCES buildings(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 6. semesters
-- =====================================================

CREATE TABLE semesters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    semester_name VARCHAR(50) NOT NULL UNIQUE,
    academic_year VARCHAR(20) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    registration_open_date DATE NULL,
    registration_close_date DATE NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'open',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 7. services
-- =====================================================

CREATE TABLE services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    service_name VARCHAR(100) NOT NULL UNIQUE,
    unit VARCHAR(30) NOT NULL,
    unit_price DECIMAL(12,2) NOT NULL,
    description TEXT,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 8. room_registrations
-- =====================================================

CREATE TABLE room_registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    semester_id INT NOT NULL,
    desired_building_id INT NULL,
    desired_room_type VARCHAR(50),
    desired_gender_type VARCHAR(20),
    assigned_room_id INT NULL,
    priority_score INT NOT NULL DEFAULT 0,
    note TEXT,
    status VARCHAR(30) NOT NULL DEFAULT 'pending',
    processed_by INT NULL,
    processed_at DATETIME NULL,
    rejection_reason TEXT,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY uq_registration_student_semester (student_id, semester_id),

    CONSTRAINT fk_registrations_student
        FOREIGN KEY (student_id) REFERENCES students(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    CONSTRAINT fk_registrations_semester
        FOREIGN KEY (semester_id) REFERENCES semesters(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    CONSTRAINT fk_registrations_desired_building
        FOREIGN KEY (desired_building_id) REFERENCES buildings(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE,

    CONSTRAINT fk_registrations_assigned_room
        FOREIGN KEY (assigned_room_id) REFERENCES rooms(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE,

    CONSTRAINT fk_registrations_processed_by
        FOREIGN KEY (processed_by) REFERENCES users(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 9. contracts
-- =====================================================

CREATE TABLE contracts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    registration_id INT NULL UNIQUE,
    student_id INT NOT NULL,
    room_id INT NOT NULL,
    semester_id INT NOT NULL,
    contract_code VARCHAR(50) NOT NULL UNIQUE,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    deposit_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    monthly_price DECIMAL(12,2) NOT NULL,
    status VARCHAR(30) NOT NULL DEFAULT 'active',
    created_by INT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    terminated_at DATETIME NULL,
    termination_reason TEXT,

    CONSTRAINT fk_contracts_registration
        FOREIGN KEY (registration_id) REFERENCES room_registrations(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    CONSTRAINT fk_contracts_student
        FOREIGN KEY (student_id) REFERENCES students(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    CONSTRAINT fk_contracts_room
        FOREIGN KEY (room_id) REFERENCES rooms(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    CONSTRAINT fk_contracts_semester
        FOREIGN KEY (semester_id) REFERENCES semesters(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    CONSTRAINT fk_contracts_created_by
        FOREIGN KEY (created_by) REFERENCES users(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 10. utility_readings
-- =====================================================

CREATE TABLE utility_readings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_id INT NOT NULL,
    month_year VARCHAR(7) NOT NULL,
    electric_old INT NOT NULL DEFAULT 0,
    electric_new INT NOT NULL DEFAULT 0,
    water_old INT NOT NULL DEFAULT 0,
    water_new INT NOT NULL DEFAULT 0,
    recorded_by INT NULL,
    recorded_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    note TEXT,

    UNIQUE KEY uq_utility_room_month (room_id, month_year),

    CONSTRAINT fk_utility_room
        FOREIGN KEY (room_id) REFERENCES rooms(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    CONSTRAINT fk_utility_recorded_by
        FOREIGN KEY (recorded_by) REFERENCES users(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 11. invoices
-- =====================================================

CREATE TABLE invoices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    contract_id INT NOT NULL,
    room_id INT NOT NULL,
    student_id INT NOT NULL,
    month_year VARCHAR(7) NOT NULL,
    invoice_code VARCHAR(50) NOT NULL UNIQUE,
    total_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    paid_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    due_date DATE NULL,
    status VARCHAR(30) NOT NULL DEFAULT 'unpaid',
    created_by INT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY uq_invoice_contract_month (contract_id, month_year),

    CONSTRAINT fk_invoices_contract
        FOREIGN KEY (contract_id) REFERENCES contracts(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    CONSTRAINT fk_invoices_room
        FOREIGN KEY (room_id) REFERENCES rooms(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    CONSTRAINT fk_invoices_student
        FOREIGN KEY (student_id) REFERENCES students(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    CONSTRAINT fk_invoices_created_by
        FOREIGN KEY (created_by) REFERENCES users(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 12. invoice_details
-- =====================================================

CREATE TABLE invoice_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invoice_id INT NOT NULL,
    service_id INT NULL,
    description VARCHAR(255) NOT NULL,
    quantity DECIMAL(10,2) NOT NULL DEFAULT 1,
    unit_price DECIMAL(12,2) NOT NULL DEFAULT 0,
    amount DECIMAL(12,2) NOT NULL DEFAULT 0,

    CONSTRAINT fk_invoice_details_invoice
        FOREIGN KEY (invoice_id) REFERENCES invoices(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    CONSTRAINT fk_invoice_details_service
        FOREIGN KEY (service_id) REFERENCES services(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 13. payments
-- =====================================================

CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invoice_id INT NOT NULL,
    student_id INT NOT NULL,
    payment_code VARCHAR(50) NOT NULL UNIQUE,
    amount DECIMAL(12,2) NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    payment_status VARCHAR(30) NOT NULL DEFAULT 'pending',
    paid_at DATETIME NULL,
    transaction_reference VARCHAR(100),
    note TEXT,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_payments_invoice
        FOREIGN KEY (invoice_id) REFERENCES invoices(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    CONSTRAINT fk_payments_student
        FOREIGN KEY (student_id) REFERENCES students(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 14. maintenance_requests
-- =====================================================

CREATE TABLE maintenance_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_id INT NOT NULL,
    student_id INT NOT NULL,
    issue_title VARCHAR(150) NOT NULL,
    issue_description TEXT NOT NULL,
    request_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(30) NOT NULL DEFAULT 'pending',
    assigned_to INT NULL,
    updated_by INT NULL,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    completed_at DATETIME NULL,
    manager_note TEXT,

    CONSTRAINT fk_maintenance_room
        FOREIGN KEY (room_id) REFERENCES rooms(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    CONSTRAINT fk_maintenance_student
        FOREIGN KEY (student_id) REFERENCES students(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    CONSTRAINT fk_maintenance_assigned_to
        FOREIGN KEY (assigned_to) REFERENCES users(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE,

    CONSTRAINT fk_maintenance_updated_by
        FOREIGN KEY (updated_by) REFERENCES users(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 15. violation_records
-- =====================================================

CREATE TABLE violation_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    contract_id INT NULL,
    room_id INT NULL,
    violation_date DATE NOT NULL,
    violation_type VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    penalty_points INT NOT NULL DEFAULT 0,
    recorded_by INT NULL,
    status VARCHAR(30) NOT NULL DEFAULT 'recorded',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    resolved_at DATETIME NULL,
    resolution_note TEXT,

    CONSTRAINT fk_violations_student
        FOREIGN KEY (student_id) REFERENCES students(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    CONSTRAINT fk_violations_contract
        FOREIGN KEY (contract_id) REFERENCES contracts(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE,

    CONSTRAINT fk_violations_room
        FOREIGN KEY (room_id) REFERENCES rooms(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE,

    CONSTRAINT fk_violations_recorded_by
        FOREIGN KEY (recorded_by) REFERENCES users(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 16. audit_logs
-- =====================================================

CREATE TABLE audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    action VARCHAR(50) NOT NULL,
    table_name VARCHAR(100),
    record_id INT,
    old_value TEXT,
    new_value TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_audit_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;