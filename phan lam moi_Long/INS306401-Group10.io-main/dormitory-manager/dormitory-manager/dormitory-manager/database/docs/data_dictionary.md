# Data Dictionary

File này mô tả cấu trúc dữ liệu của hệ thống Dormitory Manager.

Database gồm 16 bảng chính: `roles`, `users`, `students`, `buildings`, `rooms`, `semesters`, `services`, `room_registrations`, `contracts`, `utility_readings`, `invoices`, `invoice_details`, `payments`, `maintenance_requests`, `violation_records`, và `audit_logs`.

Mỗi bảng trong hệ thống đại diện cho một thực thể hoặc một nhóm nghiệp vụ cụ thể. Ví dụ, `users` dùng để lưu tài khoản đăng nhập, `students` dùng để lưu hồ sơ sinh viên, `rooms` dùng để lưu thông tin phòng, `room_registrations` dùng để lưu đơn đăng ký phòng, `contracts` dùng để lưu hợp đồng, còn `invoices` và `payments` dùng để quản lý hóa đơn và thanh toán.

Trong báo cáo chính, nhóm sẽ trình bày chi tiết Data Dictionary theo các thông tin: tên bảng, mục đích bảng, tên cột, kiểu dữ liệu, khóa chính, khóa ngoại, ràng buộc dữ liệu và mô tả nghiệp vụ của từng cột.
