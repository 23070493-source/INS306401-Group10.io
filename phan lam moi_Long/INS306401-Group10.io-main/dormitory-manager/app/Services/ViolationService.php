<?php

class ViolationService
{
    public function __construct(private PDO $db)
    {
    }

    public function totalPointsForStudent(int $studentId): int
    {
        $stmt = $this->db->prepare("
            SELECT COALESCE(SUM(penalty_points), 0)
            FROM violation_records
            WHERE student_id = :student_id
        ");
        $stmt->execute(['student_id' => $studentId]);

        return (int) $stmt->fetchColumn();
    }

    public function warningLevel(int $points): string
    {
        if ($points >= 15) {
            return 'critical';
        }

        if ($points >= 10) {
            return 'serious';
        }

        if ($points >= 5) {
            return 'warning';
        }

        return 'normal';
    }

    public function createRecord(
        int $studentId,
        string $violationType,
        string $description,
        int $penaltyPoints,
        string $violationDate,
        int $recordedBy
    ): int {
        $stmt = $this->db->prepare("
            SELECT
                c.id AS contract_id,
                c.room_id
            FROM contracts c
            WHERE c.student_id = :student_id
              AND c.status = 'active'
            ORDER BY c.id DESC
            LIMIT 1
        ");
        $stmt->execute(['student_id' => $studentId]);
        $contract = $stmt->fetch() ?: ['contract_id' => null, 'room_id' => null];

        $stmt = $this->db->prepare("
            INSERT INTO violation_records (
                student_id,
                contract_id,
                room_id,
                violation_date,
                violation_type,
                description,
                penalty_points,
                recorded_by,
                status
            )
            VALUES (
                :student_id,
                :contract_id,
                :room_id,
                :violation_date,
                :violation_type,
                :description,
                :penalty_points,
                :recorded_by,
                'recorded'
            )
        ");
        $stmt->execute([
            'student_id' => $studentId,
            'contract_id' => $contract['contract_id'],
            'room_id' => $contract['room_id'],
            'violation_date' => $violationDate,
            'violation_type' => $violationType,
            'description' => $description,
            'penalty_points' => $penaltyPoints,
            'recorded_by' => $recordedBy
        ]);

        return (int) $this->db->lastInsertId();
    }
}
