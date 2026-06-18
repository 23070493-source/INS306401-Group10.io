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
    none: { vi: 'Không', en: 'None' }
};

const TEXT_LABELS = [
    ['language', 'Ngôn ngữ', 'Language'],
    ['vietnamese', 'Tiếng Việt', 'Vietnamese'],
    ['english', 'English', 'English'],
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
    ['maintenance_title_placeholder', 'Ví dụ: Bóng đèn phòng bị hỏng', 'Example: The room light is broken'],
    ['maintenance_desc_placeholder', 'Mô tả chi tiết vấn đề cần sửa chữa', 'Describe the issue that needs repair'],
    ['example_prefix', 'Ví dụ:', 'Example:']
];

const TEXT_LOOKUP = new Map();
const I18N_BY_KEY = new Map();

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
    const select = document.getElementById('language-select');

    if (!select) {
        return;
    }

    select.value = getCurrentLang();
    select.addEventListener('change', () => {
        const lang = select.value === 'en' ? 'en' : 'vi';
        localStorage.setItem('dm_lang', lang);
        applyLanguage(lang);
    });
}

function getCurrentLang() {
    return localStorage.getItem('dm_lang') === 'en' ? 'en' : DEFAULT_LANG;
}

function normalizeText(value) {
    return String(value ?? '').replace(/\s+/g, ' ').trim();
}

function translateKnownText(value, lang) {
    const normalized = normalizeText(value);
    const entry = TEXT_LOOKUP.get(normalized);

    if (entry) {
        return entry[lang];
    }

    if (lang === 'en' && normalized.startsWith('Ví dụ:')) {
        return normalized.replace(/^Ví dụ:/, 'Example:');
    }

    if (lang === 'vi' && normalized.startsWith('Example:')) {
        return normalized.replace(/^Example:/, 'Ví dụ:');
    }

    return value;
}

function applyLanguage(lang) {
    document.documentElement.lang = lang;

    const select = document.getElementById('language-select');
    if (select) {
        select.value = lang;
    }

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
        }
    });

    document.querySelectorAll('[placeholder]').forEach((element) => {
        const translated = translateKnownText(element.getAttribute('placeholder'), lang);
        element.setAttribute('placeholder', translated);
    });
}

window.dmApplyLanguage = applyLanguage;
