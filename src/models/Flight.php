<?php
require_once __DIR__ . '/../../config/database.php';

class Flight
{
    private $id;
    private $flight_number;
    private $price;
    private $seats;

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setFlightNumber($flight_number)
    {
        $this->flight_number = $flight_number;
    }

    public function setPrice($price)
    {
        $this->price = $price;
    }

    public function setSeats($seats)
    {
        $this->seats = $seats;
    }

    public function create()
    {
        global $conn;
        try {
            $stmt = $conn->prepare("INSERT INTO flights (flight_number, price, seats) VALUES (?, ?, ?)");
            return $stmt->execute([$this->flight_number, $this->price, $this->seats]);
        } catch (Exception $e) {
            throw new Exception("Database error: " . $e->getMessage());
        }
    }

    public function getAll()
    {
        global $conn;
        try {
            $stmt = $conn->query("SELECT * FROM flights");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Database error: " . $e->getMessage());
        }
    }

    public function getById($id)
    {
        global $conn;
        try {
            $stmt = $conn->prepare("SELECT * FROM flights WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Database error: " . $e->getMessage());
        }
    }

    public function update()
    {
        global $conn;
        try {
            $stmt = $conn->prepare("UPDATE flights SET flight_number = ?, price = ?, seats = ? WHERE id = ?");
            return $stmt->execute([$this->flight_number, $this->price, $this->seats, $this->id]);
        } catch (Exception $e) {
            throw new Exception("Database error: " . $e->getMessage());
        }
    }

    public function delete()
    {
        global $conn;
        try {
            $stmt = $conn->prepare("DELETE FROM flights WHERE id = ?");
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