const DEFAULT_LANG = 'vi';

const VALUE_LABELS = {
    active: { vi: 'Đang hoạt động', en: 'Active' },
    inactive: { vi: 'Ngưng hoạt động', en: 'Inactive' },
    available: { vi: 'Còn trống', en: 'Available' },
    full: { vi: 'Đã đầy', en: 'Full' },
    maintenance: { vi: 'Bảo trì', en: 'Maintenance' },
    open: { vi: 'Đang mở', en: 'Open' },
    closed: { vi: 'Đã đóng', en: 'Closed' },
    upcoming: { vi: 'Sắp mở', en: 'Upcoming' },
    pending: { vi: 'Chờ xử lý', en: 'Pending' },
    approved: { vi: 'Đã duyệt', en: 'Approved' },
    rejected: { vi: 'Từ chối', en: 'Rejected' },
    cancelled: { vi: 'Đã hủy', en: 'Cancelled' },
    canceled: { vi: 'Đã hủy', en: 'Cancelled' },
    success: { vi: 'Thành công', en: 'Success' },
    paid: { vi: 'Đã thanh toán', en: 'Paid' },
    unpaid: { vi: 'Chưa thanh toán', en: 'Unpaid' },
    partially_paid: { vi: 'Thanh toán một phần', en: 'Partially paid' },
    overdue: { vi: 'Quá hạn', en: 'Overdue' },
    recorded: { vi: 'Đã ghi nhận', en: 'Recorded' },
    reviewed: { vi: 'Đã xem xét', en: 'Reviewed' },
    resolved: { vi: 'Đã xử lý', en: 'Resolved' },
    completed: { vi: 'Hoàn tất', en: 'Completed' },
    in_progress: { vi: 'Đang xử lý', en: 'In progress' },
    invoiced: { vi: 'Đã lập hóa đơn', en: 'Invoiced' },
    terminated: { vi: 'Đã chấm dứt', en: 'Terminated' },
    expired: { vi: 'Hết hạn', en: 'Expired' },
    standard: { vi: 'Tiêu chuẩn', en: 'Standard' },
    premium: { vi: 'Cao cấp', en: 'Premium' },
    single: { vi: 'Phòng đơn', en: 'single' },
    double: { vi: 'Phòng đôi', en: 'double' },
    quad: { vi: 'Phòng 4 người', en: 'quad' },
    six: { vi: 'Phòng 6 người', en: 'six' },
    eight: { vi: 'Phòng 8 người', en: 'eight' },
    male: { vi: 'Nam', en: 'Male' },
    female: { vi: 'Nữ', en: 'Female' },
    mixed: { vi: 'Linh hoạt', en: 'Mixed' },
    other: { vi: 'Khác', en: 'Other' },
    low: { vi: 'Thấp', en: 'Low' },
    medium: { vi: 'Trung bình', en: 'Medium' },
    high: { vi: 'Cao', en: 'High' },
    urgent: { vi: 'Khẩn cấp', en: 'Urgent' },
    freshman: { vi: 'Tân sinh viên', en: 'Freshman' },
    international: { vi: 'Sinh viên quốc tế', en: 'International' },
    policy: { vi: 'Diện chính sách', en: 'Policy' },
    scholarship: { vi: 'Học bổng', en: 'Scholarship' },
    none: { vi: 'Không', en: 'None' },
    online: { vi: 'Trực tuyến', en: 'Online' },
    bank_transfer: { vi: 'Chuyển khoản ngân hàng', en: 'Bank transfer' },
    cash: { vi: 'Tiền mặt', en: 'Cash' },
    electricity: { vi: 'Điện', en: 'Electricity' },
    water: { vi: 'Nước', en: 'Water' },
    furniture: { vi: 'Nội thất', en: 'Furniture' },
    air_conditioner: { vi: 'Điều hòa', en: 'Air conditioner' }
};

const TEXT_LABELS = [
    ['guest_portal', 'Cổng đăng nhập', 'Guest portal'],
    ['admin_center', 'Trung tâm quản trị', 'Control center'],
    ['manager_center', 'Điều phối ký túc xá', 'Dormitory operations'],
    ['student_portal', 'Cổng sinh viên', 'Student portal'],
    ['guest_role', 'Khách', 'Guest'],
    ['admin_role', 'Quản trị viên', 'Admin'],
    ['manager_role', 'Quản lý', 'Manager'],
    ['manager_role_full', 'Quản lý ký túc xá', 'Dormitory manager'],
    ['student_role', 'Sinh viên', 'Student'],
    ['role', 'Vai trò', 'Role'],
    ['choose_login_role', '-- Chọn vai trò đăng nhập --', '-- Select login role --'],
    ['username', 'Tên đăng nhập', 'Username'],
    ['password', 'Mật khẩu', 'Password'],
    ['login', 'Đăng nhập', 'Login'],
    ['login_intro', 'Đăng nhập vào hệ thống quản lý ký túc xá', 'Sign in to the dormitory management system'],
    ['forgot_password', 'Quên mật khẩu?', 'Forgot password?'],
    ['no_student_account', 'Chưa có tài khoản sinh viên?', 'No student account yet?'],
    ['create_account', 'Tạo tài khoản', 'Create account'],
    ['dashboard', 'Bảng điều khiển', 'Dashboard'],
    ['admin_dashboard', 'Bảng điều khiển quản trị', 'Admin dashboard'],
    ['admin_dashboard_intro', 'Quản lý tài khoản, tòa nhà, phòng, học kỳ, dịch vụ và giám sát toàn bộ hệ thống.', 'Manage users, buildings, rooms, semesters, services and monitor the whole system.'],
    ['manager_dashboard', 'Bảng điều khiển quản lý', 'Manager dashboard'],
    ['manager_dashboard_intro', 'Quản lý đăng ký phòng, hợp đồng, hóa đơn, sự cố và vi phạm.', 'Manage room registrations, contracts, invoices, incidents and violations.'],
    ['manager_dashboard_eyebrow', 'Trung tâm vận hành', 'Operations center'],
    ['system_status', 'Trạng thái hệ thống', 'System status'],
    ['ready_for_operations', 'Đang sẵn sàng vận hành', 'Ready for operations'],
    ['dashboard_data_note', 'Dữ liệu đang lấy trực tiếp từ cơ sở dữ liệu demo.', 'Data is loaded directly from the demo database.'],
    ['student_dashboard', 'Bảng điều khiển sinh viên', 'Student dashboard'],
    ['accounts', 'Tài khoản', 'Users'],
    ['students', 'Sinh viên', 'Students'],
    ['buildings', 'Tòa nhà', 'Buildings'],
    ['rooms', 'Phòng', 'Rooms'],
    ['semesters', 'Học kỳ', 'Semesters'],
    ['services', 'Dịch vụ', 'Services'],
    ['audit_logs', 'Nhật ký hệ thống', 'Audit logs'],
    ['reports', 'Báo cáo', 'Reports'],
    ['room_registrations', 'Đơn đăng ký phòng', 'Room registrations'],
    ['contracts', 'Hợp đồng', 'Contracts'],
    ['invoices', 'Hóa đơn', 'Invoices'],
    ['utility_readings', 'Chỉ số điện nước', 'Utility readings'],
    ['payments', 'Thanh toán', 'Payments'],
    ['maintenance', 'Sửa chữa', 'Maintenance'],
    ['violations', 'Vi phạm', 'Violations'],
    ['available_rooms', 'Phòng còn trống', 'Available rooms'],
    ['register_room', 'Đăng ký phòng', 'Register room'],
    ['my_registration', 'Đơn đăng ký của tôi', 'My registration'],
    ['my_registration_short', 'Đơn của tôi', 'My registration'],
    ['my_contract', 'Hợp đồng của tôi', 'My contract'],
    ['my_invoices', 'Hóa đơn của tôi', 'My invoices'],
    ['maintenance_request', 'Yêu cầu sửa chữa', 'Maintenance request'],
    ['my_violations', 'Vi phạm của tôi', 'My violations'],
    ['profile', 'Hồ sơ cá nhân', 'My profile'],
    ['logout', 'Đăng xuất', 'Logout'],
    ['system_overview', 'Tổng quan hệ thống', 'System overview'],
    ['operation_monitoring', 'Giám sát vận hành', 'Operation monitoring'],
    ['admin_quick_actions', 'Thao tác nhanh quản trị', 'Admin quick actions'],
    ['total_users', 'Tổng tài khoản', 'Total users'],
    ['total_students', 'Tổng sinh viên', 'Total students'],
    ['total_managers', 'Tổng quản lý', 'Total managers'],
    ['total_buildings', 'Tổng tòa nhà', 'Total buildings'],
    ['total_rooms', 'Tổng phòng', 'Total rooms'],
    ['maintenance_rooms', 'Phòng bảo trì', 'Maintenance rooms'],
    ['active_contracts', 'Hợp đồng đang hiệu lực', 'Active contracts'],
    ['pending_registrations', 'Đơn chờ duyệt', 'Pending registrations'],
    ['unpaid_invoices', 'Hóa đơn chưa thanh toán', 'Unpaid invoices'],
    ['open_maintenance', 'Yêu cầu sửa chữa mở', 'Open maintenance'],
    ['warning_students', 'Sinh viên cảnh báo', 'Warning students'],
    ['manage_users', 'Quản lý tài khoản', 'Manage users'],
    ['manage_buildings', 'Quản lý tòa nhà', 'Manage buildings'],
    ['manage_rooms', 'Quản lý phòng', 'Manage rooms'],
    ['manage_semesters', 'Quản lý học kỳ', 'Manage semesters'],
    ['manage_services', 'Quản lý dịch vụ', 'Manage services'],
    ['manage_users_desc', 'Quản lý tài khoản Quản trị viên, Quản lý và Sinh viên.', 'Manage Admin, Manager and Student accounts.'],
    ['manage_buildings_desc', 'Quản lý thông tin tòa nhà KTX.', 'Manage dormitory building information.'],
    ['manage_rooms_desc', 'Quản lý phòng, loại phòng, sức chứa và trạng thái.', 'Manage rooms, room types, capacity and status.'],
    ['manage_semesters_desc', 'Quản lý kỳ đăng ký KTX.', 'Manage dormitory registration semesters.'],
    ['manage_services_desc', 'Quản lý các dịch vụ tính phí.', 'Manage chargeable services.'],
    ['all', 'Tất cả', 'All'],
    ['create_service', 'Tạo dịch vụ', 'Create service'],
    ['service_name', 'Tên dịch vụ', 'Service name'],
    ['unit', 'Đơn vị', 'Unit'],
    ['default_price', 'Đơn giá mặc định', 'Default price'],
    ['status', 'Trạng thái', 'Status'],
    ['description', 'Mô tả', 'Description'],
    ['create_invoice', 'Tạo hóa đơn', 'Create invoice'],
    ['create_user', 'Tạo tài khoản', 'Create user'],
    ['create_building', 'Tạo tòa nhà', 'Create building'],
    ['create_room', 'Tạo phòng', 'Create room'],
    ['create_semester', 'Tạo học kỳ', 'Create semester'],
    ['back_to_registrations', 'Quay lại danh sách đơn', 'Back to registrations'],
    ['registration_detail', 'Chi tiết đơn đăng ký', 'Registration detail'],
    ['student_information', 'Thông tin sinh viên', 'Student information'],
    ['registration_information', 'Thông tin đơn đăng ký', 'Registration information'],
    ['reject_registration', 'Từ chối đơn đăng ký', 'Reject registration'],
    ['rejection_reason', 'Lý do từ chối', 'Rejection reason'],
    ['fetch_suggestions', 'Tải lại gợi ý phòng', 'Reload room suggestions'],
    ['loading', 'Đang tải...', 'Loading...'],
    ['select_room', 'Chọn phòng', 'Select room'],
    ['approve_contract', 'Duyệt và tạo hợp đồng', 'Approve and create contract'],
    ['no_suitable_room', 'Không có phòng phù hợp.', 'No suitable rooms found.'],
    ['register_student_account', 'Đăng ký tài khoản sinh viên', 'Create student account'],
    ['register_student_intro', 'Tạo tài khoản để đăng ký phòng, xem hợp đồng, hóa đơn và gửi yêu cầu sửa chữa.', 'Create an account to register rooms, view contracts, invoices and submit maintenance requests.'],
    ['account_information', 'Thông tin tài khoản', 'Account information'],
    ['student_profile', 'Hồ sơ sinh viên', 'Student profile'],
    ['phone', 'Số điện thoại', 'Phone number'],
    ['confirm_password', 'Xác nhận mật khẩu', 'Confirm password'],
    ['student_code', 'Mã sinh viên', 'Student code'],
    ['student_code_colon', 'Mã sinh viên:', 'Student code:'],
    ['full_name', 'Họ và tên', 'Full name'],
    ['full_name_colon', 'Họ và tên:', 'Full name:'],
    ['gender', 'Giới tính', 'Gender'],
    ['gender_colon', 'Giới tính:', 'Gender:'],
    ['select_gender', 'Chọn giới tính', 'Select gender'],
    ['dob', 'Ngày sinh', 'Date of birth'],
    ['program', 'Chương trình học', 'Program'],
    ['program_colon', 'Chương trình:', 'Program:'],
    ['faculty_colon', 'Khoa/Viện:', 'Faculty/Institute:'],
    ['priority_type', 'Diện ưu tiên', 'Priority type'],
    ['priority_type_colon', 'Diện ưu tiên:', 'Priority type:'],
    ['address', 'Địa chỉ', 'Address'],
    ['already_have_account', 'Đã có tài khoản?', 'Already have an account?'],
    ['back_to_login', 'Quay lại đăng nhập', 'Back to login'],
    ['reset_password', 'Đặt lại mật khẩu', 'Reset password'],
    ['reset_password_intro', 'Nhập tên đăng nhập và email đã đăng ký để tạo mật khẩu mới.', 'Enter your username and registered email to create a new password.'],
    ['new_password', 'Mật khẩu mới', 'New password'],
    ['confirm_new_password', 'Xác nhận mật khẩu mới', 'Confirm new password'],
    ['reset_password_button', 'Đặt lại mật khẩu', 'Reset password'],
    ['remembered_password', 'Đã nhớ mật khẩu?', 'Remembered your password?'],
    ['profile_intro', 'Quản lý thông tin tài khoản cá nhân, avatar và mật khẩu.', 'Manage personal account information, avatar and password.'],
    ['update_contact_avatar', 'Cập nhật liên hệ và avatar', 'Update contact and avatar'],
    ['save_profile', 'Lưu hồ sơ', 'Save profile'],
    ['change_password', 'Đổi mật khẩu', 'Change password'],
    ['current_password', 'Mật khẩu hiện tại', 'Current password'],
    ['enter_current_password', 'Nhập mật khẩu hiện tại', 'Enter current password'],
    ['enter_new_password', 'Nhập mật khẩu mới', 'Enter new password'],
    ['repeat_new_password', 'Nhập lại mật khẩu mới', 'Re-enter new password'],
    ['image_upload_hint_2mb', 'Cho phép JPG, PNG, WEBP. Tối đa 2MB.', 'JPG, PNG and WEBP are allowed. Maximum 2MB.'],
    ['image_upload_hint_5mb', 'Cho phép JPG, PNG, WEBP. Tối đa 5MB. Không bắt buộc.', 'JPG, PNG and WEBP are allowed. Maximum 5MB. Optional.'],
    ['admin_reports_intro', 'Admin xem báo cáo tổng quan về phòng, hợp đồng, hóa đơn và vi phạm.', 'Admin views summary reports about rooms, contracts, invoices and violations.'],
    ['no_violating_students', 'Chưa có sinh viên vi phạm.', 'No violating students yet.'],
    ['admin_semesters_intro', 'Admin quản lý học kỳ đăng ký ký túc xá.', 'Admin manages dormitory registration semesters.'],
    ['no_semesters', 'Không có học kỳ nào.', 'No semesters found.'],
    ['admin_rooms_intro', 'Admin quản lý phòng, loại phòng, sức chứa, giá phòng và trạng thái sử dụng.', 'Admin manages rooms, room types, capacity, monthly prices and usage status.'],
    ['no_rooms', 'Không có phòng nào.', 'No rooms found.'],
    ['admin_buildings_intro', 'Admin quản lý thông tin các tòa nhà trong ký túc xá.', 'Admin manages dormitory building information.'],
    ['building_note_placeholder', 'Ghi chú về tòa nhà', 'Building notes'],
    ['no_buildings', 'Không có tòa nhà nào.', 'No buildings found.'],
    ['manage_students', 'Quản lý sinh viên', 'Manage students'],
    ['admin_students_intro', 'Admin quản lý hồ sơ sinh viên. User role Student cần có student profile để sử dụng đầy đủ các flow.', 'Admin manages student profiles. Student users need a student profile to use all flows.'],
    ['no_student_profiles', 'Không có hồ sơ sinh viên nào.', 'No student profiles found.'],
    ['audit_logs_intro', 'Admin theo dõi lịch sử thao tác quan trọng trong hệ thống.', 'Admin tracks important activity history in the system.'],
    ['no_audit_logs', 'Chưa có audit log nào.', 'No audit logs yet.'],
    ['admin_users_intro', 'Admin quản lý toàn bộ tài khoản đăng nhập của hệ thống: Admin, Manager và Student.', 'Admin manages all login accounts in the system: Admin, Manager and Student.'],
    ['default_password_placeholder', 'Mật khẩu mặc định', 'Default password'],
    ['no_users', 'Không có user nào.', 'No users found.'],
    ['admin_services_intro', 'Admin quản lý các dịch vụ tính phí như điện, nước, internet, vệ sinh, gửi xe.', 'Admin manages chargeable services such as electricity, water, internet, cleaning and parking.'],
    ['service_note_placeholder', 'Ghi chú về dịch vụ', 'Service notes'],
    ['no_services', 'Không có dịch vụ nào.', 'No services found.'],
    ['create_invoice_intro', 'Tạo hóa đơn mới cho hợp đồng active.', 'Create a new invoice for an active contract.'],
    ['choose_contract', '-- Chọn hợp đồng --', '-- Select contract --'],
    ['no_choice', '-- Không chọn --', '-- No selection --'],
    ['blank_monthly_price_hint', 'Để trống sẽ lấy monthly_price của contract', 'Leave blank to use the contract monthly price'],
    ['manager_invoices_intro', 'Quản lý hóa đơn KTX của sinh viên.', 'Manage student dormitory invoices.'],
    ['no_matching_invoices', 'Không có hóa đơn nào phù hợp.', 'No matching invoices found.'],
    ['manager_registrations_intro', 'Danh sách đơn đăng ký phòng của sinh viên.', 'List of student room registrations.'],
    ['no_registrations', 'Chưa có đơn đăng ký nào.', 'No registrations yet.'],
    ['violation_records', 'Biên bản vi phạm', 'Violation records'],
    ['admin_violations_intro', 'Admin theo dõi toàn bộ biên bản vi phạm, điểm cảnh báo và người ghi nhận.', 'Admin monitors all violation records, warning points and record owners.'],
    ['total_violations', 'Tổng biên bản', 'Total records'],
    ['critical_students', 'Sinh viên critical', 'Critical students'],
    ['total_penalty_points', 'Tổng điểm phạt', 'Total penalty points'],
    ['penalty_points', 'Điểm phạt', 'Penalty points'],
    ['violation_type', 'Loại vi phạm', 'Violation type'],
    ['violation_date', 'Ngày vi phạm', 'Violation date'],
    ['recorded_by', 'Người ghi nhận', 'Recorded by'],
    ['choose_violation_type_first', 'Chọn loại vi phạm trước', 'Select violation type first'],
    ['choose_violation_type_note', 'Vui lòng chọn loại vi phạm để hệ thống tự xác định điểm phạt.', 'Please select a violation type so the system can determine penalty points.'],
    ['enter_other_penalty', 'Nhập điểm phạt cho loại Khác', 'Enter penalty points for Other'],
    ['other_penalty_note', 'Loại Khác cho phép quản lý nhập điểm phạt thủ công trong khoảng 1 - 20.', 'Other allows the manager to enter penalty points manually from 1 to 20.'],
    ['auto_penalty_note', 'Điểm phạt đã được hệ thống tự động gán theo loại vi phạm.', 'Penalty points were automatically assigned by violation type.'],
    ['manager_violations_intro', 'Manager tạo biên bản vi phạm. Điểm phạt được hệ thống tự động gán theo loại vi phạm. Nếu sinh viên vượt ngưỡng Critical Warning, Manager có thể chấm dứt hợp đồng KTX.', 'Manager creates violation records. Penalty points are assigned automatically by violation type. If a student exceeds the Critical Warning threshold, the manager can terminate the dormitory contract.'],
    ['maintenance_management', 'Quản lý sửa chữa', 'Maintenance management'],
    ['manager_maintenance_intro', 'Manager xem ảnh minh chứng và cập nhật trạng thái các yêu cầu sửa chữa từ sinh viên.', 'Manager reviews evidence images and updates student maintenance request statuses.'],
    ['no_matching_maintenance', 'Không có yêu cầu sửa chữa nào phù hợp.', 'No matching maintenance requests found.'],
    ['manager_payments_intro', 'Manager xác nhận hoặc từ chối các khoản chuyển khoản sinh viên gửi lên.', 'Manager approves or rejects bank transfer submissions from students.'],
    ['no_matching_payments', 'Không có payment nào phù hợp.', 'No matching payments found.'],
    ['manager_contracts_intro', 'Quản lý và theo dõi danh sách hợp đồng KTX. Manager có thể checkout/kết thúc hợp đồng đang active.', 'Manage and track dormitory contracts. Managers can check out or end active contracts.'],
    ['no_matching_contracts', 'Không có hợp đồng nào phù hợp.', 'No matching contracts found.'],
    ['utility_readings_intro', 'Manager nhập chỉ số điện/nước theo phòng. Sau đó hệ thống tự chia tiền và sinh invoice cho sinh viên đang có hợp đồng active trong phòng.', 'Manager enters room electricity/water readings. The system then splits charges and creates invoices for students with active contracts in that room.'],
    ['no_utility_readings', 'Chưa có utility reading nào.', 'No utility readings yet.'],
    ['registration_detail_intro', 'Chi tiết đơn đăng ký và xử lý duyệt / từ chối.', 'View registration detail and approve or reject it.'],
    ['suitable_rooms', 'Phòng phù hợp có thể xếp', 'Suitable rooms for assignment'],
    ['suitable_rooms_desc', 'Dữ liệu được lọc theo giới tính, loại phòng, tòa mong muốn và số giường còn trống.', 'Data is filtered by gender, room type, preferred building and remaining beds.'],
    ['no_room_for_approval', 'Không có phòng phù hợp để duyệt đơn này.', 'No suitable rooms are available to approve this registration.'],
    ['select_room_placeholder', '-- Chọn phòng --', '-- Select room --'],
    ['registration_processed_notice', 'Đơn này đã được xử lý. Không thể duyệt hoặc từ chối lại.', 'This registration has already been processed and cannot be approved or rejected again.'],
    ['student_dashboard_intro', 'Theo dõi đăng ký phòng, hợp đồng, hóa đơn và các yêu cầu của bạn.', 'Track your room registration, contract, invoices and requests.'],
    ['student_payment_submit', 'Gửi thông tin chuyển khoản', 'Submit transfer information'],
    ['student_payment_submit_intro', 'Gửi thông tin chuyển khoản để Manager kiểm tra và xác nhận thanh toán.', 'Submit transfer information for the manager to verify and confirm payment.'],
    ['student_maintenance_intro', 'Gửi yêu cầu sửa chữa, đính kèm ảnh minh chứng và theo dõi trạng thái xử lý.', 'Submit maintenance requests, attach evidence images and track processing status.'],
    ['student_not_found', 'Không tìm thấy hồ sơ sinh viên.', 'Student profile not found.'],
    ['current_room_colon', 'Phòng hiện tại:', 'Current room:'],
    ['no_active_contract', 'Chưa có hợp đồng active', 'No active contract'],
    ['normal_status', 'Bình thường', 'Normal'],
    ['admin_dashboard_eyebrow', 'Trung tâm quản trị', 'Administration center'],
    ['admin_scope', 'Phạm vi quản trị', 'Administration scope'],
    ['whole_system', 'Toàn hệ thống', 'Whole system'],
    ['admin_scope_note', 'Điều phối dữ liệu nền cho sinh viên và quản lý vận hành.', 'Coordinate master data for students and operations managers.'],
    ['admin_users_metric_note', 'Quản lý quyền truy cập toàn hệ thống', 'Manage system-wide access'],
    ['admin_students_metric_note', 'Theo dõi hồ sơ và dữ liệu sinh viên', 'Track student profiles and data'],
    ['admin_managers_metric_note', 'Tài khoản phụ trách vận hành KTX', 'Accounts responsible for dormitory operations'],
    ['admin_buildings_metric_note', 'Cấu hình khu nhà ký túc xá', 'Configure dormitory buildings'],
    ['admin_rooms_metric_note', 'Quản lý sức chứa, loại phòng và trạng thái', 'Manage capacity, room types and status'],
    ['admin_data_queue', 'Dữ liệu cần chú ý', 'Data requiring attention'],
    ['available_rooms_admin_note', 'Kiểm tra nguồn phòng có thể tiếp nhận sinh viên.', 'Check room inventory available for student assignment.'],
    ['maintenance_rooms_admin_note', 'Theo dõi phòng tạm dừng khai thác.', 'Track rooms temporarily unavailable.'],
    ['warning_students_admin_note', 'Theo dõi sinh viên có điểm vi phạm vượt ngưỡng.', 'Track students whose violation points pass warning thresholds.'],
    ['admin_tools', 'Công cụ quản trị', 'Admin tools'],
    ['admin_flow', 'Luồng quản trị', 'Administration flow'],
    ['admin_data_workflow', 'Chuẩn hóa dữ liệu nền hệ thống', 'Standardize system master data'],
    ['admin_step_accounts', 'Tạo tài khoản và hồ sơ', 'Create accounts and profiles'],
    ['admin_step_facilities', 'Thiết lập tòa nhà và phòng', 'Set up buildings and rooms'],
    ['admin_step_terms_services', 'Cấu hình học kỳ và dịch vụ', 'Configure semesters and services'],
    ['admin_step_reports', 'Theo dõi nhật ký và báo cáo', 'Monitor logs and reports'],
    ['registration_status', 'Trạng thái đăng ký', 'Registration status'],
    ['contract_status', 'Hợp đồng', 'Contract'],
    ['unpaid_invoice_count', 'Hóa đơn cần thanh toán', 'Invoices to pay'],
    ['maintenance_requests', 'Yêu cầu sửa chữa', 'Maintenance requests'],
    ['violation_points', 'Điểm vi phạm', 'Violation points'],
    ['view_registration', 'Xem đơn đăng ký phòng của bạn', 'View your room registration'],
    ['view_contract', 'Theo dõi hợp đồng ký túc xá', 'Track dormitory contract'],
    ['student_priority_tasks', 'Theo dõi cá nhân', 'Personal tracking'],
    ['current_room', 'Phòng hiện tại', 'Current room'],
    ['current_room_empty', 'Chưa có hợp đồng phòng đang hiệu lực.', 'No active room contract yet.'],
    ['start_date', 'Ngày bắt đầu', 'Start date'],
    ['end_date', 'Ngày kết thúc', 'End date'],
    ['recent_activity', 'Hoạt động gần đây', 'Recent activity'],
    ['recent_maintenance_requests', 'Yêu cầu sửa chữa mới', 'Recent maintenance requests'],
    ['student_tools', 'Công cụ sinh viên', 'Student tools'],
    ['find_available_rooms', 'Tìm phòng còn trống', 'Find available rooms'],
    ['submit_room_registration', 'Đăng ký phòng', 'Submit room registration'],
    ['view_my_invoices', 'Xem hóa đơn và gửi minh chứng chuyển khoản', 'View invoices and submit transfer proof'],
    ['submit_maintenance_request', 'Gửi và theo dõi yêu cầu sửa chữa', 'Submit and track maintenance requests'],
    ['student_flow', 'Luồng sinh viên', 'Student flow'],
    ['student_dormitory_workflow', 'Quy trình sử dụng ký túc xá', 'Dormitory usage workflow'],
    ['student_step_register', 'Đăng ký phòng', 'Register for a room'],
    ['student_step_contract', 'Theo dõi hợp đồng', 'Track contract'],
    ['student_step_payment', 'Gửi minh chứng thanh toán', 'Submit payment proof'],
    ['student_step_support', 'Gửi sửa chữa và theo dõi vi phạm', 'Submit maintenance and track violations'],
    ['invoice_information', 'Thông tin hóa đơn', 'Invoice information'],
    ['contract', 'Hợp đồng', 'Contract'],
    ['amount_to_transfer', 'Số tiền cần chuyển', 'Amount to transfer'],
    ['dormitory_bank_account', 'Tài khoản KTX', 'Dormitory bank account'],
    ['bank', 'Ngân hàng', 'Bank'],
    ['account_name', 'Tên tài khoản', 'Account name'],
    ['account_number', 'Số tài khoản', 'Account number'],
    ['transfer_content', 'Nội dung chuyển khoản', 'Transfer content'],
    ['payment_proof', 'Minh chứng thanh toán', 'Payment proof'],
    ['your_bank_transfer_information', 'Thông tin chuyển khoản của bạn', 'Your bank transfer information'],
    ['sender_bank', 'Ngân hàng chuyển', 'Sender bank'],
    ['sender_account_name', 'Tên chủ tài khoản', 'Sender account name'],
    ['transaction_reference', 'Mã giao dịch', 'Transaction reference'],
    ['auto_submit_time', 'Thời gian gửi', 'Submission time'],
    ['payment_time_auto_note', 'Thời gian gửi sẽ được hệ thống ghi nhận tự động khi bạn bấm gửi.', 'The submission time will be recorded automatically when you submit.'],
    ['transfer_note', 'Ghi chú chuyển khoản', 'Transfer note'],
    ['submit_payment_proof', 'Gửi minh chứng thanh toán', 'Submit payment proof'],
    ['back_to_my_invoices', 'Quay lại hóa đơn của tôi', 'Back to my invoices'],
    ['print_contract', 'In hợp đồng', 'Print contract'],
    ['print_invoice', 'In hóa đơn', 'Print invoice'],
    ['go_back', 'Quay lại', 'Go back'],
    ['print_contract_title', 'Hợp đồng ký túc xá', 'Dormitory contract'],
    ['print_contract_subtitle', 'Bản in phục vụ đối chiếu thông tin cư trú sinh viên.', 'Printable copy for student residence verification.'],
    ['print_invoice_title', 'Hóa đơn ký túc xá', 'Dormitory invoice'],
    ['print_invoice_subtitle', 'Bản in phục vụ đối chiếu thanh toán và xác nhận công nợ.', 'Printable copy for payment reconciliation and balance confirmation.'],
    ['room_information', 'Thông tin phòng ở', 'Room information'],
    ['contract_terms', 'Điều khoản tài chính và thời hạn', 'Financial terms and duration'],
    ['student_signature', 'Sinh viên', 'Student'],
    ['manager_signature', 'Quản lý KTX', 'Dormitory manager'],
    ['sign_and_full_name', 'Ký và ghi rõ họ tên', 'Signature and full name'],
    ['semester', 'Học kỳ', 'Semester'],
    ['invoice_detail_list', 'Chi tiết hóa đơn', 'Invoice details'],
    ['no_invoice_details', 'Không có dòng chi tiết hóa đơn.', 'No invoice detail rows.'],
    ['payment_summary', 'Tổng hợp thanh toán', 'Payment summary'],
    ['payment_history', 'Lịch sử thanh toán', 'Payment history'],
    ['need_active_contract_maintenance', 'Bạn cần có hợp đồng active trước khi gửi yêu cầu sửa chữa.', 'You need an active contract before submitting a maintenance request.'],
    ['no_maintenance_requests', 'Bạn chưa có yêu cầu sửa chữa nào.', 'You have no maintenance requests yet.'],
    ['student_violations_intro', 'Xem lịch sử vi phạm nội quy KTX và mức cảnh báo hiện tại.', 'View your dormitory rule violation history and current warning level.'],
    ['no_violations_student', 'Bạn chưa có vi phạm nào.', 'You have no violations.'],
    ['warning_none_message', 'Bạn chưa nằm trong nhóm cảnh báo. Hãy tiếp tục tuân thủ nội quy KTX.', 'You are not in a warning group. Please continue following dormitory rules.'],
    ['warning_low_message', 'Bạn đã có một số điểm vi phạm. Cần chú ý hơn trong sinh hoạt tại KTX.', 'You have some violation points. Please be more careful in dormitory life.'],
    ['warning_high_message', 'Bạn đang ở mức cảnh báo nghiêm trọng. Cần chú ý tuân thủ nội quy để tránh bị xử lý kỷ luật.', 'You are at a serious warning level. Follow the rules carefully to avoid disciplinary action.'],
    ['warning_critical_message', 'Bạn đang ở mức cảnh báo rất nghiêm trọng. Có thể bị xem xét kỷ luật hoặc chấm dứt hợp đồng KTX.', 'You are at a very serious warning level and may face disciplinary review or dormitory contract termination.'],
    ['bank_example_placeholder', 'Ví dụ: Vietcombank, BIDV, Techcombank...', 'Example: Vietcombank, BIDV, Techcombank...'],
    ['account_owner_placeholder', 'Tên chủ tài khoản đã chuyển khoản', 'Name of the transfer account holder'],
    ['transaction_code_placeholder', 'Mã giao dịch trên app ngân hàng', 'Transaction code from the banking app'],
    ['transfer_note_placeholder', 'Nội dung chuyển khoản hoặc ghi chú thêm', 'Transfer content or additional notes'],
    ['manager_dashboard_pending', 'Đơn chờ duyệt', 'Pending Registrations'],
    ['manager_dashboard_unpaid_overdue', 'Hóa đơn chưa thanh toán / quá hạn', 'Unpaid / Overdue Invoices'],
    ['manager_dashboard_open_maintenance', 'Yêu cầu sửa chữa đang mở', 'Open Maintenance'],
    ['manager_dashboard_violation_warnings', 'Cảnh báo vi phạm', 'Violation Warnings'],
    ['review_room_requests', 'Xem và duyệt đơn đăng ký phòng', 'Review room registration requests'],
    ['review_active_contracts', 'Theo dõi hợp đồng đang ở ký túc xá', 'Track active dormitory contracts'],
    ['verify_invoices_payments', 'Kiểm tra hóa đơn và thanh toán', 'Check invoices and payments'],
    ['coordinate_repairs', 'Điều phối xử lý sự cố phòng ở', 'Coordinate room issue handling'],
    ['review_violation_records', 'Theo dõi sinh viên có điểm vi phạm', 'Track students with violation points'],
    ['priority_tasks', 'Công việc ưu tiên', 'Priority tasks'],
    ['today_queue', 'Hàng đợi hôm nay', 'Today queue'],
    ['pending_registration_review', 'Duyệt đơn đăng ký phòng', 'Review room registrations'],
    ['registrations_subtitle', 'Ưu tiên xử lý đơn đang chờ để sinh viên nhận phòng đúng hạn.', 'Prioritize pending requests so students can move in on time.'],
    ['unpaid_invoice_followup', 'Đối soát hóa đơn chưa thanh toán', 'Reconcile unpaid invoices'],
    ['unpaid_invoice_subtitle', 'Kiểm tra khoản chuyển khoản và cập nhật trạng thái hóa đơn.', 'Review transfers and update invoice statuses.'],
    ['open_maintenance_followup', 'Theo dõi yêu cầu sửa chữa', 'Follow maintenance requests'],
    ['open_maintenance_subtitle', 'Phân loại mức ưu tiên và cập nhật tiến độ xử lý.', 'Classify priority and update processing progress.'],
    ['quick_operations', 'Thao tác nhanh', 'Quick operations'],
    ['manager_tools', 'Công cụ quản lý', 'Manager tools'],
    ['verify_payments', 'Xác nhận thanh toán', 'Verify payments'],
    ['record_utility_readings', 'Nhập chỉ số điện nước', 'Record utility readings'],
    ['operational_flow', 'Luồng vận hành', 'Operational flow'],
    ['weekly_flow', 'Theo dõi quy trình ký túc xá', 'Track the dormitory workflow'],
    ['step_room_registration', 'Duyệt đăng ký phòng', 'Approve room registrations'],
    ['step_contract_invoice', 'Tạo hợp đồng và hóa đơn', 'Create contracts and invoices'],
    ['step_payment_confirm', 'Xác nhận thanh toán', 'Confirm payments'],
    ['step_maintenance_violation', 'Theo dõi sửa chữa và vi phạm', 'Monitor maintenance and violations'],
    ['desired_building', 'Tòa mong muốn', 'Desired Building'],
    ['desired_room_type', 'Loại phòng mong muốn', 'Desired Room Type'],
    ['desired_gender_type', 'Giới tính phòng mong muốn', 'Desired Gender Type'],
    ['room_type', 'Loại phòng', 'Room Type'],
    ['priority_score', 'Điểm ưu tiên', 'Priority Score'],
    ['score', 'Điểm ưu tiên', 'Score'],
    ['created_at', 'Ngày tạo', 'Created At'],
    ['action', 'Thao tác', 'Action'],
    ['detail', 'Chi tiết', 'Detail'],
    ['total_contracts', 'Tổng hợp đồng', 'Total Contracts'],
    ['active_effective', 'Đang hiệu lực', 'Active'],
    ['active_contracts_effective', 'Hợp đồng đang hiệu lực', 'Active Contracts'],
    ['contract_code', 'Mã hợp đồng', 'Contract Code'],
    ['student', 'Sinh viên', 'Student'],
    ['room', 'Phòng', 'Room'],
    ['duration', 'Thời hạn', 'Duration'],
    ['monthly_price', 'Giá hàng tháng', 'Monthly Price'],
    ['deposit', 'Tiền cọc', 'Deposit'],
    ['created_by', 'Người tạo', 'Created By'],
    ['end_action', 'Kết thúc', 'Checkout'],
    ['ended_label', 'Kết thúc:', 'Ended:'],
    ['by_label', 'Bởi:', 'By:'],
    ['end_contract', 'Kết thúc hợp đồng', 'End Contract'],
    ['checkout_note_placeholder', 'Ghi chú kết thúc', 'Checkout note'],
    ['no_checkout_note', 'Không có ghi chú kết thúc', 'No checkout note'],
    ['total_invoices', 'Tổng hóa đơn', 'Total Invoices'],
    ['invoice_code', 'Mã hóa đơn', 'Invoice Code'],
    ['month', 'Tháng', 'Month'],
    ['due_date', 'Hạn thanh toán', 'Due Date'],
    ['total_amount', 'Tổng tiền', 'Total Amount'],
    ['paid_amount', 'Đã thanh toán', 'Paid Amount'],
    ['remaining', 'Còn lại', 'Remaining'],
    ['total_payments', 'Tổng thanh toán', 'Total Payments'],
    ['no_matching_payments_vi', 'Không có thanh toán nào phù hợp.', 'No matching payments found.'],
    ['payment', 'Thanh toán', 'Payment'],
    ['invoice', 'Hóa đơn', 'Invoice'],
    ['bank_proof', 'Thông tin chuyển khoản', 'Bank Proof'],
    ['amount', 'Số tiền', 'Amount'],
    ['verified_by', 'Người xác nhận', 'Verified By'],
    ['method_label', 'Phương thức:', 'Method:'],
    ['submitted_label', 'Ngày gửi:', 'Submitted:'],
    ['invoice_status_label', 'Trạng thái hóa đơn:', 'Invoice status:'],
    ['bank_label', 'Ngân hàng:', 'Bank:'],
    ['account_label', 'Tài khoản:', 'Account:'],
    ['ref_label', 'Mã giao dịch:', 'Ref:'],
    ['note_label', 'Ghi chú:', 'Note:'],
    ['approve', 'Xác nhận', 'Approve'],
    ['approved_badge', 'Đã duyệt', 'Approved'],
    ['reject_reason', 'Lý do từ chối', 'Reject reason'],
    ['total_requests', 'Tổng yêu cầu', 'Total'],
    ['issue', 'Sự cố', 'Issue'],
    ['evidence', 'Minh chứng', 'Evidence'],
    ['priority', 'Mức ưu tiên', 'Priority'],
    ['request_date', 'Ngày yêu cầu', 'Request Date'],
    ['processed_by', 'Người xử lý', 'Processed By'],
    ['update', 'Cập nhật', 'Update'],
    ['category_label', 'Danh mục:', 'Category:'],
    ['view_image', 'Xem ảnh', 'View image'],
    ['resolution_note', 'Ghi chú xử lý', 'Resolution note'],
    ['manager_maintenance_intro_vi', 'Quản lý xem ảnh minh chứng và cập nhật trạng thái các yêu cầu sửa chữa từ sinh viên.', 'Manager reviews evidence images and updates student maintenance request statuses.'],
    ['manager_payments_intro_vi', 'Quản lý xác nhận hoặc từ chối các khoản chuyển khoản sinh viên gửi lên.', 'Manager approves or rejects bank transfer submissions from students.'],
    ['manager_contracts_intro_vi', 'Quản lý và theo dõi danh sách hợp đồng KTX. Quản lý có thể kết thúc hợp đồng đang hiệu lực.', 'Manage and track dormitory contracts. Managers can end active contracts.'],
    ['create_invoice_intro_active', 'Tạo hóa đơn mới cho hợp đồng đang hiệu lực.', 'Create a new invoice for an active contract.'],
    ['utility_readings_intro_vi2', 'Quản lý nhập chỉ số điện/nước theo phòng. Sau đó hệ thống tự chia tiền và sinh hóa đơn cho sinh viên đang có hợp đồng hiệu lực trong phòng.', 'Managers enter room electricity/water readings. The system then splits charges and creates invoices for students with active contracts in that room.'],
    ['create_violation_record', 'Tạo biên bản vi phạm', 'Create Violation Record'],
    ['serious', 'Nghiêm trọng', 'Serious'],
    ['critical', 'Rất nghiêm trọng', 'Critical'],
    ['select_student_placeholder', '-- Chọn sinh viên --', '-- Select student --'],
    ['select_violation_type_placeholder', '-- Chọn loại vi phạm --', '-- Select violation type --'],
    ['late_return', 'Về muộn', 'Late return'],
    ['noise_disturbance', 'Gây ồn', 'Noise disturbance'],
    ['poor_hygiene', 'Vệ sinh kém', 'Poor hygiene'],
    ['unauthorized_room_change', 'Tự ý đổi phòng', 'Unauthorized room change'],
    ['unpaid_fee', 'Chưa thanh toán phí', 'Unpaid fee'],
    ['damage_to_property', 'Làm hư hỏng tài sản', 'Damage to property'],
    ['smoking_alcohol_violation', 'Hút thuốc hoặc sử dụng rượu bia', 'Smoking or alcohol violation'],
    ['unauthorized_guest', 'Khách không được phép', 'Unauthorized guest'],
    ['custom_points', 'điểm tùy chỉnh', 'custom points'],
    ['points', 'Điểm', 'Points'],
    ['point_rules', 'Bảng điểm vi phạm', 'Violation Point Rules'],
    ['custom', 'Tùy chỉnh', 'Custom'],
    ['student_warning_summary', 'Tổng hợp sinh viên cảnh báo', 'Student Warning Summary'],
    ['faculty', 'Khoa/Viện', 'Faculty'],
    ['violation_count', 'Số lần vi phạm', 'Violation Count'],
    ['total_points', 'Tổng điểm', 'Total Points'],
    ['warning_level', 'Mức cảnh báo', 'Warning Level'],
    ['active_contract', 'Hợp đồng hiệu lực', 'Active Contract'],
    ['warning_label', 'Cảnh báo', 'Warning'],
    ['serious_warning', 'Cảnh báo nghiêm trọng', 'Serious Warning'],
    ['critical_warning', 'Cảnh báo rất nghiêm trọng', 'Critical Warning'],
    ['no_active_contract_vi', 'Không có hợp đồng đang hiệu lực', 'No active contract'],
    ['terminate_contract', 'Chấm dứt hợp đồng', 'Terminate Contract'],
    ['below_threshold', 'Chưa đủ ngưỡng.', 'Below threshold.'],
    ['violation_list', 'Danh sách vi phạm', 'Violation List'],
    ['recorded_by', 'Người ghi nhận', 'Recorded By'],
    ['made_noise_desc', 'Gây ồn sau giờ yên tĩnh.', 'Made loud noise after quiet hours.'],
    ['damaged_desk_desc', 'Làm hỏng bàn học trong phòng.', 'Damaged study desk in the room.'],
    ['hygiene_desc', 'Vệ sinh phòng không đạt tiêu chuẩn KTX.', 'Room hygiene did not meet dormitory standard.'],
    ['guest_desc', 'Có khách không được phép sau giờ thăm.', 'Had an unauthorized guest after visiting hours.'],
    ['door_lock_issue', 'Khóa cửa bị kẹt', 'Door lock issue'],
    ['door_lock_desc', 'Khóa cửa phòng khó mở.', 'Room door lock is difficult to open.'],
    ['broken_light', 'Bóng đèn bị hỏng', 'Broken light'],
    ['broken_light_desc', 'Bóng đèn trần gần cửa không hoạt động.', 'The ceiling light near the door is not working.'],
    ['water_leakage', 'Rò rỉ nước', 'Water leakage'],
    ['water_leakage_desc', 'Có rò rỉ nước nhỏ gần bồn rửa trong phòng tắm.', 'There is a small water leakage near the bathroom sink.'],
    ['air_conditioner_noise', 'Điều hòa phát tiếng ồn', 'Air conditioner noise'],
    ['air_conditioner_noise_desc', 'Điều hòa phát tiếng ồn lớn vào ban đêm.', 'The air conditioner makes loud noise at night.'],
    ['technician_assigned', 'Đã phân công kỹ thuật viên', 'Technician assigned'],
    ['ac_fan_cleaned', 'Đã vệ sinh quạt điều hòa', 'AC fan cleaned'],
    ['full_payment_received', 'Đã nhận đủ thanh toán', 'Full payment received'],
    ['partial_payment', 'Thanh toán một phần', 'Partial payment'],
    ['pending_payment_not_counted', 'Thanh toán chờ duyệt chưa được tính', 'Pending payment not counted'],
    ['cash_desk', 'Quầy thu tiền mặt', 'Cash desk'],
    ['building_a', 'Tòa A', 'Building A'],
    ['building_b', 'Tòa B', 'Building B'],
    ['building_c', 'Tòa C', 'Building C'],
    ['spring_2026', 'Học kỳ Xuân 2026', 'Spring 2026'],
    ['information_technology', 'Công nghệ thông tin', 'Information Technology'],
    ['software_engineering', 'Kỹ thuật phần mềm', 'Software Engineering'],
    ['data_science', 'Khoa học dữ liệu', 'Data Science'],
    ['cyber_security', 'An toàn thông tin', 'Cyber Security'],
    ['artificial_intelligence', 'Trí tuệ nhân tạo', 'Artificial Intelligence'],
    ['registration_id', 'Mã đơn', 'Registration ID'],
    ['assigned_room', 'Phòng được xếp', 'Assigned Room'],
    ['building', 'Tòa nhà', 'Building'],
    ['capacity', 'Sức chứa', 'Capacity'],
    ['current_occupancy', 'Đang ở', 'Current'],
    ['available_beds', 'Giường trống', 'Available Beds'],
    ['price', 'Giá', 'Price'],
    ['back_to_registrations_short', 'Quay lại danh sách đơn', 'Back to Registrations'],
    ['room_rent', 'Tính tiền phòng', 'Include Room Rent'],
    ['room_rent_amount', 'Số tiền phòng', 'Room Rent Amount'],
    ['additional_services', 'Dịch vụ phát sinh', 'Additional Services'],
    ['quantity', 'Số lượng', 'Quantity'],
    ['unit_price', 'Đơn giá', 'Unit Price'],
    ['back_to_invoices', 'Quay lại danh sách hóa đơn', 'Back to Invoices'],
    ['create_utility_reading', 'Tạo chỉ số điện nước', 'Create Utility Reading'],
    ['select_service_placeholder', '-- Chọn dịch vụ --', '-- Select service --'],
    ['optional_placeholder', '-- Không bắt buộc --', '-- Optional --'],
    ['reading_month', 'Tháng ghi chỉ số', 'Reading Month'],
    ['previous_reading', 'Chỉ số cũ', 'Previous Reading'],
    ['current_reading', 'Chỉ số mới', 'Current Reading'],
    ['create_reading', 'Tạo chỉ số', 'Create Reading'],
    ['reading_list', 'Danh sách chỉ số', 'Reading List'],
    ['consumption', 'Tiêu thụ', 'Consumption'],
    ['generate_invoice', 'Sinh hóa đơn', 'Generate Invoice'],
    ['maintenance_title_placeholder', 'Ví dụ: Bóng đèn phòng bị hỏng', 'Example: The room light is broken'],
    ['maintenance_desc_placeholder', 'Mô tả chi tiết vấn đề cần sửa chữa', 'Describe the issue that needs repair'],
    ['example_prefix', 'Ví dụ:', 'Example:'],
    ['room_availability_label', 'Tình trạng phòng', 'Room Availability'],
    ['find_room_title', 'Tìm phòng ký túc xá phù hợp', 'Find a suitable dormitory room'],
    ['room_list', 'Danh sách phòng', 'Room List'],
    ['available_rooms_count', 'Số phòng còn trống', 'Available Rooms'],
    ['price_per_month', 'Giá / tháng', 'Price / Month'],
    ['current_status', 'Trạng thái hiện tại', 'Current Status'],
    ['active_dorm_contract', 'Hợp đồng ký túc xá đang hiệu lực', 'Active Dormitory Contract'],
    ['view_my_contract', 'Xem hợp đồng của tôi', 'View My Contract'],
    ['view_my_registration', 'Xem đơn đăng ký của tôi', 'View My Registration'],
    ['room_registration_submitted', 'Đơn đăng ký phòng đã được gửi', 'Room Registration Submitted'],
    ['room_registration_form', 'Biểu mẫu đăng ký phòng', 'Room Registration Form'],
    ['select_semester', 'Chọn học kỳ', 'Select semester'],
    ['select_room_type', 'Chọn loại phòng', 'Select room type'],
    ['no_specific_building', 'Không chọn tòa cụ thể', 'No specific building'],
    ['submit_registration', 'Gửi đăng ký', 'Submit Registration'],
    ['student_profile_not_found', 'Không tìm thấy hồ sơ sinh viên.', 'Student profile not found.'],
    ['no_rooms_now', 'Hiện chưa có phòng trống.', 'No available rooms at the moment.'],
    ['back_to_dashboard', 'Quay lại bảng điều khiển', 'Back to Dashboard'],
    ['registration_overview', 'Tổng quan đăng ký', 'Registration Overview'],
    ['track_registration', 'Theo dõi hồ sơ đăng ký phòng ký túc xá', 'Track your dormitory room registration'],
    ['registration_history', 'Lịch sử đăng ký', 'Registration History'],
    ['registration_table', 'Bảng đơn đăng ký', 'Registration Table'],
    ['total_registrations', 'Tổng số đơn', 'Total Registrations'],
    ['latest_registration', 'Đơn mới nhất', 'Latest Registration'],
    ['priority_score', 'Mức ưu tiên', 'Priority Score'],
    ['not_assigned', 'Chưa xếp phòng', 'Not assigned'],
    ['rejection_reason_vn', 'Lý do từ chối', 'Rejection Reason'],
    ['contract_overview', 'Tổng quan hợp đồng', 'Contract Overview'],
    ['no_contract_found', 'Chưa có hợp đồng ký túc xá.', 'No dormitory contract found.'],
    ['view_invoices', 'Xem hóa đơn', 'View Invoices'],
    ['latest_contract', 'Hợp đồng mới nhất', 'Latest Contract'],
    ['per_month', 'mỗi tháng', 'per month'],
    ['deposit_amount', 'Tiền đặt cọc', 'Deposit Amount'],
    ['billing_overview', 'Tổng quan hóa đơn', 'Billing Overview'],
    ['no_invoices_found', 'Chưa có hóa đơn nào.', 'No invoices found.'],
    ['total_invoices', 'Tổng số hóa đơn', 'Total Invoices'],
    ['total_amount', 'Tổng tiền', 'Total Amount'],
    ['paid_amount', 'Đã thanh toán', 'Paid Amount'],
    ['remaining_due', 'Còn phải trả', 'Remaining Due'],
    ['waiting_confirmation', 'Chờ xác nhận', 'Waiting Confirmation'],
    ['pending_amount', 'Số tiền chờ xác nhận', 'Pending Amount'],
    ['invoice_table', 'Bảng hóa đơn', 'Invoice Table'],
    ['invoice_code', 'Mã hóa đơn', 'Invoice Code'],
    ['submit_bank_transfer', 'Gửi minh chứng chuyển khoản', 'Submit Bank Transfer'],
    ['no_registration_submitted', 'Bạn chưa gửi đơn đăng ký phòng nào.', 'No room registration has been submitted yet.'],
    ['total_rooms_label', 'Tổng số phòng', 'Total Rooms'],
    ['create_room', 'Tạo phòng', 'Create Room'],
    ['room_number', 'Số phòng', 'Room Number'],
    ['price_per_month_admin', 'Giá mỗi tháng', 'Price Per Month'],
    ['select_building', '-- Chọn tòa nhà --', '-- Select building --'],
    ['select_type', '-- Chọn loại phòng --', '-- Select type --'],
    ['select_gender_admin', '-- Chọn giới tính --', '-- Select gender --'],
    ['room_list_admin', 'Danh sách phòng', 'Room List'],
    ['occupancy', 'Số người ở', 'Occupancy'],
    ['total_students_label', 'Tổng sinh viên', 'Total Students'],
    ['missing_profile', 'Thiếu hồ sơ', 'Missing Profile'],
    ['create_student_profile', 'Tạo hồ sơ sinh viên', 'Create Student Profile'],
    ['user_student', 'Tài khoản sinh viên', 'User Student'],
    ['select_student_user', '-- Chọn tài khoản sinh viên --', '-- Select Student User --'],
    ['student_list', 'Danh sách sinh viên', 'Student List'],
    ['account', 'Tài khoản', 'Account'],
    ['priority', 'Mức ưu tiên', 'Priority'],
    ['total_users_label', 'Tổng tài khoản', 'Total Users'],
    ['admins_label', 'Quản trị viên', 'Admins'],
    ['managers_label', 'Quản lý', 'Managers'],
    ['create_user_account', 'Tạo tài khoản người dùng', 'Create User Account'],
    ['phone_label', 'Số điện thoại', 'Phone'],
    ['select_role', '-- Chọn vai trò --', '-- Select role --'],
    ['create_user_button', 'Tạo tài khoản', 'Create User'],
    ['user_list', 'Danh sách tài khoản', 'User List'],
    ['student_profile_col', 'Hồ sơ sinh viên', 'Student Profile'],
    ['contact', 'Liên hệ', 'Contact'],
    ['actions', 'Thao tác', 'Actions'],
    ['no_profile', 'Chưa có hồ sơ', 'No profile'],
    ['deactivate', 'Ngừng kích hoạt', 'Deactivate'],
    ['activate', 'Kích hoạt', 'Activate'],
    ['reset_password_en', 'Đặt lại mật khẩu', 'Reset Password'],
    ['occupancy_by_building', 'Tỷ lệ lấp đầy theo tòa', 'Occupancy By Building'],
    ['total_capacity', 'Tổng sức chứa', 'Total Capacity'],
    ['active_occupancy', 'Số chỗ đang sử dụng', 'Active Occupancy'],
    ['occupancy_rate', 'Tỷ lệ lấp đầy', 'Occupancy Rate'],
    ['invoice_summary', 'Tổng hợp hóa đơn', 'Invoice Summary'],
    ['invoice_count', 'Số hóa đơn', 'Invoice Count'],
    ['remaining', 'Còn lại', 'Remaining'],
    ['top_violation_students', 'Sinh viên vi phạm nhiều nhất', 'Top Violation Students'],
    ['violation_history', 'Lịch sử vi phạm', 'Violation History'],
    ['violation_date_col', 'Ngày vi phạm', 'Violation Date'],
    ['description_col', 'Mô tả', 'Description'],
    ['created_by', 'Người tạo', 'Created By'],
    ['total_violation_points', 'Tổng điểm vi phạm:', 'Total Violation Points:'],
    ['normal_level', 'Bình thường', 'Normal'],
    ['warning_level_en', 'Cảnh báo', 'Warning'],
    ['points_lower', 'điểm', 'points']
];

const TEXT_LOOKUP = new Map();
const I18N_BY_KEY = new Map();
const EMBEDDED_TEXT_LABELS = [
    { vi: 'Tòa A', en: 'Building A' },
    { vi: 'Tòa B', en: 'Building B' },
    { vi: 'Tòa C', en: 'Building C' },
    { vi: 'Học kỳ Xuân 2026', en: 'Spring 2026' },
    { vi: 'Công nghệ thông tin', en: 'Information Technology' },
    { vi: 'Kinh doanh quốc tế', en: 'International Business' },
    { vi: 'Kỹ thuật phần mềm', en: 'Software Engineering' },
    { vi: 'Khoa học dữ liệu', en: 'Data Science' },
    { vi: 'An toàn thông tin', en: 'Cyber Security' },
    { vi: 'Trí tuệ nhân tạo', en: 'Artificial Intelligence' },
    { vi: 'Quản trị kinh doanh', en: 'Business Administration' },
    { vi: 'Marketing', en: 'Marketing' },
    { vi: 'Tiêu chuẩn', en: 'Standard' },
    { vi: 'Cao cấp', en: 'Premium' },
    { vi: 'Nam', en: 'Male' },
    { vi: 'Nữ', en: 'Female' },
    { vi: 'Linh hoạt', en: 'Mixed' },
    { vi: 'Đang hoạt động', en: 'Active' },
    { vi: 'Ngừng hoạt động', en: 'Inactive' },
    { vi: 'Còn trống', en: 'Available' },
    { vi: 'Bảo trì', en: 'Maintenance' },
    { vi: 'Đã thanh toán', en: 'Paid' },
    { vi: 'Chưa thanh toán', en: 'Unpaid' },
    { vi: 'Thanh toán một phần', en: 'Partially paid' },
    { vi: ' - Phòng ', en: ' - Room ' },
    { vi: ' VND/tháng', en: ' VND/month' }
];

TEXT_LABELS.forEach(([key, vi, en]) => {
    I18N_BY_KEY.set(key, { key, vi, en });
    TEXT_LOOKUP.set(normalizeText(vi), { key, vi, en });
    TEXT_LOOKUP.set(normalizeText(en), { key, vi, en });
});

Object.entries(VALUE_LABELS).forEach(([key, labels]) => {
    TEXT_LOOKUP.set(normalizeText(key), { key, ...labels });
    TEXT_LOOKUP.set(normalizeText(labels.vi), { key, ...labels });
    TEXT_LOOKUP.set(normalizeText(labels.en), { key, ...labels });
});

document.addEventListener('DOMContentLoaded', () => {
    bindLanguageSwitch();
    bindRoomSuggestionFetch();
    applyLanguage(getCurrentLang());
});

function bindRoomSuggestionFetch() {
    document.querySelectorAll('[data-fetch-room-suggestions]').forEach((button) => {
        button.addEventListener('click', async () => {
            const registrationId = button.dataset.registrationId;
            const select = document.getElementById(button.dataset.targetSelect);
            const tableBody = document.getElementById(button.dataset.targetTable);

            if (!registrationId || !select || !tableBody) {
                return;
            }

            const originalText = button.textContent;
            button.disabled = true;
            button.textContent = translateKnownText('Đang tải...', getCurrentLang());

            try {
                const response = await fetch(`${window.APP_BASE_URL}/index.php?route=api/registrations/suggestions&id=${registrationId}`, {
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                const payload = await response.json();

                if (!payload.success) {
                    alert(payload.message || 'Không thể tải gợi ý phòng.');
                    return;
                }

                renderRoomSuggestions(payload.data || [], select, tableBody);
            } catch (error) {
                alert('Không thể kết nối API gợi ý phòng.');
            } finally {
                button.disabled = false;
                button.textContent = originalText;
            }
        });
    });
}

function renderRoomSuggestions(rooms, select, tableBody) {
    const lang = getCurrentLang();
    select.innerHTML = `<option value="">-- ${translateKnownText('Chọn phòng', lang)} --</option>`;
    tableBody.innerHTML = '';

    if (!rooms.length) {
        const row = document.createElement('tr');
        row.innerHTML = `<td colspan="8">${translateKnownText('Không có phòng phù hợp.', lang)}</td>`;
        tableBody.appendChild(row);
        return;
    }

    rooms.forEach((room) => {
        const option = document.createElement('option');
        option.value = room.room_id;
        const roomWord = lang === 'en' ? 'Room' : 'Phòng';
        const bedsText = lang === 'en' ? `${room.available_beds} beds left` : `Còn ${room.available_beds} giường`;
        const monthText = lang === 'en' ? 'month' : 'tháng';
        option.textContent = `${room.building_name} - ${roomWord} ${room.room_number} | ${label(room.room_type, lang)} | ${label(room.gender_type, lang)} | ${bedsText} | ${formatVnd(room.price_per_month)}/${monthText}`;
        select.appendChild(option);

        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${escapeHtml(room.building_name)}</td>
            <td>${escapeHtml(room.room_number)}</td>
            <td>${label(room.room_type, lang)}</td>
            <td>${label(room.gender_type, lang)}</td>
            <td>${escapeHtml(room.capacity)}</td>
            <td>${escapeHtml(room.current_occupancy)}</td>
            <td><span class="badge success">${escapeHtml(room.available_beds)}</span></td>
            <td>${formatVnd(room.price_per_month)}</td>
        `;
        tableBody.appendChild(row);
    });
}

function formatVnd(value) {
    return `${Number(value || 0).toLocaleString('vi-VN')} VND`;
}

function escapeHtml(value) {
    return String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}

function label(value, lang = getCurrentLang()) {
    return VALUE_LABELS[value]?.[lang] || escapeHtml(value);
}

function bindLanguageSwitch() {
    localStorage.setItem('dm_lang', DEFAULT_LANG);

    document.querySelectorAll('.language-box, .auth-language-box').forEach((element) => {
        element.remove();
    });
}

function getCurrentLang() {
    return DEFAULT_LANG;
}

function normalizeText(value) {
    return String(value ?? '').replace(/\s+/g, ' ').trim().toLocaleLowerCase('vi-VN');
}

function translateKnownText(value, lang) {
    const normalized = normalizeText(value);
    const entry = TEXT_LOOKUP.get(normalized);

    if (entry) {
        return entry[lang];
    }

    const original = String(value ?? '');

    if (lang === 'en' && /^ví dụ:/i.test(normalized)) {
        return original.replace(/^ví dụ:/i, 'Example:');
    }

    if (lang === 'vi' && /^example:/i.test(normalized)) {
        return original.replace(/^example:/i, 'Ví dụ:');
    }

    if (lang === 'en' && /^\d+\s+điểm$/i.test(normalized)) {
        return original.replace(/điểm/i, 'points');
    }

    if (lang === 'vi' && /^\d+\s+points$/i.test(normalized)) {
        return original.replace(/points/i, 'điểm');
    }

    let embeddedTranslated = original;
    EMBEDDED_TEXT_LABELS.forEach((entry) => {
        const source = lang === 'vi' ? entry.en : entry.vi;
        const target = lang === 'vi' ? entry.vi : entry.en;
        embeddedTranslated = embeddedTranslated.split(source).join(target);
    });

    if (lang === 'en') {
        embeddedTranslated = embeddedTranslated.replace(/-\s*Phòng\s+/g, '- Room ');
    }

    if (lang === 'vi') {
        embeddedTranslated = embeddedTranslated.replace(/-\s*Room\s+/g, '- Phòng ');
    }

    if (embeddedTranslated !== original) {
        return embeddedTranslated;
    }

    return value;
}

function applyLanguage(lang) {
    lang = DEFAULT_LANG;
    document.documentElement.lang = lang;

    document.querySelectorAll('[data-i18n]').forEach((element) => {
        const entry = I18N_BY_KEY.get(element.dataset.i18n);

        if (entry) {
            element.textContent = entry[lang];
        }
    });

    document.querySelectorAll('[data-i18n-placeholder]').forEach((element) => {
        const entry = I18N_BY_KEY.get(element.dataset.i18nPlaceholder);

        if (entry) {
            element.setAttribute('placeholder', entry[lang]);
        }
    });

    document.querySelectorAll('[data-i18n-aria-label]').forEach((element) => {
        const entry = I18N_BY_KEY.get(element.dataset.i18nAriaLabel);

        if (entry) {
            element.setAttribute('aria-label', entry[lang]);
        }
    });

    const pageTitle = TEXT_LOOKUP.get(normalizeText(document.title));
    if (pageTitle) {
        document.title = pageTitle[lang];
    }

    const skipTags = new Set(['SCRIPT', 'STYLE', 'TEXTAREA', 'INPUT']);
    const walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT);
    const nodes = [];

    while (walker.nextNode()) {
        const node = walker.currentNode;
        if (!node.parentElement || skipTags.has(node.parentElement.tagName)) {
            continue;
        }

        if (node.parentElement.closest('[data-i18n]')) {
            continue;
        }

        nodes.push(node);
    }

    nodes.forEach((node) => {
        const raw = node.nodeValue;
        const trimmed = normalizeText(raw);

        if (!trimmed) {
            return;
        }

        const entry = TEXT_LOOKUP.get(trimmed);
        if (entry) {
            node.nodeValue = raw.replace(raw.trim(), entry[lang]);
            return;
        }

        const embeddedTranslation = translateKnownText(raw.trim(), lang);
        if (embeddedTranslation !== raw.trim()) {
            node.nodeValue = raw.replace(raw.trim(), embeddedTranslation);
        }
    });

    document.querySelectorAll('[placeholder]').forEach((element) => {
        const translated = translateKnownText(element.getAttribute('placeholder'), lang);
        element.setAttribute('placeholder', translated);
    });
}

window.dmApplyLanguage = applyLanguage;
window.dmCurrentLang = getCurrentLang;
window.dmTranslate = translateKnownText;
