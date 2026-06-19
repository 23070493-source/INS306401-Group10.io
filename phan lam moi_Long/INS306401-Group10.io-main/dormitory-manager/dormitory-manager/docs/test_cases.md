# Dormitory Manager - Defense Test Cases

Use these cases during the final presentation to prove backend logic, validation,
role authorization, database relationships, and stable workflows.

## Demo Accounts

Common password: `password`

| Role | Username | Main demo purpose |
|---|---|---|
| Admin | `admin01` | User, room, service, semester, reports, audit logs, violation overview |
| Manager | `manager_a` | Registration approval, contract, invoices, payments, maintenance, violations |
| Student | `student001` | Pending room registration case |
| Student | `student003` | Clean invoice payment case after running `database/demo_ready.sql` |
| Student | `student007` | Overdue invoice payment case |

## Case 1 - Role-Based Login

1. Open login page.
2. Choose `Admin`.
3. Enter username `student003` and password `password`.
4. Submit.

Expected result: login is rejected because the selected role does not match the account role.

Business rule: users must authenticate with the correct role.

## Case 2 - Student Room Registration

1. Login as `student001`.
2. Open `Đăng ký phòng`.
3. Submit a room registration for an open semester.
4. Open `Đơn của tôi`.

Expected result: the registration is created with `pending` status.

Business rules:
- A student cannot have duplicate active/pending registrations in the same semester.
- A student with an active contract cannot register another room.

## Case 3 - Manager Approves Registration

1. Login as `manager_a`.
2. Open `Đơn đăng ký phòng`.
3. Open the pending registration from `student001`.
4. Use room suggestions and approve with a valid room.

Expected result:
- Registration becomes `approved`.
- A contract is created.
- Room occupancy/status is recalculated.

Business rules:
- Only pending registrations can be approved.
- Selected room must be available and must have remaining capacity.

## Case 4 - Clean Invoice Payment

Before demo, import:

```sql
SOURCE C:/xampp/htdocs/INS306401-Group10.io-main/dormitory-manager/database/demo_ready.sql;
```

1. Login as `student003`.
2. Open `Hóa đơn của tôi`.
3. Find invoice `INV-DEMO-003`.
4. Click `Submit Bank Transfer`.
5. Fill bank, account owner, transaction reference and transfer date.
6. Submit.

Expected result: invoice shows `Waiting Confirmation`; the student cannot submit another payment for the same invoice while one payment is pending.

Business rules:
- Student can only pay their own invoice.
- Paid invoices cannot be paid again.
- An invoice with a pending payment blocks another payment submission.

## Case 5 - Manager Confirms Payment

1. Login as `manager_a`.
2. Open `Thanh toán`.
3. Find the payment for `INV-DEMO-003`.
4. Approve it.
5. Login again as `student003`.
6. Open `Hóa đơn của tôi`.

Expected result:
- Payment status becomes `success`.
- Invoice `paid_amount` increases.
- Invoice status becomes `paid` if the payment covers the remaining balance.

Business rules:
- Only managers can approve/reject payments.
- Only pending payments can be approved or rejected.

## Case 6 - Maintenance Request Flow

1. Login as a student with an active contract, such as `student003`.
2. Open `Yêu cầu sửa chữa`.
3. Create a maintenance request.
4. Login as `manager_a`.
5. Open `Sửa chữa` and update status to `in_progress` or `completed`.

Expected result: maintenance status changes and manager note is saved.

Business rule: student must have an active contract before creating a maintenance request.

## Case 7 - Violation Warning Logic

1. Login as `manager_a`.
2. Open `Vi phạm`.
3. Create a violation record for a student.
4. Open the warning section.
5. Login as the same student and open `Vi phạm của tôi`.

Expected result:
- Penalty points are added.
- Student warning level changes when total points reach thresholds.
- Admin can also view violation overview in `Admin > Vi phạm`.

Business rules:
- Penalty points are assigned by violation type.
- Critical warning students can be considered for contract termination.

## Case 8 - Admin Audit and Reports

1. Login as `admin01`.
2. Open `Nhật ký hệ thống`.
3. Open `Báo cáo`.
4. Open `Vi phạm`.

Expected result: admin can review system activities, operational reports and violation overview.

Business rule: admin has monitoring access but operational approval stays with managers.
