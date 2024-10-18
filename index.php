<?php
include 'partials/header.php';

?>


<?php if (isset($_SESSION['user'])): ?>
    <header class="d-flex flex-column flex-md-row align-items-center p-3 mb-3">
        <article class="text-white mb-3 mb-md-0 me-3 col-md-4 col-lg-4">
            <h1 class="title font-weight-bold">Welcome to <br>Flight Booking System</h1>
            <p>Book your flight now and explore the world with ease!</p>
        </article>

        <?php if ($_SESSION['user']['role'] === 'admin'): ?>
            <section class="d-flex flex-column flex-md-row gap-3 col-md-8 col-lg-8 p-3">
                <aside class="p-3 rounded bg-manage-flights">
                    <a href="admin/manage_flights.php" class="full-link">
                        <h2 class="sub_title font-weight-bold">Manage Flights</h2>
                        <p>Create, edit, update flights and create flight schedules</p>
                        <i class="fa fa-plane link-icon"></i>
                    </a>
                </aside>
                <aside class="p-3 rounded bg-manage-schedules">
                    <a href="admin/manage_schedules.php" class="full-link">
                        <h2 class="sub_title font-weight-bold">Manage Flight Schedules</h2>
                        <p>Create, edit, delete flight schedules</p>
                        <i class="fa fa-calendar link-icon"></i>
                    </a>
                </aside>
                <aside class="p-3 rounded bg-manage-users">
                    <a href="admin/manage_users.php" class="full-link">
                        <h2 class="sub_title font-weight-bold">Manage Users</h2>
                        <p>Update users</p>
                        <i class="fa fa-users link-icon"></i>
                    </a>
                </aside>
            </section>
        <?php else: ?>
            <section class="d-flex flex-column p-3 ms-auto">
                <div class="card p-3">
                    <div class="d-flex align-items-center">
                        <div class="image p-2">
                            <img src="https://friconix.com/png/fi-cnsuxx-user-circle.png" class="rounded" width="100">
                        </div>
                        <div class="ml-3 w-100">
                            <h4 class="mb-0 mt-0 text-dark">Hi 👋, <?php echo $_SESSION['user']['name']; ?></h4>
                            <span class="text-black-50"><?php echo $_SESSION['user']['email']; ?></span>
                            <div class="p-2 mt-2 bg-primary d-flex justify-content-between rounded text-white stats">
                                <div class="d-flex flex-column">
                                    <span class="articles">Account balance</span>
                                    <span class="number1" id="balance"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        <?php endif; ?>
    </header>
<?php else: ?>
    <div class="text-center header-alt p-5 mx-auto">
        <h1 class="title font-weight-bold">Welcome to <br>Flight Booking System</h1>
        <p>Book your flight now and explore the world with ease!</p>
    </div>
<?php endif; ?>
    <!-- for card end-->

    <div class="container mt-5 d-flex justify-content-center align-items-center">
        <?php if (isset($_SESSION['user'])): ?>

            <?php if ($_SESSION['user']['role'] === 'admin'): ?>

                <div class="admin-section mt-4">
                    <div class="card shadow p-4" style="width: 100%; border-radius: 10px;">
                        <h4 class="text-center available-schedules" style="font-weight: 400">
                            <i class="fa fa-file"></i> All Bookings
                        </h4>
                        <div id="allBookings" class="table-responsive"></div>
                    </div>
                </div>

            <?php else: ?>
                <div class="user-section mt-4">
                    <div class="card shadow p-3" style="width: 100%; border-radius: 10px;">
                        <h4 class="text-center available-schedules" style="font-weight: 400">
                            <i class="fa fa-plane"></i> Book a flight
                        </h4>
                        <div id="flightSchedules" class="container table-responsive py-2"></div>
                    </div>
                    <div class="card shadow p-3 mt-4" style="width: 100%; border-radius: 10px;">
                        <h4 class="text-center available-schedules" style="font-weight: 400">
                            <i class="fa fa-file"></i> Your Bookings
                        </h4>
                        <div id="userBookings" class="container table-responsive py-2"></div>
                    </div>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <div class="card shadow p-4" style="width: 100%; border-radius: 10px;">
                <h4 class="text-center available-schedules" style="font-weight: 400">
                    <i class="fa fa-plane"></i> Available Flight Schedules
                </h4>
                <div id="flightSchedules" class="container table-responsive py-3"></div>
            </div>
        <?php endif; ?>
        <div class="modal fade" id="bookModal" tabindex="-1" aria-labelledby="bookModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="bookModalLabel">Book Seats</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="seatCountForm">
                            <div class="mb-3">
                                <label for="seatCount" class="form-label">Seat Count</label>
                                <input type="number" class="form-control" id="seatCount" name="seatCount" min="1"
                                       required>
                                <div class="invalid-feedback" id="seatCountError">Please enter a valid seat count.
                                </div>
                            </div>
                            <input type="hidden" id="flightId">
                            <input type="hidden" id="scheduleId">
                            <input type="hidden" id="flightNumber">
                            <input type="hidden" id="destination">
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" onclick="submitSeatCount()">Book Now</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            <?php if (isset($_SESSION['user'])): ?>
            <?php if ($_SESSION['user']['role'] === 'admin'): ?>

            $.ajax({
                url: 'src/controllers/BookingController.php?action=getAllBookings',
                type: 'GET',
                success: function (res) {
                    if (res.success) {
                        let bookingsHtml = '<table class="table table-bordered table-hover mt-3">' +
                            '<thead class="thead-dark">' +
                            '<tr>' +
                            '<th scope="col">Booking ID</th>' +
                            '<th scope="col">User</th>' +
                            '<th scope="col">Flight</th>' +
                            '<th scope="col">Destination</th>' +
                            '<th scope="col">Schedule</th>' +
                            '<th scope="col">Status</th>' +
                            '<th scope="col">Action</th>' +
                            '</tr></thead><tbody>';
                        res.data.forEach(booking => {
                            bookingsHtml += `<tr>
                            <th scope="row">${booking.id}</th>
                            <td>${booking.email}</td>
                            <td>${booking.flight_number}</td>
                            <td>${booking.destination}</td>
                            <td>${formatDateTime(booking.departure_time)}</td>
                            <td>
                                <span style="font-weight: bold" class="p-2 ${booking.status === 'confirmed' ? 'text-success' : booking.status === 'pending' ? 'text-warning' : 'text-danger'}">
                                    ${booking.status?.toUpperCase()}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-danger" onclick="cancelBooking(${booking.id})" ${booking.status === 'canceled' ? 'disabled' : ''}>
                                    <i class="fa fa-times"></i> Cancel Booking
                                </button>
                            </td>
                            </tr>`;

                        });
                        bookingsHtml += '</tbody></table>';
                        $('#allBookings').html(bookingsHtml);
                    } else {
                        $('#allBookings').html('<p>No bookings found.</p>');
                    }
                }
            });

            <?php else: ?>

            $.ajax({
                url: 'src/controllers/UserController.php?action=getBalance',
                method: 'GET',
                success: function (response) {
                    if (response.success) {
                        let bal = `Rs. ${response.data.balance}`;
                        $('#balance').text(bal);
                    } else {
                        $('#balance').text('Error fetching balance');
                    }
                },
                error: function () {
                    $('#balance').text('Error fetching balance');
                }
            });

            $.ajax({
                url: 'src/controllers/ScheduleController.php?action=getSchedules',
                type: 'GET',
                success: function (res) {
                    if (res.success) {
                        let schedulesHtml = '<table class="table table-bordered table-hover mt-3">' +
                            '<thead class="thead-dark">' +
                            '<tr>' +
                            '<th scope="col">Flight Number</th>' +
                            '<th scope="col">Destination</th>' +
                            '<th scope="col">Departure Time</th>' +
                            '<th scope="col">Available Seats</th>' +
                            '<th scope="col">Price</th>' +
                            '<th scope="col">Action</th>' +
                            '</tr>' +
                            '</thead>' +
                            '<tbody>';
                        res.data.forEach(schedule => {
                            schedulesHtml += `<tr>
                                        <th scope="row">${schedule.flight_number}</th>
                                        <td>${schedule.destination}</td>
                                        <td>${formatDateTime(schedule.departure_time)}</td>
                                        <td>${schedule.available_seats}/${schedule.total_seats}</td>
                                        <td>${schedule.price}</td>
                                          <td>
                                            <button type="button" class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#bookModal"
                                                data-flight-number="${schedule.flight_number}"
                                                data-destination="${schedule.destination}"
                                                data-departure-time="${schedule.departure_time}"
                                                data-available-seats="${schedule.available_seats}"
                                                data-schedule-id="${schedule.id}"
                                                data-flight-id="${schedule.flight_id}"
                                                data-price="${schedule.price}">
                                                <i class="fa fa-ticket me-2"></i> Book
                                            </button>
                                         </td>
                                    </tr>`;
                        });
                        schedulesHtml += '</tbody></table>';
                        $('#flightSchedules').html(schedulesHtml);
                    } else {
                        $('#flightSchedules').html('<p>No flight schedules available at the moment.</p>');
                    }
                }
            });

            $.ajax({
                url: `src/controllers/BookingController.php?action=getUserBookings&userId=<?php echo $_SESSION['user']['id']; ?>`,
                type: 'GET',
                success: function (res) {
                    if (res.success) {
                        let bookingsHtml = '<table class="table table-bordered table-hover mt-3">' +
                            '<thead class="thead-dark"><tr>' +
                            '<th scope="col">Booking ID</th>' +
                            '<th scope="col">Flight Number</th>' +
                            '<th scope="col"> Destination</th>' +
                            '<th scope="col">Schedule</th>' +
                            '<th scope="col">Seat Count</th>' +
                            '<th scope="col">Status</th>' +
                            '<th scope="col">Action</th></tr></thead><tbody>';
                        res.data.forEach(booking => {
                            bookingsHtml += `<tr>
                            <th scope="row">${booking.id}</th>
                            <td>${booking.flight_number}</td>
                            <td>${booking.destination}</td>
                            <td>${formatDateTime(booking.departure_time)}</td>
                            <td>${booking.seats}</td>
                           <td>
                                <span style="font-weight: bold" class="p-2 ${booking.status === 'confirmed' ? 'text-success' : booking.status === 'pending' ? 'text-warning' : 'text-danger'}">
                                    ${booking.status?.toUpperCase()}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-danger" onclick="cancelBooking(${booking.id})" ${booking.status === 'canceled' ? 'disabled' : ''}>
                                    <i class="fa fa-times"></i> Cancel Booking
                                </button>
                            </td>
                            </tr>`;
                        });
                        bookingsHtml += '</tbody></table>';
                        $('#userBookings').html(bookingsHtml);
                    } else {
                        $('#userBookings').html('<p>You have no bookings yet.</p>');
                    }
                }
            });

            <?php endif; ?>
            <?php else: ?>

            $.ajax({
                url: 'src/controllers/ScheduleController.php?action=getSchedules',
                type: 'GET',
                success: function (res) {
                    if (res.success) {
                        let schedulesHtml = '<table class="table table-bordered table-hover mt-3">' +
                            '<thead class="thead-dark"><tr>' +
                            '<th scope="col">Flight Number</th>' +
                            '<th scope="col">Destination</th>' +
                            '<th scope="col">Departure Time</th>' +
                            '<th scope="col">Available Seats</th>' +
                            '<th scope="col">Price</th>' +
                            '</tr></thead><tbody>';
                        res.data.forEach(schedule => {
                            schedulesHtml += `<tr>
                                    <th scope="row">${schedule.flight_number}</th>
                                    <td>${schedule.destination}</td>
                                    <td>${formatDateTime(schedule.departure_time)}</td>
                                    <td>${schedule.available_seats}/${schedule.total_seats}</td>
                                    <td>${schedule.price}</td>
                                </tr>`;
                        });
                        schedulesHtml += '</tbody></table>';
                        $('#flightSchedules').html(schedulesHtml);
                    } else {
                        $('#flightSchedules').html('<p>No flight schedules available at the moment.</p>');
                    }
                }
            });

            <?php endif; ?>
        });

        $('#bookModal').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget);
            const availableSeats = button.data('available-seats');
            const flight_id = button.data('flight-id');
            const schedule_id = button.data('schedule-id')

            const modal = $(this);
            modal.find('#seatCount').attr('max', availableSeats);
            modal.find('#seatCount').val(1);
            modal.find('#seatCountError').hide();
            modal.find('#flightId').val(flight_id);
            modal.find('#scheduleId').val(schedule_id);

        });

        function submitSeatCount() {
            const seatCount = document.getElementById('seatCount').value;
            const maxSeats = document.getElementById('seatCount').max;
            const flightId = document.getElementById('flightId').value;
            const scheduleId = document.getElementById('scheduleId').value;
            const userId = <?php echo isset($_SESSION['user']) ? $_SESSION['user']['id'] : 'null'; ?>;

            if (seatCount < 1 || seatCount > maxSeats) {
                document.getElementById('seatCountError').style.display = 'block';
            } else if (userId !== 'null') {
                $.ajax({
                    url: 'src/controllers/BookingController.php?action=createBooking',
                    type: 'POST',
                    data: {
                        user_id: userId,
                        flight_id: flightId,
                        schedule_id: scheduleId,
                        seats: seatCount,
                        status: 'confirmed'
                    },
                    success: function (response) {
                        if (response.success) {
                            alert('Booking created successfully!');
                        } else {
                            alert('Failed to create booking: ' + response.message);
                        }
                        $('#bookModal').modal('hide');
                        location.reload();
                    },
                    error: function (xhr, status, error) {
                        alert('An error occurred: ' + error);
                        $('#bookModal').modal('hide');
                    }
                });
            } else {
                alert('User session is not set. Please log in to book a flight.');
            }
        }

        function cancelBooking(bookingId) {
            if (confirm('Are you sure you want to cancel this booking?')) {
                $.ajax({
                    url: 'src/controllers/BookingController.php?action=cancelBooking',
                    type: 'POST',
                    data: {
                        booking_id: bookingId
                    },
                    success: function (response) {
                        if (response.success) {
                            alert('Booking canceled successfully!');
                            location.reload();
                        } else {
                            alert('Failed to cancel booking: ' + response.message);
                        }
                    },
                    error: function (xhr, status, error) {
                        alert('An error occurred: ' + error);
                    }
                });
            }
        }
    </script>

<?php include 'partials/footer.php'; ?>