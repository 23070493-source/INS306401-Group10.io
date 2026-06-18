# Normalization and 3NF Explanation

File này giải thích cách database của hệ thống Dormitory Manager được thiết kế theo hướng chuẩn hóa dữ liệu, đặc biệt là chuẩn 3NF.

Database đạt 1NF vì mỗi cột chỉ lưu một giá trị nguyên tử, không lưu danh sách nhiều giá trị trong một ô. Ví dụ, hệ thống không lưu nhiều dịch vụ trong một cột của bảng hóa đơn, mà tách thành bảng `invoice_details`, trong đó mỗi dòng là một khoản phí riêng.

Database đạt 2NF vì mỗi bảng có khóa chính rõ ràng và các thuộc tính trong bảng đều phụ thuộc vào khóa chính của bảng đó. Ví dụ, các cột như `room_number`, `capacity`, `price_per_month` mô tả trực tiếp cho một phòng trong bảng `rooms`.

Database đạt 3NF vì hệ thống hạn chế phụ thuộc bắc cầu và không lưu dữ liệu lặp không cần thiết. Ví dụ, bảng `buildings` không lưu `manager_name` dạng text mà dùng `manager_id` tham chiếu đến bảng `users`. Bảng `room_registrations` không lưu tên tòa nhà mong muốn dạng text mà dùng `desired_building_id`. Hóa đơn được tách thành `invoices` và `invoice_details` để một hóa đơn có thể có nhiều khoản phí mà không cần tạo nhiều cột cố định.

Một số dữ liệu như `contracts.monthly_price`, `invoice_details.unit_price`, `invoices.student_id`, `invoices.room_id` và `invoices.paid_amount` được giữ lại có chủ đích để đảm bảo tính lịch sử và hỗ trợ truy vấn nhanh. Đây là dữ liệu snapshot phục vụ nghiệp vụ, không phải lỗi thiết kế.

Nhìn chung, database được thiết kế để giảm trùng lặp dữ liệu, đảm bảo tính nhất quán và hỗ trợ đầy đủ các nghiệp vụ chính như đăng ký phòng, xếp phòng, tạo hợp đồng, lập hóa đơn, thanh toán, sửa chữa và theo dõi vi phạm.
