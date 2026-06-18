USE dormitory_manager;

-- =====================================================
-- 1. CHECK TOTAL ROWS IN ALL TABLES
-- =====================================================

SELECT 'roles' AS table_name, COUNT(*) AS total FROM roles
UNION ALL
SELECT 'users', COUNT(*) FROM users
UNION ALL
SELECT 'students', COUNT(*) FROM students
UNION ALL
SELECT 'buildings', COUNT(*) FROM buildings
UNION ALL
SELECT 'rooms', COUNT(*) FROM rooms
UNION ALL
SELECT 'semesters', COUNT(*) FROM semesters
UNION ALL
SELECT 'services', COUNT(*) FROM services
UNION ALL
SELECT 'room_registrations', COUNT(*) FROM room_registrations
UNION ALL
SELECT 'contracts', COUNT(*) FROM contracts
UNION ALL
SELECT 'utility_readings', COUNT(*) FROM utility_readings
UNION ALL
SELECT 'invoices', COUNT(*) FROM invoices
UNION ALL
SELECT 'invoice_details', COUNT(*) FROM invoice_details
UNION ALL
SELECT 'payments', COUNT(*) FROM payments
UNION ALL
SELECT 'maintenance_requests', COUNT(*) FROM maintenance_requests
UNION ALL
SELECT 'violation_records', COUNT(*) FROM violation_records
UNION ALL
SELECT 'audit_logs', COUNT(*) FROM audit_logs;


-- =====================================================
-- 2. USERS WITH ROLES
-- =====================================================

SELECT 
    u.id,
    u.username,
    u.email,
    u.phone,
    r.role_name,
    u.status
FROM users u
JOIN roles r ON r.id = u.role_id
ORDER BY u.id;


-- =====================================================
-- 3. STUDENT PROFILES WITH LOGIN ACCOUNTS
-- =====================================================

SELECT
    s.id,
    s.student_code,
    s.full_name,
    s.gender,
    s.faculty,
    s.program,
    s.priority_type,
    u.username,
    u.email,
    u.status
FROM students s
JOIN users u ON u.id = s.user_id
ORDER BY s.id;


-- =====================================================
-- 4. ROOM LIST WITH BUILDING AND MANAGER
-- =====================================================

SELECT
    r.id AS room_id,
    b.building_name,
    r.room_number,
    r.room_type,
    r.gender_type,
    r.capacity,
    r.price_per_month,
    r.status,
    u.username AS manager_username
FROM rooms r
JOIN buildings b ON b.id = r.building_id
LEFT JOIN users u ON u.id = b.manager_id
ORDER BY b.building_name, r.room_number;


-- =====================================================
-- 5. AVAILABLE ROOMS WITH CURRENT OCCUPANCY
-- =====================================================

SELECT 
    r.id AS room_id,
    b.building_name,
    r.room_number,
    r.room_type,
    r.gender_type,
    r.capacity,
    COUNT(c.id) AS current_occupancy,
    r.capacity - COUNT(c.id) AS available_beds,
    r.price_per_month,
    r.status
FROM rooms r
JOIN buildings b ON b.id = r.building_id
LEFT JOIN contracts c 
    ON c.room_id = r.id 
    AND c.status = 'active'
WHERE r.status = 'available'
GROUP BY 
    r.id,
    b.building_name,
    r.room_number,
    r.room_type,
    r.gender_type,
    r.capacity,
    r.price_per_month,
    r.status
HAVING current_occupancy < r.capacity
ORDER BY b.building_name, r.room_number;


-- =====================================================
-- 6. PENDING ROOM REGISTRATIONS
-- =====================================================

SELECT 
    rr.id AS registration_id,
    s.student_code,
    s.full_name,
    s.gender,
    s.priority_type,
    se.semester_name,
    b.building_name AS desired_building,
    rr.desired_room_type,
    rr.desired_gender_type,
    rr.priority_score,
    rr.status,
    rr.created_at
FROM room_registrations rr
JOIN students s ON s.id = rr.student_id
JOIN semesters se ON se.id = rr.semester_id
LEFT JOIN buildings b ON b.id = rr.desired_building_id
WHERE rr.status = 'pending'
ORDER BY rr.priority_score DESC, rr.created_at ASC;


-- =====================================================
-- 7. ROOM ALLOCATION SUGGESTION FOR STUDENT ID = 1
-- Student 1: male, wants standard room in Building A
-- =====================================================

SELECT 
    r.id AS room_id,
    b.building_name,
    r.room_number,
    r.room_type,
    r.gender_type,
    r.capacity,
    COUNT(c.id) AS current_occupancy,
    r.capacity - COUNT(c.id) AS available_beds,
    r.price_per_month,
    CASE 
        WHEN b.id = rr.desired_building_id THEN 20 ELSE 0
    END +
    CASE 
        WHEN r.room_type = rr.desired_room_type THEN 10 ELSE 0
    END +
    CASE 
        WHEN r.gender_type = s.gender THEN 10 ELSE 0
    END AS match_score
FROM room_registrations rr
JOIN students s ON s.id = rr.student_id
JOIN rooms r ON r.gender_type = s.gender
JOIN buildings b ON b.id = r.building_id
LEFT JOIN contracts c 
    ON c.room_id = r.id 
    AND c.status = 'active'
WHERE rr.student_id = 1
  AND rr.status = 'pending'
  AND r.status = 'available'
GROUP BY 
    r.id,
    b.id,
    b.building_name,
    r.room_number,
    r.room_type,
    r.gender_type,
    r.capacity,
    r.price_per_month,
    rr.desired_building_id,
    rr.desired_room_type,
    s.gender
HAVING current_occupancy < r.capacity
ORDER BY match_score DESC, available_beds DESC, r.price_per_month ASC;


-- =====================================================
-- 8. ACTIVE CONTRACTS
-- =====================================================

SELECT 
    c.id AS contract_id,
    c.contract_code,
    s.student_code,
    s.full_name,
    b.building_name,
    r.room_number,
    c.start_date,
    c.end_date,
    c.deposit_amount,
    c.monthly_price,
    c.status
FROM contracts c
JOIN students s ON s.id = c.student_id
JOIN rooms r ON r.id = c.room_id
JOIN buildings b ON b.id = r.building_id
WHERE c.status = 'active'
ORDER BY c.id;


-- =====================================================
-- 9. ROOM OCCUPANCY REPORT
-- =====================================================

SELECT
    b.building_name,
    r.room_number,
    r.gender_type,
    r.capacity,
    COUNT(c.id) AS current_occupancy,
    r.capacity - COUNT(c.id) AS available_beds,
    r.status
FROM rooms r
JOIN buildings b ON b.id = r.building_id
LEFT JOIN contracts c 
    ON c.room_id = r.id 
    AND c.status = 'active'
GROUP BY
    r.id,
    b.building_name,
    r.room_number,
    r.gender_type,
    r.capacity,
    r.status
ORDER BY b.building_name, r.room_number;


-- =====================================================
-- 10. UTILITY USAGE BY ROOM
-- =====================================================

SELECT
    b.building_name,
    r.room_number,
    ur.month_year,
    ur.electric_old,
    ur.electric_new,
    ur.electric_new - ur.electric_old AS electric_usage,
    ur.water_old,
    ur.water_new,
    ur.water_new - ur.water_old AS water_usage,
    u.username AS recorded_by
FROM utility_readings ur
JOIN rooms r ON r.id = ur.room_id
JOIN buildings b ON b.id = r.building_id
LEFT JOIN users u ON u.id = ur.recorded_by
ORDER BY ur.month_year DESC, b.building_name, r.room_number;


-- =====================================================
-- 11. UNPAID / PARTIALLY PAID / OVERDUE INVOICES
-- =====================================================

SELECT
    i.id AS invoice_id,
    i.invoice_code,
    s.student_code,
    s.full_name,
    b.building_name,
    r.room_number,
    i.month_year,
    i.total_amount,
    i.paid_amount,
    i.total_amount - i.paid_amount AS remaining_amount,
    i.due_date,
    i.status
FROM invoices i
JOIN students s ON s.id = i.student_id
JOIN rooms r ON r.id = i.room_id
JOIN buildings b ON b.id = r.building_id
WHERE i.status IN ('unpaid', 'partially_paid', 'overdue')
ORDER BY i.due_date ASC;


-- =====================================================
-- 12. INVOICE DETAILS FOR ALL INVOICES
-- =====================================================

SELECT 
    i.invoice_code,
    s.student_code,
    s.full_name,
    id.description,
    id.quantity,
    id.unit_price,
    id.amount
FROM invoice_details id
JOIN invoices i ON i.id = id.invoice_id
JOIN students s ON s.id = i.student_id
ORDER BY i.id, id.id;


-- =====================================================
-- 13. CHECK WHETHER INVOICE TOTAL MATCHES DETAILS
-- If difference = 0, invoice is consistent.
-- =====================================================

SELECT
    i.invoice_code,
    i.total_amount AS invoice_total,
    SUM(id.amount) AS detail_total,
    i.total_amount - SUM(id.amount) AS difference
FROM invoices i
JOIN invoice_details id ON id.invoice_id = i.id
GROUP BY i.id, i.invoice_code, i.total_amount
ORDER BY i.id;


-- =====================================================
-- 14. PAYMENT HISTORY
-- =====================================================

SELECT
    p.payment_code,
    s.student_code,
    s.full_name,
    i.invoice_code,
    p.amount,
    p.payment_method,
    p.payment_status,
    p.paid_at,
    p.transaction_reference
FROM payments p
JOIN students s ON s.id = p.student_id
JOIN invoices i ON i.id = p.invoice_id
ORDER BY p.id;


-- =====================================================
-- 15. TOTAL SUCCESSFUL PAYMENT BY STUDENT
-- =====================================================

SELECT
    s.student_code,
    s.full_name,
    COALESCE(SUM(p.amount), 0) AS total_successful_payment
FROM students s
LEFT JOIN payments p 
    ON p.student_id = s.id 
    AND p.payment_status = 'success'
GROUP BY s.id, s.student_code, s.full_name
ORDER BY total_successful_payment DESC;


-- =====================================================
-- 16. OPEN MAINTENANCE REQUESTS
-- =====================================================

SELECT
    mr.id AS request_id,
    s.student_code,
    s.full_name,
    b.building_name,
    r.room_number,
    mr.issue_title,
    mr.status,
    mr.request_date,
    assigned.username AS assigned_to
FROM maintenance_requests mr
JOIN students s ON s.id = mr.student_id
JOIN rooms r ON r.id = mr.room_id
JOIN buildings b ON b.id = r.building_id
LEFT JOIN users assigned ON assigned.id = mr.assigned_to
WHERE mr.status IN ('pending', 'in_progress')
ORDER BY mr.request_date ASC;


-- =====================================================
-- 17. VIOLATION HISTORY
-- =====================================================

SELECT
    vr.id AS violation_id,
    s.student_code,
    s.full_name,
    b.building_name,
    r.room_number,
    vr.violation_date,
    vr.violation_type,
    vr.penalty_points,
    vr.status,
    u.username AS recorded_by
FROM violation_records vr
JOIN students s ON s.id = vr.student_id
LEFT JOIN rooms r ON r.id = vr.room_id
LEFT JOIN buildings b ON b.id = r.building_id
LEFT JOIN users u ON u.id = vr.recorded_by
ORDER BY vr.violation_date DESC;


-- =====================================================
-- 18. STUDENTS WITH HIGH VIOLATION POINTS
-- Threshold: >= 10 points
-- =====================================================

SELECT
    s.student_code,
    s.full_name,
    SUM(vr.penalty_points) AS total_penalty_points,
    COUNT(vr.id) AS violation_count,
    CASE
        WHEN SUM(vr.penalty_points) >= 10 THEN 'Need review for contract termination'
        ELSE 'Normal'
    END AS warning_status
FROM violation_records vr
JOIN students s ON s.id = vr.student_id
GROUP BY s.id, s.student_code, s.full_name
HAVING total_penalty_points >= 10
ORDER BY total_penalty_points DESC;


-- =====================================================
-- 19. AUDIT LOGS
-- =====================================================

SELECT
    al.id,
    u.username,
    al.action,
    al.table_name,
    al.record_id,
    al.old_value,
    al.new_value,
    al.ip_address,
    al.created_at
FROM audit_logs al
LEFT JOIN users u ON u.id = al.user_id
ORDER BY al.created_at DESC, al.id DESC;


-- =====================================================
-- 20. MANAGER DASHBOARD SUMMARY
-- =====================================================

SELECT
    (SELECT COUNT(*) FROM room_registrations WHERE status = 'pending') AS pending_registrations,
    (SELECT COUNT(*) FROM contracts WHERE status = 'active') AS active_contracts,
    (SELECT COUNT(*) FROM invoices WHERE status IN ('unpaid', 'overdue')) AS unpaid_or_overdue_invoices,
    (SELECT COUNT(*) FROM maintenance_requests WHERE status IN ('pending', 'in_progress')) AS open_maintenance_requests,
    (
        SELECT COUNT(*) 
        FROM (
            SELECT student_id
            FROM violation_records
            GROUP BY student_id
            HAVING SUM(penalty_points) >= 10
        ) warning_students
    ) AS violation_warning_students;


-- =====================================================
-- 21. ADMIN DASHBOARD SUMMARY
-- =====================================================

SELECT
    (SELECT COUNT(*) FROM users) AS total_users,
    (SELECT COUNT(*) FROM students) AS total_students,
    (SELECT COUNT(*) FROM buildings) AS total_buildings,
    (SELECT COUNT(*) FROM rooms) AS total_rooms,
    (SELECT COUNT(*) FROM rooms WHERE status = 'available') AS available_rooms,
    (SELECT COUNT(*) FROM rooms WHERE status = 'maintenance') AS maintenance_rooms,
    (SELECT COUNT(*) FROM rooms WHERE status = 'inactive') AS inactive_rooms;