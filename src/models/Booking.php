<?php
require_once __DIR__ . '/../../config/database.php';

class Booking
{
    private $id;
    private $user_id;
    private $flight_id;
    private $schedule_id;
    private $seats;
    private $status;

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

    public function setFlightId($flight_id)
    {
        $this->flight_id = $flight_id;
    }

    public function setScheduleId($schedule_id)
    {
        $this->schedule_id = $schedule_id;
    }

    public function setSeats($seats)
    {
        $this->seats = $seats;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function create()
    {
        global $conn;
        try {
            $conn->beginTransaction();
            $stmt = $conn->prepare("INSERT INTO bookings (user_id, flight_id, schedule_id, seats, status) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$this->user_id, $this->flight_id, $this->schedule_id, $this->seats, $this->status]);


            $stmt = $conn->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
            $stmt->execute([$this->calculateBookingCost(), $this->user_id]);
            $conn->commit();

            $this->logAction("Create booking", $this->user_id);

            return true;

        } catch (Exception $e) {
            throw new Exception("Database error: " . $e->getMessage());
        }
    }

    public function getByUserId($user_id)
    {
        global $conn;
        try {
            $stmt = $conn->prepare("
            SELECT
                bookings.*,
                flights.flight_number,
                schedules.departure_time,
                schedules.destination
            FROM
                bookings
            JOIN
                schedules ON bookings.schedule_id = schedules.id
            JOIN
                flights ON bookings.flight_id = flights.id
            WHERE
                bookings.user_id = ?
        ");
            $stmt->execute([$user_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Database error: " . $e->getMessage());
        }
    }


    public function cancel()
    {
        global $conn;
        try {

            $conn->beginTransaction();

            $stmt = $conn->prepare("SELECT seats FROM bookings WHERE id = ?");
            $stmt->execute([$this->id]);
            $this->seats = $stmt->fetchColumn();

            $stmt = $conn->prepare("SELECT flight_id FROM bookings WHERE id = ?");
            $stmt->execute([$this->id]);
            $this->flight_id = $stmt->fetchColumn();

            $stmt = $conn->prepare("UPDATE bookings SET status = 'canceled' WHERE id = ?");
            $stmt->execute([$this->id]);

            $stmt = $conn->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
            $stmt->execute([$this->calculateBookingCost(), $this->user_id]);
            $conn->commit();

            $this->logAction("Updated booking", $this->user_id);

            return true;
        } catch (Exception $e) {
            $conn->rollBack();
            throw new Exception("Database error: " . $e->getMessage());
        }
    }

    public function updateById()
    {
        global $conn;
        try {
            $this->logAction("Updated booking", $this->user_id);
            $stmt = $conn->prepare("UPDATE bookings SET user_id = ?, flight_id = ?, schedule_id = ?, seats = ?, status = ? WHERE id = ?");
            return $stmt->execute([$this->user_id, $this->flight_id, $this->schedule_id, $this->seats, $this->status, $this->id]);
        } catch (Exception $e) {
            throw new Exception("Database error: " . $e->getMessage());
        }
    }

    public function getAll()
    {
        global $conn;
        try {
            $stmt = $conn->prepare("
            SELECT 
                bookings.*, 
                flights.flight_number, 
                schedules.departure_time, 
                schedules.destination,
                users.email
            FROM 
                bookings 
            JOIN 
                schedules ON bookings.schedule_id = schedules.id 
            JOIN 
                flights ON bookings.flight_id = flights.id
            JOIN
                users ON bookings.user_id = users.id
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Database error: " . $e->getMessage());
        }
    }

    private function calculateBookingCost()
    {
        global $conn;
        $stmt = $conn->prepare("SELECT price FROM flights WHERE id = ?");
        $stmt->execute([$this->flight_id]);
        $pricePerSeat = $stmt->fetchColumn();
        return $pricePerSeat * $this->seats;
    }

    private function logAction($action, $userId)
    {
        global $conn;
        try {
            $stmt = $conn->prepare("INSERT INTO logs (action, user_id) VALUES (?, ?)");
            $stmt->execute([$action, $userId]);
        } catch (Exception $e) {
            throw new Exception("Logging error: " . $e->getMessage());
        }
    }
}

?>
