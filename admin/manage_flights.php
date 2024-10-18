<?php
include __DIR__ . '/../partials/header.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}
?>

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4 heading p-3 rounded-3">
            <h4 class="text-center available-schedules text-light" style="font-weight: 400">
                <i class="fa fa-plane"></i> Manage Flights
            </h4>
        </div>
        <div class="create-section mt-4">
            <div class="card shadow p-3" style="width: 100%; border-radius: 10px;">
                <button id="showFlightForm" class="btn btn-primary m-2" style="width: fit-content"><i class="fa fa-plus me-2"></i>Create New Flight</button>
                <form id="flightForm" class="mb-4" style="display: none;">
                    <h5>Create Flight</h5>
                    <div class="row">
                        <input type="hidden" name="flight_id" id="flight_id">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="flight_number">Flight Number</label>
                                <input type="text" class="form-control" name="flight_number" id="flight_number"
                                       required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="price">A Seat Price</label>
                                <input type="number" class="form-control" name="price" id="price" step="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="seats">Seats</label>
                                <input type="number" class="form-control" name="seats" id="seats" required>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success mt-3"><i class="fa fa-save me-2"></i>Save Flight</button>
                    <button type="button" id="cancelFlightForm" class="btn btn-secondary mt-3">Cancel</button>
                </form>

                <div id="error" class="alert alert-danger" style="display: none;"></div>
                <div id="success" class="alert alert-success" style="display: none;"></div>
            </div>
        </div>
        <div class="list-section mt-4">
            <div class="card shadow p-3" style="width: 100%; border-radius: 10px;">
                <h4 class="mt-4">All Flights</h4>
                <div id="noFlightsMessage" class="alert alert-info" style="display: none;">No flights available. Click
                    "Create
                    New Flight" to add one.
                </div>
                <div class="container table-responsive py-2">
                    <table class="table table-bordered table-hover mt-3" style="display: none;">
                        <thead class="thead-dark">
                        <tr>
                            <th scope="col">Flight Number</th>
                            <th scope="col">A Seat Price</th>
                            <th scope="col">Seats</th>
                            <th scope="col">Actions</th>
                        </tr>
                        </thead>
                        <tbody id="flightTableBody"></tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="editFlightModal" tabindex="-1" aria-labelledby="editFlightModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editFlightModalLabel">Edit Flight</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editFlightForm">
                    <div class="modal-body">
                        <input type="hidden" id="edit_flight_id">
                        <div class="form-group mb-3">
                            <label for="edit_flight_number">Flight Number</label>
                            <input type="text" class="form-control" id="edit_flight_number" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="edit_price">A seat Price</label>
                            <input type="number" class="form-control" id="edit_price" step="0.01" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="edit_seats">Seats</label>
                            <input type="number" class="form-control" id="edit_seats" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $('#showFlightForm').on('click', function () {
                $('#flightForm').slideDown();
                $('#showFlightForm').hide();
            });

            $('#cancelFlightForm').on('click', function () {
                $('#flightForm').slideUp();
                $('#showFlightForm').show();
                $('#flightForm')[0].reset();
            });

            function loadFlights() {
                $.ajax({
                    url: '../src/controllers/FlightController.php?action=getAll',
                    method: 'GET',
                    success: function (response) {
                        if (response.success && response.data.length > 0) {
                            let flightsHtml = '';
                            response.data.forEach(function (flight) {
                                flightsHtml += `<tr data-id="${flight.id}">
                            <th scope="row">${flight.flight_number}</th>
                            <td>${parseFloat(flight.price).toFixed(2)}</td>
                            <td>${flight.seats}</td>
                            <td>
                                <button class="btn btn-sm btn-warning edit-flight" data-id="${flight.id}" data-bs-toggle="modal" data-bs-target="#editFlightModal"><i class="fa fa-pencil me-2"></i>Edit</button>
                                <button class="btn btn-sm btn-danger delete-flight" data-id="${flight.id}"><i class="fa fa-trash-o me-2"></i>Delete</button>
                                <a href="manage_schedules.php?flight_id=${flight.id}" class="btn btn-sm btn-danger"><i class="fa fa-clock-o me-2"></i>Create Schedule</a>
                            </td>
                        </tr>`;
                            });
                            $('#flightTableBody').html(flightsHtml);
                            $('table').show();
                            $('#noFlightsMessage').hide();
                        } else {
                            $('table').hide();
                            $('#noFlightsMessage').show();
                        }
                    },
                    error: function () {
                        $('#error').text('Failed to load flights. Please try again.').show();
                    }
                });
            }

            loadFlights();

            $('#flightForm').on('submit', function (e) {
                e.preventDefault();
                $.ajax({
                    url: '../src/controllers/FlightController.php?action=save',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function (response) {
                        if (response.success) {
                            $('#success').text(response.message).show();
                            $('#error').hide();
                            loadFlights();
                            $('#flightForm').slideUp();
                            $('#showFlightForm').show();
                            $('#flightForm')[0].reset();
                        } else {
                            $('#error').text(response.message).show();
                            $('#success').hide();
                        }
                    },
                    error: function () {
                        $('#error').text('An error occurred. Please try again.').show();
                        $('#success').hide();
                    }
                });
            });

            $(document).on('click', '.edit-flight', function () {
                const flightId = $(this).data('id');
                $.ajax({
                    url: '../src/controllers/FlightController.php?action=getById',
                    method: 'GET',
                    data: {id: flightId},
                    success: function (response) {
                        if (response.success) {
                            const flight = response.data;
                            $('#edit_flight_id').val(flight.id);
                            $('#edit_flight_number').val(flight.flight_number);
                            $('#edit_price').val(flight.price);
                            $('#edit_seats').val(flight.seats);
                        } else {
                            $('#error').text(response.message).show();
                        }
                    },
                    error: function () {
                        $('#error').text('Failed to load flight data.').show();
                    }
                });
            });

            $('#editFlightForm').on('submit', function (e) {
                e.preventDefault();
                const flightId = $('#edit_flight_id').val();
                const data = {
                    id: flightId,
                    flight_number: $('#edit_flight_number').val(),
                    price: $('#edit_price').val(),
                    seats: $('#edit_seats').val(),
                };
                $.ajax({
                    url: '../src/controllers/FlightController.php?action=update',
                    method: 'POST',
                    data: data,
                    success: function (response) {
                        if (response.success) {
                            $('#success').text(response.message).show();
                            $('#error').hide();
                            $('#editFlightModal').modal('hide');
                            loadFlights();
                        } else {
                            $('#error').text(response.message).show();
                        }
                    },
                    error: function () {
                        $('#error').text('Failed to update the flight.').show();
                    }
                });
            });

            $(document).on('click', '.delete-flight', function () {
                const flightId = $(this).data('id');
                if (confirm('Are you sure you want to delete this flight?')) {
                    $.ajax({
                        url: '../src/controllers/FlightController.php?action=delete',
                        method: 'POST',
                        data: {id: flightId},
                        success: function (response) {
                            if (response.success) {
                                $('#success').text(response.message).show();
                                loadFlights();
                            } else {
                                $('#error').text(response.message).show();
                            }
                        },
                        error: function () {
                            $('#error').text('Failed to delete the flight.').show();
                        }
                    });
                }
            });

        });
    </script>

<?php include __DIR__ . '/../partials/footer.php'; ?>