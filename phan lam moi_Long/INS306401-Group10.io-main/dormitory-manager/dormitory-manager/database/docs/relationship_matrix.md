# Relationship Matrix

File này mô tả quan hệ giữa các bảng trong database của hệ thống Dormitory Manager.

Các bảng được liên kết với nhau bằng khóa ngoại để đảm bảo dữ liệu không bị rời rạc. Ví dụ, bảng `users` liên kết với `roles` để xác định vai trò người dùng; bảng `students` liên kết với `users` để gắn hồ sơ sinh viên với tài khoản đăng nhập; bảng `rooms` liên kết với `buildings` để xác định phòng thuộc tòa nhà nào.

Các nghiệp vụ chính cũng được thể hiện thông qua quan hệ dữ liệu. Sinh viên tạo đơn đăng ký trong bảng `room_registrations`; khi đơn được duyệt, hệ thống tạo hợp đồng trong bảng `contracts`; từ hợp đồng, hệ thống sinh hóa đơn trong bảng `invoices`; mỗi hóa đơn có nhiều dòng chi tiết trong `invoice_details` và có thể có nhiều lần thanh toán trong `payments`.

Ngoài ra, hệ thống còn có các quan hệ phục vụ quản lý sự cố, vi phạm và audit log. Bảng `maintenance_requests` lưu yêu cầu sửa chữa theo sinh viên và phòng. Bảng `violation_records` lưu vi phạm theo sinh viên, phòng và hợp đồng. Bảng `audit_logs` lưu lịch sử thao tác của người dùng trong hệ thống.

Trong báo cáo chính, nhóm sẽ trình bày Relationship Matrix chi tiết theo dạng bảng, gồm bảng cha, bảng con, khóa ngoại, kiểu quan hệ và ý nghĩa nghiệp vụ.
