<h1>Yêu cầu sửa chữa</h1>
<p>Gửi yêu cầu sửa chữa, đính kèm ảnh minh chứng và theo dõi trạng thái xử lý.</p>

<?php if (!$student): ?>
    <div class="alert error">Không tìm thấy hồ sơ sinh viên.</div>

<?php else: ?>

    <div class="profile-box">
        <h2><?= htmlspecialchars($student['full_name']) ?></h2>
        <p><strong>Mã sinh viên:</strong> <?= htmlspecialchars($student['student_code']) ?></p>

        <?php if ($contract): ?>
            <p>
                <strong>Phòng hiện tại:</strong>
                <?= htmlspecialchars($contract['building_name']) ?>
                -
                <?= htmlspecialchars($contract['room_number']) ?>
            </p>
        <?php else: ?>
            <p><strong>Phòng hiện tại:</strong> Chưa có hợp đồng active</p>
        <?php endif; ?>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert error">
            <?php foreach ($errors as $error): ?>
                <div><?= htmlspecialchars($error) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if ($contract): ?>
        <form 
            method="POST" 
            action="<?= BASE_URL ?>/index.php?route=student/maintenance-store" 
            class="form-card wide-form"
            enctype="multipart/form-data"
        >
            <h2>Tạo yêu cầu sửa chữa</h2>

            <label>Danh mục</label>
            <select name="category" required>
                <option value="">-- Chọn danh mục --</option>
                <option value="electricity" <?= ($old['category'] ?? '') === 'electricity' ? 'selected' : '' ?>>Điện</option>
                <option value="water" <?= ($old['category'] ?? '') === 'water' ? 'selected' : '' ?>>Nước</option>
                <option value="furniture" <?= ($old['category'] ?? '') === 'furniture' ? 'selected' : '' ?>>Nội thất</option>
                <option value="internet" <?= ($old['category'] ?? '') === 'internet' ? 'selected' : '' ?>>Internet</option>
                <option value="cleaning" <?= ($old['category'] ?? '') === 'cleaning' ? 'selected' : '' ?>>Vệ sinh</option>
                <option value="other" <?= ($old['category'] ?? '') === 'other' ? 'selected' : '' ?>>Khác</option>
            </select>

            <label>Mức ưu tiên</label>
            <select name="priority" required>
                <option value="low" <?= ($old['priority'] ?? '') === 'low' ? 'selected' : '' ?>>Thấp</option>
                <option value="medium" <?= ($old['priority'] ?? 'medium') === 'medium' ? 'selected' : '' ?>>Trung bình</option>
                <option value="high" <?= ($old['priority'] ?? '') === 'high' ? 'selected' : '' ?>>Cao</option>
                <option value="urgent" <?= ($old['priority'] ?? '') === 'urgent' ? 'selected' : '' ?>>Khẩn cấp</option>
            </select>

            <label>Tiêu đề</label>
            <input
                type="text"
                name="title"
                required
                placeholder="Ví dụ: Bóng đèn phòng bị hỏng"
                value="<?= htmlspecialchars($old['title'] ?? '') ?>"
            >

            <label>Mô tả</label>
            <textarea
                name="description"
                rows="5"
                required
                placeholder="Mô tả chi tiết vấn đề cần sửa chữa"
            ><?= htmlspecialchars($old['description'] ?? '') ?></textarea>

            <label>Ảnh minh chứng</label>
            <input
                type="file"
                name="evidence_image"
                accept="image/jpeg,image/png,image/webp"
            >
            <small>Cho phép JPG, PNG, WEBP. Tối đa 5MB. Không bắt buộc.</small>

            <button type="submit">Gửi yêu cầu</button>
        </form>
    <?php else: ?>
        <div class="alert error">
            Bạn cần có hợp đồng active trước khi gửi yêu cầu sửa chữa.
        </div>
    <?php endif; ?>

    <h2>Yêu cầu sửa chữa của tôi</h2>

    <?php if (empty($requests)): ?>
        <div class="alert error">Bạn chưa có yêu cầu sửa chữa nào.</div>
    <?php else: ?>
        <table>
            <thead>
            <tr>
                <th>ID</th>
                <th>Phòng</th>
                <th>Danh mục</th>
                <th>Sự cố</th>
                <th>Minh chứng</th>
                <th>Ưu tiên</th>
                <th>Trạng thái</th>
                <th>Ngày yêu cầu</th>
                <th>Người xử lý</th>
                <th>Ghi chú xử lý</th>
            </tr>
            </thead>

            <tbody>
            <?php foreach ($requests as $request): ?>
                <?php
                $imagePath = trim($request['evidence_image'] ?? '');
                $imageUrl = $imagePath !== '' ? BASE_URL . '/' . ltrim($imagePath, '/') : '';
                ?>

                <tr>
                    <td><?= htmlspecialchars($request['id']) ?></td>

                    <td>
                        <?= htmlspecialchars($request['building_name'] ?? '-') ?>
                        -
                        <?= htmlspecialchars($request['room_number'] ?? '-') ?>
                    </td>

                    <td><?= htmlspecialchars($request['category'] ?? '-') ?></td>

                    <td>
                        <strong><?= htmlspecialchars($request['title'] ?? '-') ?></strong>
                        <br>
                        <small><?= htmlspecialchars($request['description'] ?? '-') ?></small>
                    </td>

                    <td>
                        <?php if ($imageUrl !== ''): ?>
                            <a href="<?= htmlspecialchars($imageUrl) ?>" target="_blank">
                                <img
                                    src="<?= htmlspecialchars($imageUrl) ?>"
                                    alt="Ảnh minh chứng"
                                    class="evidence-thumb"
                                >
                            </a>
                            <br>
                            <small>
                                <a href="<?= htmlspecialchars($imageUrl) ?>" target="_blank">
                                    Xem ảnh
                                </a>
                            </small>
                        <?php else: ?>
                            <span>-</span>
                        <?php endif; ?>
                    </td>

                    <td>
                        <span class="badge priority-<?= htmlspecialchars($request['priority'] ?? 'medium') ?>">
                            <?= htmlspecialchars($request['priority'] ?? 'medium') ?>
                        </span>
                    </td>

                    <td>
                        <span class="badge <?= htmlspecialchars($request['status']) ?>">
                            <?= htmlspecialchars($request['status']) ?>
                        </span>
                    </td>

                    <td><?= htmlspecialchars($request['request_date'] ?? '-') ?></td>

                    <td>
                        <?= htmlspecialchars($request['processed_by_username'] ?? '-') ?>
                        <?php if (!empty($request['processed_at'])): ?>
                            <br>
                            <small><?= htmlspecialchars($request['processed_at']) ?></small>
                        <?php endif; ?>
                    </td>

                    <td><?= htmlspecialchars($request['resolution_note'] ?? '-') ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

<?php endif; ?>
