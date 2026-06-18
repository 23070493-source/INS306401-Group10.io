<?php

class RoomAllocationService
{
    public function __construct(private PDO $db)
    {
    }

    public function suggestForRegistration(int $registrationId): array
    {
        $stmt = $this->db->prepare("
            SELECT
                rr.*,
                s.gender,
                s.priority_type
            FROM room_registrations rr
            JOIN students s ON s.id = rr.student_id
            WHERE rr.id = :id
            LIMIT 1
        ");
        $stmt->execute(['id' => $registrationId]);
        $registration = $stmt->fetch();

        if (!$registration) {
            return [];
        }

        $params = [
            'gender_score' => $registration['gender'],
            'gender_filter' => $registration['gender'],
            'desired_room_type_score' => $registration['desired_room_type'],
            'desired_room_type_filter' => $registration['desired_room_type']
        ];

        $buildingSql = '';
        if (!empty($registration['desired_building_id'])) {
            $buildingSql = 'AND r.building_id = :desired_building_id';
            $params['desired_building_id'] = $registration['desired_building_id'];
        }

        $stmt = $this->db->prepare("
            SELECT
                r.id AS room_id,
                b.building_name,
                r.room_number,
                r.room_type,
                r.gender_type,
                r.capacity,
                COUNT(c.id) AS current_occupancy,
                r.capacity - COUNT(c.id) AS available_beds,
                r.price_per_month,
                (
                    CASE WHEN r.room_type = :desired_room_type_score THEN 30 ELSE 0 END +
                    CASE WHEN r.gender_type = :gender_score THEN 30 ELSE 0 END +
                    CASE WHEN r.gender_type = 'mixed' THEN 10 ELSE 0 END +
                    CASE WHEN COUNT(c.id) = 0 THEN 5 ELSE 0 END
                ) AS match_score
            FROM rooms r
            JOIN buildings b ON b.id = r.building_id
            LEFT JOIN contracts c ON c.room_id = r.id AND c.status = 'active'
            WHERE r.status = 'available'
              AND r.room_type = :desired_room_type_filter
              AND r.gender_type IN (:gender_filter, 'mixed')
              $buildingSql
            GROUP BY
                r.id,
                b.building_name,
                r.room_number,
                r.room_type,
                r.gender_type,
                r.capacity,
                r.price_per_month
            HAVING current_occupancy < r.capacity
            ORDER BY match_score DESC, available_beds DESC, r.price_per_month ASC
        ");
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function availableRooms(): array
    {
        return $this->db->query("
            SELECT
                r.id AS room_id,
                b.building_name,
                r.room_number,
                r.room_type,
                r.gender_type,
                r.capacity,
                COUNT(c.id) AS current_occupancy,
                r.capacity - COUNT(c.id) AS available_beds,
                r.price_per_month,
                r.status
            FROM rooms r
            JOIN buildings b ON b.id = r.building_id
            LEFT JOIN contracts c ON c.room_id = r.id AND c.status = 'active'
            WHERE r.status = 'available'
            GROUP BY
                r.id,
                b.building_name,
                r.room_number,
                r.room_type,
                r.gender_type,
                r.capacity,
                r.price_per_month,
                r.status
            HAVING current_occupancy < r.capacity
            ORDER BY b.building_name, r.room_number
        ")->fetchAll();
    }
}
