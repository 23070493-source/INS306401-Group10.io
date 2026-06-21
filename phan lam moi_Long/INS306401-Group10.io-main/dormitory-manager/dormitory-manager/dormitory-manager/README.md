# Dormitory Manager

Web quản lý ký túc xá chạy bằng PHP thuần, MySQL/MariaDB và mô hình MVC thủ công. Hệ thống có 3 vai trò: Quản trị viên, Quản lý ký túc xá và Sinh viên.

## Cài đặt trên XAMPP

1. Copy thư mục `dormitory-manager` vào trong `htdocs`, hoặc giữ nguyên project nếu Apache đang trỏ tới thư mục hiện tại.
2. Mở XAMPP Control Panel và bật `Apache` + `MySQL`.
3. Vào phpMyAdmin hoặc dùng terminal MySQL để import:

```sql
SOURCE C:/Users/ADMIN/Desktop/Web2/INS306401-Group10.io-main/dormitory-manager/database/schema.sql;
SOURCE C:/Users/ADMIN/Desktop/Web2/INS306401-Group10.io-main/dormitory-manager/database/seed.sql;
SOURCE C:/Users/ADMIN/Desktop/Web2/INS306401-Group10.io-main/dormitory-manager/database/demo_ready.sql;
```

4. Mở trình duyệt tới:

```text
http://localhost/INS306401-Group10.io-main/dormitory-manager/public/
```

Nếu bạn đặt project ở đường dẫn khác trong `htdocs`, chỉ cần đổi phần thư mục trước `/dormitory-manager/public/`. File `config/config.php` đã tự nhận `BASE_URL` theo thư mục public hiện tại.

## Tài khoản demo

Mật khẩu chung cho tài khoản seed: `password`

| Vai trò | Username |
|---|---|
| Quản trị viên | `admin01` |
| Quản lý | `manager_a` |
| Quản lý | `manager_b` |
| Sinh viên | `student001` đến `student008` |

Ở màn đăng nhập phải chọn đúng vai trò. Nếu chọn sai vai trò, hệ thống sẽ báo tài khoản không tồn tại hoặc không khớp vai trò.

## Kịch bản demo đề xuất

1. Đăng nhập `admin01` để xem dashboard, quản lý tài khoản, sinh viên, tòa nhà, phòng, học kỳ, dịch vụ và báo cáo.
2. Đăng nhập `student001`, vào `Đăng ký phòng`, gửi đơn đăng ký phòng.
3. Đăng nhập `manager_a`, vào `Đơn đăng ký phòng`, xem chi tiết, tải lại gợi ý phòng bằng AJAX, duyệt đơn và tạo hợp đồng.
4. Manager vào `Tạo hóa đơn` hoặc `Chỉ số điện nước` để lập hóa đơn.
5. Sinh viên vào `Hóa đơn của tôi`, gửi thông tin chuyển khoản.
6. Manager vào `Thanh toán`, duyệt giao dịch để cập nhật trạng thái hóa đơn.
7. Sinh viên gửi `Yêu cầu sửa chữa`; Manager cập nhật tiến độ.
8. Manager ghi nhận `Biên bản vi phạm`; dashboard và báo cáo hiển thị cảnh báo điểm vi phạm.

## Kiểm thử database

Sau khi import schema và seed, có thể chạy:

```sql
SOURCE C:/Users/ADMIN/Desktop/Web2/INS306401-Group10.io-main/dormitory-manager/database/test_queries.sql;
```

File này kiểm tra số dòng 16 bảng, phòng còn trống, đơn pending, hợp đồng active, hóa đơn chưa thanh toán, lịch sử thanh toán, yêu cầu sửa chữa, vi phạm và dashboard summary.

## Tài liệu bảo vệ

- `docs/test_cases.md`: kịch bản test và demo theo đúng rubric.
- `docs/team_module_matrix.md`: chia module theo thành viên, bảng phụ trách, CRUD và business rules.
- `database/demo_ready.sql`: tạo case invoice sạch `INV-DEMO-003` cho `student003` để demo `Submit Bank Transfer` từ đầu.
