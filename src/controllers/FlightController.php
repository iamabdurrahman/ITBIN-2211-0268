<?php
require_once __DIR__ . '/../models/Flight.php';
require_once __DIR__ . '/../utils/Response.php';
require_once __DIR__ . '/../utils/Session.php';
Session::start();

class FlightController
{
    public function createFlight($data)
    {
        try {
            $flight = new Flight();
            $flight->setFlightNumber($data['flight_number']);
            $flight->setPrice($data['price']);
            $flight->setSeats($data['seats']);
            $result = $flight->create();

            if ($result) {
                Response::success("Flight created successfully!");
            } else {
                Response::error("Failed to create flight.");
            }
        } catch (Exception $e) {
            Response::error("Error: " . $e->getMessage());
        }
    }

    public function getFlights()
    {
        try {
            $flight = new Flight();
            $flights = $flight->getAll();
            Response::success("Flights retrieved successfully.", $flights);
        } catch (Exception $e) {
            Response::error("Error: " . $e->getMessage());
        }
    }

    public function getFlightById($id)
    {
        try {
            $flight = new Flight();
            $flight = $flight->getById($id);
            Response::success("Flights retrieved successfully.", $flight);
        } catch (Exception $e)
        {
            Response::error("Error: " . $e->getMessage());
        }
    }

    public function updateFlight($id, $data)
    {
        try {
            $flight = new Flight();
            $flight->setId($id);
            $flight->setFlightNumber($data['flight_number']);
            $flight->setPrice($data['price']);
            $flight->setSeats($data['seats']);
            $result = $flight->update();

            if ($result) {
                Response::success("Flight updated successfully!");
            } else {
                Response::error("Failed to update flight.");
            }
        } catch (Exception $e) {
            Response::error("Error: " . $e->getMessage());
        }
    }

    public function deleteFlight($id)
    {
        try {
            $flight = new Flight();
            $flight->setId($id);
            $result = $flight->delete();

            if ($result) {
                Response::success("Flight deleted successfully!");
            } else {
                Response::error("Failed to delete flight.");
            }
        } catch (Exception $e) {
            Response::error("Error: " . $e->getMessage());
        }
    }
}

if (isset($_GET['action'])) {
    $controller = new FlightController();
    $action = $_GET['action'];
    $data = $_POST;

    switch ($action) {
        case 'save':
            $controller->createFlight($data);
            break;
        case 'getById':
            $id = $_GET['id'];
            $controller->getFlightById($id);
            break;
        case 'getAll':
            $controller->getFlights();
            break;

        case 'delete':
            $id = $_POST['id'];
            $controller->deleteFlight($id);
            break;
        case 'update':
            $id = $_POST['id'];
            $controller->updateFlight($id, $data);
            break;
    }
}
?>