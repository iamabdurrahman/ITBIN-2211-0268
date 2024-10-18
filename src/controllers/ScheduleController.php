<?php
require_once __DIR__ . '/../models/Schedule.php';
require_once __DIR__ . '/../utils/Response.php';
require_once __DIR__ . '/../utils/Session.php';
Session::start();

class ScheduleController
{
    public function createSchedule($data)
    {
        try {
            $schedule = new Schedule();
            $schedule->setDestination($data['destination']);
            $schedule->setFlightId($data['flight_id']);
            $schedule->setDepartureTime($data['departure_time']);
            $result = $schedule->create();
            if ($result) {
                Response::success("Schedule created successfully!");
            } else {
                Response::error("Failed to create schedule.");
            }
        } catch (Exception $e) {
            Response::error("Error: " . $e->getMessage());
        }
    }

    public function getAllSchedules()
    {
        try {
            $schedule = new Schedule();
            $schedules = $schedule->getAll();
            Response::success("Schedules retrieved successfully.", $schedules);
        } catch (Exception $e) {
            Response::error("Error: " . $e->getMessage());
        }
    }

    public function getScheduleById($id)
    {
        try {
            $schedule = new Schedule();
            $schedule = $schedule->getById($id);
            Response::success("Schedule retrieved successfully.", $schedule);
        } catch (Exception $e) {
            Response::error("Error: " . $e->getMessage());
        }
    }


    public function getAllByFlightId($flight_id)
    {
        try {
            $schedule = new Schedule();
            $schedules = $schedule->getByFlightId($flight_id);
            Response::success("Schedules retrieved successfully.", $schedules);
        } catch (Exception $e) {
            Response::error("Error: " . $e->getMessage());
        }
    }

    public function updateSchedule($id, $data)
    {
        try {
            $schedule = new Schedule();
            $schedule->setId($id);
            $schedule->setDestination($data['destination']);
            $schedule->setFlightId($data['flight_id']);
            $schedule->setDepartureTime($data['departure_time']);
            $result = $schedule->update();

            if ($result) {
                Response::success("Schedule updated successfully!");
            } else {
                Response::error("Failed to update schedule.");
            }
        } catch (Exception $e) {
            Response::error("Error: " . $e->getMessage());
        }
    }

    public function deleteSchedule($id)
    {
        try {
            $schedule = new Schedule();
            $schedule->setId($id);
            $result = $schedule->delete();

            if ($result) {
                Response::success("Schedule deleted successfully!");
            } else {
                Response::error("Failed to delete schedule.");
            }
        } catch (Exception $e) {
            Response::error("Error: " . $e->getMessage());
        }
    }
}

if (isset($_GET['action'])) {
    $controller = new ScheduleController();
    $action = $_GET['action'];
    $data = $_POST;
    switch ($action) {
        case 'getSchedules':
            if (isset($_GET['flight_id'])) {
                $controller->getAllByFlightId($_GET['flight_id']);
            } else {
                $controller->getAllSchedules();
            }
            break;
        case 'getById':
            $controller->getScheduleById($_GET['id']);
            break;
        case  'save':
            $controller->createSchedule($data);
            break;

        case 'update':
            $id = $_POST['id'];
            $controller->updateSchedule($id, $data);
            break;

        case 'delete':
            $id = $_POST['id'];
            $controller->deleteSchedule($id);
            break;

    }
}
?>