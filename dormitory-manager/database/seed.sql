USE dormitory_manager;

SET FOREIGN_KEY_CHECKS = 0;

DELETE FROM audit_logs;
DELETE FROM violation_records;
DELETE FROM maintenance_requests;
DELETE FROM payments;
DELETE FROM invoice_details;
DELETE FROM invoices;
DELETE FROM utility_readings;
DELETE FROM contracts;
DELETE FROM room_registrations;
DELETE FROM services;
DELETE FROM semesters;
DELETE FROM rooms;
DELETE FROM buildings;
DELETE FROM students;
DELETE FROM users;
DELETE FROM roles;

ALTER TABLE audit_logs AUTO_INCREMENT = 1;
ALTER TABLE violation_records AUTO_INCREMENT = 1;
ALTER TABLE maintenance_requests AUTO_INCREMENT = 1;
ALTER TABLE payments AUTO_INCREMENT = 1;
ALTER TABLE invoice_details AUTO_INCREMENT = 1;
ALTER TABLE invoices AUTO_INCREMENT = 1;
ALTER TABLE utility_readings AUTO_INCREMENT = 1;
ALTER TABLE contracts AUTO_INCREMENT = 1;
ALTER TABLE room_registrations AUTO_INCREMENT = 1;
ALTER TABLE services AUTO_INCREMENT = 1;
ALTER TABLE semesters AUTO_INCREMENT = 1;
ALTER TABLE rooms AUTO_INCREMENT = 1;
ALTER TABLE buildings AUTO_INCREMENT = 1;
ALTER TABLE students AUTO_INCREMENT = 1;
ALTER TABLE users AUTO_INCREMENT = 1;
ALTER TABLE roles AUTO_INCREMENT = 1;

SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================
-- 1. roles
-- =====================================================

INSERT INTO roles (id, role_name, description) VALUES
(1, 'Admin', 'System administrator who manages accounts and master data'),
(2, 'Manager', 'Dormitory manager who handles registrations, contracts, billing and reports'),
(3, 'Student', 'Student user who registers rooms, views contracts and pays invoices');

-- =====================================================
-- 2. users
-- Demo password for all users: password
-- =====================================================

INSERT INTO users (id, username, password_hash, email, phone, role_id, status) VALUES
(1, 'admin01', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin01@school.edu.vn', '0900000001', 1, 'active'),
(2, 'manager_a', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'manager.a@school.edu.vn', '0900000002', 2, 'active'),
(3, 'manager_b', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'manager.b@school.edu.vn', '0900000003', 2, 'active'),

(4, 'student001', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student001@school.edu.vn', '0910000001', 3, 'active'),
(5, 'student002', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student002@school.edu.vn', '0910000002', 3, 'active'),
(6, 'student003', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student003@school.edu.vn', '0910000003', 3, 'active'),
(7, 'student004', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student004@school.edu.vn', '0910000004', 3, 'active'),
(8, 'student005', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student005@school.edu.vn', '0910000005', 3, 'active'),
(9, 'student006', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student006@school.edu.vn', '0910000006', 3, 'active'),
(10, 'student007', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student007@school.edu.vn', '0910000007', 3, 'active'),
(11, 'student008', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student008@school.edu.vn', '0910000008', 3, 'active');

-- =====================================================
-- 3. students
-- =====================================================

INSERT INTO students (id, user_id, student_code, full_name, gender, dob, faculty, program, priority_type, address) VALUES
(1, 4, 'SV001', 'Nguyen Minh An', 'male', '2005-03-12', 'Information Technology', 'Software Engineering', 'freshman', 'Ha Noi'),
(2, 5, 'SV002', 'Tran Gia Binh', 'male', '2004-07-21', 'Information Technology', 'Data Science', 'none', 'Bac Ninh'),
(3, 6, 'SV003', 'Le Ngoc Chi', 'female', '2005-01-09', 'International Business', 'Business Administration', 'international', 'Da Nang'),
(4, 7, 'SV004', 'Pham Thuy Dung', 'female', '2004-11-02', 'Information Technology', 'Cyber Security', 'policy', 'Thanh Hoa'),
(5, 8, 'SV005', 'Hoang Minh Em', 'male', '2005-05-18', 'Multimedia', 'Web Development', 'none', 'Hai Phong'),
(6, 9, 'SV006', 'Vu Huong Giang', 'female', '2005-09-30', 'Information Technology', 'Software Engineering', 'scholarship', 'Nam Dinh'),
(7, 10, 'SV007', 'Do Quang Huy', 'male', '2004-12-14', 'Information Technology', 'Artificial Intelligence', 'policy', 'Nghe An'),
(8, 11, 'SV008', 'Bui Khanh Linh', 'female', '2005-06-25', 'International Business', 'Marketing', 'none', 'Ha Noi');

-- =====================================================
-- 4. buildings
-- =====================================================

INSERT INTO buildings (id, building_name, total_floors, manager_id, description) VALUES
(1, 'Building A', 5, 2, 'Main dormitory building for standard rooms'),
(2, 'Building B', 6, 3, 'Dormitory building with mixed standard and premium rooms'),
(3, 'Building C', 4, 2, 'New dormitory building for international and priority students');

-- =====================================================
-- 5. rooms
-- =====================================================

INSERT INTO rooms (id, building_id, room_number, room_type, gender_type, capacity, price_per_month, status, description) VALUES
(1, 1, 'A101', 'standard', 'male', 4, 1200000.00, 'available', 'Standard male room'),
(2, 1, 'A102', 'standard', 'male', 4, 1200000.00, 'available', 'Standard male room'),
(3, 1, 'A201', 'premium', 'female', 2, 1800000.00, 'available', 'Premium female room'),
(4, 1, 'A202', 'standard', 'female', 4, 1200000.00, 'available', 'Standard female room'),
(5, 1, 'A301', 'standard', 'male', 4, 1200000.00, 'maintenance', 'Room under maintenance'),

(6, 2, 'B101', 'standard', 'female', 4, 1250000.00, 'available', 'Standard female room'),
(7, 2, 'B102', 'premium', 'male', 2, 1850000.00, 'available', 'Premium male room'),
(8, 2, 'B201', 'standard', 'male', 4, 1250000.00, 'full', 'Currently full'),
(9, 2, 'B202', 'standard', 'female', 4, 1250000.00, 'available', 'Standard female room'),
(10, 2, 'B301', 'premium', 'female', 2, 1850000.00, 'maintenance', 'Room under maintenance'),

(11, 3, 'C101', 'standard', 'male', 6, 1100000.00, 'available', 'Large standard male room'),
(12, 3, 'C102', 'standard', 'female', 6, 1100000.00, 'available', 'Large standard female room'),
(13, 3, 'C201', 'premium', 'male', 2, 2000000.00, 'available', 'Premium male room'),
(14, 3, 'C202', 'premium', 'female', 2, 2000000.00, 'available', 'Premium female room'),
(15, 3, 'C301', 'standard', 'male', 4, 1100000.00, 'inactive', 'Inactive room'),
(16, 3, 'C302', 'standard', 'female', 4, 1100000.00, 'available', 'Standard female room');

-- =====================================================
-- 6. semesters
-- =====================================================

INSERT INTO semesters (id, semester_name, academic_year, start_date, end_date, registration_open_date, registration_close_date, status) VALUES
(1, 'Spring 2026', '2025-2026', '2026-01-15', '2026-06-30', '2025-12-01', '2026-01-10', 'open'),
(2, 'Fall 2026', '2026-2027', '2026-08-15', '2026-12-30', '2026-07-01', '2026-08-10', 'closed');

-- =====================================================
-- 7. services
-- =====================================================

INSERT INTO services (id, service_name, unit, unit_price, description, is_active) VALUES
(1, 'electricity', 'kWh', 3500.00, 'Electricity usage fee', TRUE),
(2, 'water', 'm3', 10000.00, 'Water usage fee', TRUE),
(3, 'internet', 'month', 50000.00, 'Monthly internet fee', TRUE),
(4, 'cleaning', 'month', 30000.00, 'Monthly cleaning fee', TRUE),
(5, 'air_conditioner', 'month', 100000.00, 'Air conditioner service fee', TRUE);

-- =====================================================
-- 8. room_registrations
-- =====================================================

INSERT INTO room_registrations (
    id, student_id, semester_id, desired_building_id, desired_room_type,
    desired_gender_type, assigned_room_id, priority_score, note, status,
    processed_by, processed_at, rejection_reason
) VALUES
(1, 1, 1, 1, 'standard', 'male', NULL, 20, 'Freshman student wants Building A', 'pending', NULL, NULL, NULL),
(2, 2, 1, 1, 'standard', 'male', 1, 5, 'No special request', 'approved', 2, '2026-01-03 09:30:00', NULL),
(3, 3, 1, 2, 'standard', 'female', 6, 30, 'International student', 'approved', 3, '2026-01-03 10:00:00', NULL),
(4, 4, 1, 1, 'premium', 'female', 3, 40, 'Policy priority student', 'approved', 2, '2026-01-04 08:45:00', NULL),
(5, 5, 1, 3, 'premium', 'male', NULL, 5, 'Wanted premium room only', 'rejected', 3, '2026-01-04 11:20:00', 'No suitable premium male room available at requested time'),
(6, 6, 1, 2, 'standard', 'female', NULL, 25, 'Scholarship student', 'pending', NULL, NULL, NULL),
(7, 7, 1, 3, 'standard', 'male', 11, 35, 'Policy priority student', 'approved', 2, '2026-01-05 14:10:00', NULL),
(8, 8, 1, 3, 'premium', 'female', 14, 5, 'Prefers quiet premium room', 'approved', 3, '2026-01-05 15:00:00', NULL);

-- =====================================================
-- 9. contracts
-- =====================================================

INSERT INTO contracts (
    id, registration_id, student_id, room_id, semester_id, contract_code,
    start_date, end_date, deposit_amount, monthly_price, status, created_by
) VALUES
(1, 2, 2, 1, 1, 'CT-2026-001', '2026-01-15', '2026-06-30', 500000.00, 1200000.00, 'active', 2),
(2, 3, 3, 6, 1, 'CT-2026-002', '2026-01-15', '2026-06-30', 500000.00, 1250000.00, 'active', 3),
(3, 4, 4, 3, 1, 'CT-2026-003', '2026-01-15', '2026-06-30', 700000.00, 1800000.00, 'active', 2),
(4, 7, 7, 11, 1, 'CT-2026-004', '2026-01-15', '2026-06-30', 500000.00, 1100000.00, 'active', 2),
(5, 8, 8, 14, 1, 'CT-2026-005', '2026-01-15', '2026-06-30', 700000.00, 2000000.00, 'active', 3);

-- =====================================================
-- 10. utility_readings
-- =====================================================

INSERT INTO utility_readings (
    id, room_id, month_year, electric_old, electric_new,
    water_old, water_new, recorded_by, note
) VALUES
(1, 1, '2026-06', 1200, 1245, 300, 306, 2, 'Normal usage'),
(2, 6, '2026-06', 980, 1022, 260, 267, 3, 'Normal usage'),
(3, 3, '2026-06', 1500, 1530, 400, 405, 2, 'Premium room usage'),
(4, 11, '2026-06', 800, 860, 200, 210, 2, 'Large room usage'),
(5, 14, '2026-06', 1700, 1740, 450, 456, 3, 'Premium room usage');

-- =====================================================
-- 11. invoices
-- =====================================================

INSERT INTO invoices (
    id, contract_id, room_id, student_id, month_year, invoice_code,
    total_amount, paid_amount, due_date, status, created_by
) VALUES
(1, 1, 1, 2, '2026-06', 'INV-2026-001', 1467500.00, 0.00, '2026-06-15', 'unpaid', 2),
(2, 2, 6, 3, '2026-06', 'INV-2026-002', 1517000.00, 1517000.00, '2026-06-15', 'paid', 3),
(3, 3, 3, 4, '2026-06', 'INV-2026-003', 2055000.00, 500000.00, '2026-06-15', 'partially_paid', 2),
(4, 4, 11, 7, '2026-06', 'INV-2026-004', 1450000.00, 0.00, '2026-06-01', 'overdue', 2),
(5, 5, 14, 8, '2026-06', 'INV-2026-005', 2300000.00, 2300000.00, '2026-06-15', 'paid', 3);

-- =====================================================
-- 12. invoice_details
-- =====================================================

INSERT INTO invoice_details (
    id, invoice_id, service_id, description, quantity, unit_price, amount
) VALUES
(1, 1, NULL, 'Room monthly fee', 1, 1200000.00, 1200000.00),
(2, 1, 1, 'Electricity fee', 45, 3500.00, 157500.00),
(3, 1, 2, 'Water fee', 6, 10000.00, 60000.00),
(4, 1, 3, 'Internet fee', 1, 50000.00, 50000.00),

(5, 2, NULL, 'Room monthly fee', 1, 1250000.00, 1250000.00),
(6, 2, 1, 'Electricity fee', 42, 3500.00, 147000.00),
(7, 2, 2, 'Water fee', 7, 10000.00, 70000.00),
(8, 2, 3, 'Internet fee', 1, 50000.00, 50000.00),

(9, 3, NULL, 'Room monthly fee', 1, 1800000.00, 1800000.00),
(10, 3, 1, 'Electricity fee', 30, 3500.00, 105000.00),
(11, 3, 2, 'Water fee', 5, 10000.00, 50000.00),
(12, 3, 5, 'Air conditioner fee', 1, 100000.00, 100000.00),

(13, 4, NULL, 'Room monthly fee', 1, 1100000.00, 1100000.00),
(14, 4, 1, 'Electricity fee', 60, 3500.00, 210000.00),
(15, 4, 2, 'Water fee', 10, 10000.00, 100000.00),
(16, 4, 3, 'Internet fee', 1, 50000.00, 50000.00),
(17, 4, 4, 'Cleaning fee', 1, 30000.00, 30000.00),

(18, 5, NULL, 'Room monthly fee', 1, 2000000.00, 2000000.00),
(19, 5, 1, 'Electricity fee', 40, 3500.00, 140000.00),
(20, 5, 2, 'Water fee', 6, 10000.00, 60000.00),
(21, 5, 5, 'Air conditioner fee', 1, 100000.00, 100000.00);

-- =====================================================
-- 13. payments
-- =====================================================

INSERT INTO payments (
    id, invoice_id, student_id, payment_code, amount,
    payment_method, payment_status, paid_at, transaction_reference, note
) VALUES
(1, 2, 3, 'PAY-2026-001', 1517000.00, 'bank_transfer', 'success', '2026-06-10 09:20:00', 'BANK-TXN-001', 'Full payment received'),
(2, 3, 4, 'PAY-2026-002', 500000.00, 'cash', 'success', '2026-06-11 14:30:00', 'CASH-002', 'Partial payment'),
(3, 5, 8, 'PAY-2026-003', 2300000.00, 'online', 'success', '2026-06-09 20:15:00', 'ONLINE-003', 'Full online payment'),
(4, 1, 2, 'PAY-2026-004', 300000.00, 'online', 'pending', NULL, 'ONLINE-PENDING-004', 'Pending payment not counted');

-- =====================================================
-- 14. maintenance_requests
-- =====================================================

INSERT INTO maintenance_requests (
    id, room_id, student_id, issue_title, issue_description,
    request_date, status, assigned_to, updated_by, updated_at,
    completed_at, manager_note
) VALUES
(1, 1, 2, 'Broken light', 'The ceiling light near the door is not working.', '2026-06-05 08:30:00', 'pending', NULL, NULL, NULL, NULL, NULL),
(2, 6, 3, 'Water leakage', 'There is a small water leakage near the bathroom sink.', '2026-06-03 10:15:00', 'in_progress', 3, 3, '2026-06-04 09:00:00', NULL, 'Technician assigned'),
(3, 3, 4, 'Air conditioner noise', 'The air conditioner makes loud noise at night.', '2026-05-28 21:00:00', 'completed', 2, 2, '2026-05-30 11:00:00', '2026-05-30 11:00:00', 'AC fan cleaned'),
(4, 11, 7, 'Door lock issue', 'Room door lock is difficult to open.', '2026-06-07 07:45:00', 'pending', NULL, NULL, NULL, NULL, NULL);

-- =====================================================
-- 15. violation_records
-- =====================================================

INSERT INTO violation_records (
    id, student_id, contract_id, room_id, violation_date,
    violation_type, description, penalty_points, recorded_by,
    status, resolved_at, resolution_note
) VALUES
(1, 2, 1, 1, '2026-04-10', 'noise', 'Made loud noise after quiet hours.', 3, 2, 'resolved', '2026-04-11 10:00:00', 'Student was warned'),
(2, 2, 1, 1, '2026-05-15', 'damaged_property', 'Damaged study desk in the room.', 8, 2, 'reviewed', NULL, 'Repair fee under review'),
(3, 4, 3, 3, '2026-05-20', 'hygiene', 'Room hygiene did not meet dormitory standard.', 2, 3, 'resolved', '2026-05-21 09:00:00', 'Room cleaned after warning'),
(4, 7, 4, 11, '2026-06-02', 'unauthorized_guest', 'Had an unauthorized guest after visiting hours.', 5, 2, 'recorded', NULL, NULL);

-- =====================================================
-- 16. audit_logs
-- =====================================================

INSERT INTO audit_logs (
    id, user_id, action, table_name, record_id,
    old_value, new_value, ip_address, user_agent
) VALUES
(1, 1, 'create', 'users', 4, NULL, 'Created student001 account', '127.0.0.1', 'Seed Script'),
(2, 2, 'approve', 'room_registrations', 2, 'pending', 'approved', '127.0.0.1', 'Seed Script'),
(3, 3, 'approve', 'room_registrations', 3, 'pending', 'approved', '127.0.0.1', 'Seed Script'),
(4, 2, 'create', 'contracts', 1, NULL, 'Created contract CT-2026-001', '127.0.0.1', 'Seed Script'),
(5, 2, 'generate_invoice', 'invoices', 1, NULL, 'Generated invoice INV-2026-001', '127.0.0.1', 'Seed Script'),
(6, 3, 'pay', 'payments', 1, NULL, 'Payment PAY-2026-001 success', '127.0.0.1', 'Seed Script'),
(7, 2, 'create', 'maintenance_requests', 1, NULL, 'Created maintenance request', '127.0.0.1', 'Seed Script'),
(8, 2, 'create', 'violation_records', 1, NULL, 'Recorded violation', '127.0.0.1', 'Seed Script');