<?php
require_once __DIR__ . '/../models/Booking.php';
require_once __DIR__ . '/../utils/Response.php';
require_once __DIR__ . '/../utils/Session.php';
Session::start();

class BookingController
{
    public function createBooking($data)
    {
        try {
            $booking = new Booking();
            $booking->setUserId($data['user_id']);
            $booking->setFlightId($data['flight_id']);
            $booking->setScheduleId($data['schedule_id']);
            $booking->setSeats($data['seats']);
            $booking->setStatus($data['status']);
            $result = $booking->create();

            if ($result) {
                Response::success("Booking created successfully!");
            } else {
                Response::error("Failed to create booking.");
            }
        } catch (Exception $e) {
            Response::error("Error: " . $e->getMessage());
        }
    }

    public function getBookingsById($userId)
    {
        try {
            if (!$userId) {
                Response::error("User ID is required.");
                return;
            }
            $booking = new Booking();
            $bookings = $booking->getByUserId($userId);
            Response::success("Bookings retrieved successfully.", $bookings);
        } catch (Exception $e) {
            Response::error("Error: " . $e->getMessage());
        }
    }

    public function getAllBookings()
    {
        try {
            $booking = new Booking();
            $bookings = $booking->getAll();
            Response::success("Bookings retrieved successfully.", $bookings);
        } catch (Exception $e) {
            Response::error("Error: " . $e->getMessage());
        }
    }

    public function cancelBooking($id,$userId)
    {
        try {
            if (!$userId) {
                Response::error("User ID is required.");
                return;
            }
            $booking = new Booking();
            $booking->setId($id);

            $booking->setUserId($userId);
            $result = $booking->cancel();

            if ($result) {
                Response::success("Booking canceled successfully!");
            } else {
                Response::error("Failed to cancel booking.");
            }
        } catch (Exception $e) {
            Response::error("Error: " . $e->getMessage());
        }
    }

    public function update($data)
    {
        try {
            $booking = new Booking();
            $booking->setId($data['id']);
            $booking->setUserId($data['user_id']);
            $booking->setFlightId($data['flight_id']);
            $booking->setScheduleId($data['schedule_id']);
            $booking->setSeats($data['seats']);
            $booking->setStatus($data['status']);
            $result = $booking->updateById();

            if ($result) {
                Response::success("Booking updated successfully!");
            } else {
                Response::error("Failed to update booking.");
            }
        } catch (Exception $e) {
            Response::error("Error: " . $e->getMessage());
        }
    }
}

if (isset($_GET['action'])) {
    $controller = new BookingController();
    $action = $_GET['action'];
    $data = $_POST;
    switch ($action) {
        case 'getAllBookings':
            $controller->getAllBookings();
            break;
        case 'getUserBookings':
            $userId = $_GET['userId'];
            $controller->getBookingsById($userId);
            break;
        case 'createBooking':
            $controller->createBooking($data);
            break;

        case 'cancelBooking':
            $id = $_POST['booking_id'];
            $userId = $_SESSION['user']['id'];
            $controller->cancelBooking($id, $userId);
            break;
    }
}
?>