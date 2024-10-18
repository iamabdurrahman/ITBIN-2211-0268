<?php
require_once __DIR__ . '/../../config/database.php';

class Schedule
{
    private $id;
    private $destination;
    private $flight_id;
    private $departure_time;

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setDestination($destination)
    {
        $this->destination = $destination;
    }

    public function setFlightId($flight_id)
    {
        $this->flight_id = $flight_id;
    }

    public function setDepartureTime($departure_time)
    {
        $this->departure_time = $departure_time;
    }


    public function create()
    {
        global $conn;
        try {
            $stmt = $conn->prepare("INSERT INTO schedules (destination, flight_id, departure_time) VALUES (?, ?, ?)");
            return $stmt->execute([$this->destination, $this->flight_id, $this->departure_time]);
        } catch (Exception $e) {
            throw new Exception("Database error: " . $e->getMessage());
        }
    }

    public function getAll()
    {
        global $conn;
        try {
            $stmt = $conn->query("
            SELECT 
                schedules.*, 
                flights.flight_number, 
                flights.price, 
                flights.seats AS total_seats,
                (flights.seats - COALESCE(SUM(bookings.seats), 0)) AS available_seats
            FROM 
                schedules 
            JOIN 
                flights ON schedules.flight_id = flights.id 
            LEFT JOIN 
                bookings ON schedules.id = bookings.schedule_id AND bookings.status = 'confirmed'
            WHERE 
                schedules.departure_time > NOW()
            GROUP BY 
                schedules.id, flights.id
        ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Database error: " . $e->getMessage());
        }
    }

    public function getById($id)
    {
        global $conn;
        try {
            $stmt = $conn->prepare("SELECT schedules.*, flights.flight_number, schedules.flight_id FROM schedules JOIN flights ON schedules.flight_id = flights.id WHERE schedules.id = ? AND schedules.departure_time > NOW()");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Database error: " . $e->getMessage());
        }
    }

    public function getByFlightId($flight_id)
    {
        global $conn;
        try {
            $stmt = $conn->prepare("SELECT schedules.*, flights.flight_number FROM schedules JOIN flights ON schedules.flight_id = flights.id WHERE schedules.flight_id = ? AND schedules.departure_time > NOW()");
            $stmt->execute([$flight_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Database error: " . $e->getMessage());
        }
    }

    public function update()
    {
        global $conn;
        try {
            $stmt = $conn->prepare("UPDATE schedules SET destination = ?, flight_id = ?, departure_time = ? WHERE id = ?");
            return $stmt->execute([$this->destination, $this->flight_id, $this->departure_time, $this->id]);
        } catch (Exception $e) {
            throw new Exception("Database error: " . $e->getMessage());
        }
    }

    public function delete()
    {
        global $conn;
        try {
            $stmt = $conn->prepare("DELETE FROM schedules WHERE id = ?");
            return $stmt->execute([$this->id]);
        } catch (Exception $e) {
            throw new Exception("Database error: " . $e->getMessage());
        }
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