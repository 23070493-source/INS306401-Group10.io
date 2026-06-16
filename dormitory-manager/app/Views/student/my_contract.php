<h1>My Contract</h1>
<p>Xem thông tin hợp đồng KTX của bạn.</p>

<?php if (!$student): ?>
    <div class="alert error">Không tìm thấy hồ sơ sinh viên.</div>

<?php elseif (empty($contracts)): ?>
    <div class="alert error">
        Bạn chưa có hợp đồng nào. Hãy đăng ký phòng và chờ Manager duyệt.
    </div>

    <div class="page-actions">
        <a class="btn-link" href="<?= BASE_URL ?>/index.php?route=student/register-room">
            Register Room
        </a>
    </div>

<?php else: ?>

    <div class="profile-box">
        <h2><?= htmlspecialchars($student['full_name']) ?></h2>
        <p><strong>Mã sinh viên:</strong> <?= htmlspecialchars($student['student_code']) ?></p>
        <p><strong>Giới tính:</strong> <?= htmlspecialchars($student['gender']) ?></p>
        <p><strong>Khoa:</strong> <?= htmlspecialchars($student['faculty'] ?? '-') ?></p>
        <p><strong>Chương trình:</strong> <?= htmlspecialchars($student['program'] ?? '-') ?></p>
    </div>

    <?php foreach ($contracts as $contract): ?>
        <div class="contract-card">
            <div class="contract-header">
                <div>
                    <h2>Contract <?= htmlspecialchars($contract['contract_code']) ?></h2>
                    <p>
                        <span class="badge <?= htmlspecialchars($contract['status']) ?>">
                            <?= htmlspecialchars($contract['status']) ?>
                        </span>
                    </p>
                </div>

                <div class="contract-money">
                    <strong><?= number_format($contract['monthly_price']) ?> VND</strong>
                    <span>/ month</span>
                </div>
            </div>

            <div class="detail-grid">
                <div>
                    <h3>Room Information</h3>
                    <p><strong>Building:</strong> <?= htmlspecialchars($contract['building_name']) ?></p>
                    <p><strong>Room:</strong> <?= htmlspecialchars($contract['room_number']) ?></p>
                    <p><strong>Room Type:</strong> <?= htmlspecialchars($contract['room_type']) ?></p>
                    <p><strong>Gender Type:</strong> <?= htmlspecialchars($contract['gender_type']) ?></p>
                    <p><strong>Capacity:</strong> <?= htmlspecialchars($contract['capacity']) ?></p>
                </div>

                <div>
                    <h3>Contract Information</h3>
                    <p>
                        <strong>Semester:</strong>
                        <?= htmlspecialchars($contract['semester_name']) ?>
                        -
                        <?= htmlspecialchars($contract['academic_year']) ?>
                    </p>
                    <p><strong>Start Date:</strong> <?= htmlspecialchars($contract['start_date']) ?></p>
                    <p><strong>End Date:</strong> <?= htmlspecialchars($contract['end_date']) ?></p>
                    <p><strong>Monthly Price:</strong> <?= number_format($contract['monthly_price']) ?> VND</p>
                    <p><strong>Deposit Amount:</strong> <?= number_format($contract['deposit_amount']) ?> VND</p>
                    <p><strong>Created By:</strong> <?= htmlspecialchars($contract['created_by_username'] ?? '-') ?></p>
                    <p><strong>Created At:</strong> <?= htmlspecialchars($contract['created_at']) ?></p>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

<?php endif; ?>