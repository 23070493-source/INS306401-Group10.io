# Team Module Matrix

This file maps each member to a defensible backend module. It is designed to
match the final exam rubric requirement that each student owns at least three
related database tables and can explain CRUD, validation and business logic.

## Member 1 - System Administration Module

Related tables:
- `roles`
- `users`
- `students`
- `audit_logs`

Main screens:
- Admin dashboard
- User management
- Student profile management
- Audit logs

CRUD and logic:
- Create/read/update user accounts.
- Toggle account status instead of hard deleting important login records.
- Create/read/update student profiles.
- Validate duplicate username, email and student code.
- Record important changes in `audit_logs`.

Defense points:
- Explain role-based login and authorization.
- Explain why `users` links to `roles`.
- Explain why `students` links to `users` with one student profile per student account.

## Member 2 - Room Registration and Contract Module

Related tables:
- `buildings`
- `rooms`
- `semesters`
- `room_registrations`
- `contracts`

Main screens:
- Admin building, room and semester management
- Student room registration
- Manager registration approval
- Manager contract management

CRUD and logic:
- Create/read/update buildings, rooms and semesters.
- Student creates room registration.
- Manager approves or rejects pending registrations.
- Approval creates a contract and recalculates room occupancy.
- Contract checkout changes contract status.

Business rules:
- Student cannot register twice in the same semester while a registration is pending/approved.
- Student with an active contract cannot register a new room.
- Manager can only approve pending registrations.
- Selected room must be available and must have remaining beds.

Defense points:
- Explain the flow from `room_registrations` to `contracts`.
- Explain room capacity checking.
- Explain status transitions: `pending -> approved/rejected`, `active -> terminated/expired`.

## Member 3 - Billing, Maintenance and Violation Module

Related tables:
- `invoices`
- `invoice_details`
- `payments`
- `maintenance_requests`
- `violation_records`

Main screens:
- Manager invoice creation
- Student invoices and bank transfer submission
- Manager payment verification
- Student maintenance request
- Manager maintenance processing
- Manager/Admin/Student violation views

CRUD and logic:
- Manager creates invoices and invoice details.
- Student creates payment submissions.
- Manager approves/rejects pending payments.
- Student creates maintenance requests.
- Manager updates maintenance status.
- Manager creates violation records.
- Admin reviews violation overview.

Business rules:
- Student can only submit payment for their own invoice.
- Paid invoices cannot be paid again.
- An invoice with a pending payment blocks another payment submission.
- Payment approval updates invoice paid amount and status.
- Maintenance request requires an active contract.
- Violation points contribute to warning and critical warning levels.

Defense points:
- Explain invoice total vs paid amount vs pending amount.
- Explain payment status transition: `pending -> success/rejected`.
- Explain violation warning thresholds.
