<?php
require_once __DIR__ . '/../../config/database.php';

class User
{
    private $id;
    private $email;
    private $password;
    private $name;
    private $role;
    private $balance;

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setPassword($password)
    {
//        $this->password = password_hash($password, PASSWORD_DEFAULT);
        $this->password = md5($password);
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setBalance($balance)
    {
        $this->balance = $balance;
    }

    public function setRole($role)
    {
        $this->role = $role;
    }

    public function create()
    {
        global $conn;
        try {
            $stmt = $conn->prepare("INSERT INTO users (email, password, name) VALUES (?, ?, ?)");
            return $stmt->execute([$this->email, $this->password, $this->name]);
        } catch (Exception $e) {
            throw new Exception("Database error: " . $e->getMessage());
        }
    }

    public function login($email, $password)
    {
        global $conn;
        try {
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && $user['password'] == md5($password)) {
                return ['success' => true, 'user' => $user];
            }
            return ['success' => false, 'message' => 'Invalid email or password.'];
        } catch (Exception $e) {
            throw new Exception("Database error: " . $e->getMessage());
        }
    }

    public function getAll()
    {
        global $conn;
        try {
            $stmt = $conn->query("SELECT * FROM users WHERE role != 'admin'");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Database error: " . $e->getMessage());
        }
    }

    public function getById()
    {
        global $conn;
        try {
            $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$this->id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Database error: " . $e->getMessage());
        }
    }

    public function delete()
    {
        global $conn;
        try {
            $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
            return $stmt->execute([$this->id]);
        } catch (Exception $e) {
            throw new Exception("Database error: " . $e->getMessage());
        }
    }

    public function updatePassword()
    {
        global $conn;
        try {
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            return $stmt->execute([$this->password, $this->id]);
        } catch (Exception $e) {
            throw new Exception("Database error: " . $e->getMessage());
        }
    }

    public function update()
    {
        global $conn;
        try {
            $stmt = $conn->prepare("UPDATE users SET email = ?, name = ?, role = ? WHERE id = ?");
            return $stmt->execute([$this->email, $this->name, $this->role, $this->id]);
        } catch (Exception $e) {
            throw new Exception("Database error: " . $e->getMessage());
        }
    }
    public function getBalanceById($userId)
    {
        global $conn;
        $stmt = $conn->prepare("SELECT balance FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchColumn();
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