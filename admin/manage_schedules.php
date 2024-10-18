<?php
include __DIR__ . '/../partials/header.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$flightId = isset($_GET['flight_id']) ? $_GET['flight_id'] : null;
?>

    <div class="container mt-5">
        <?php if ($flightId): ?>
            <div class="d-flex justify-content-between align-items-center mb-4 heading p-3 rounded-3">
                <h4 class="text-center available-schedules text-light" style="font-weight: 400">
                    <i class="fa fa-clock-o"></i> Manage Schedules for Flight <?php echo htmlspecialchars($flightId); ?>
                </h4>
            </div>
        <?php else: ?>
            <div class="d-flex justify-content-between align-items-center mb-4 heading p-3 rounded-3">
                <h4 class="text-center available-schedules text-light" style="font-weight: 400">
                    <i class="fa fa-clock-o"></i> Manage Schedules
                </h4>
            </div>
        <?php endif; ?>

        <?php if ($flightId): ?>
            <div class="create-section mt-4 mb-3">
                <div class="card shadow p-3" style="width: 100%; border-radius: 10px;">
                    <button id="showScheduleForm" class="btn btn-primary me-2" style="width: fit-content"><i class="fa fa-plus me-2"></i> Create New Schedule</button>
                    <form id="scheduleForm" class="mb-4" style="display: none;">
                        <h4>Create Schedule</h4>
                        <div class="row">
                            <input type="hidden" name="schedule_id" id="schedule_id">
                            <input type="hidden" name="flight_id" id="flight_id"
                                   value="<?php echo htmlspecialchars($flightId); ?>">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="destination">Destination</label>
                                    <input type="text" class="form-control" name="destination" id="destination"
                                           required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="departure_time">Departure Time</label>
                                    <input type="datetime-local" class="form-control" name="departure_time"
                                           id="departure_time"
                                           required>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success mt-3">Save Schedule</button>
                        <button type="button" id="cancelScheduleForm" class="btn btn-secondary mt-3">Cancel</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <div id="error" class="alert alert-danger" style="display: none;"></div>
        <div id="success" class="alert alert-success" style="display: none;"></div>
        <div id="noSchedulesMessage" class="alert alert-info" style="display: none;">No schedules available for this
            flight.
        </div>
        <div class="create-section mt-4">
            <div class="card shadow p-3" style="width: 100%; border-radius: 10px;">
                <div class="container table-responsive py-2">
                    <h5 class="mt-4">All Schedules</h5>
                    <table class="table table-bordered table-hover mt-3" style="display: none;">
                        <thead class="thead-dark">
                        <tr>
                            <th scope="col">Flight Number</th>
                            <th scope="col">Destination</th>
                            <th scope="col">Departure Time</th>
                            <th scope="col">Actions</th>
                        </tr>
                        </thead>
                        <tbody id="scheduleTableBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editScheduleModal" tabindex="-1" aria-labelledby="editScheduleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editScheduleModalLabel">Edit Schedule</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editScheduleForm">
                    <div class="modal-body">
                        <input type="hidden" id="edit_schedule_id">
                        <input type="hidden" id="edit_flight_id">
                        <div class="form-group mb-3">
                            <label for="edit_destination">Destination</label>
                            <input type="text" class="form-control" id="edit_destination" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="edit_departure_time">Departure Time</label>
                            <input type="datetime-local" class="form-control" id="edit_departure_time" required>
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
            const flightId = "<?php echo $flightId ? $flightId : ''; ?>";

            $('#showScheduleForm').on('click', function () {
                $('#scheduleForm').slideDown();
                $('#showScheduleForm').hide();
            });

            $('#cancelScheduleForm').on('click', function () {
                $('#scheduleForm').slideUp();
                $('#showScheduleForm').show();
                $('#scheduleForm')[0].reset();
            });

            function loadSchedules() {
                let url = '../src/controllers/ScheduleController.php?action=getSchedules';
                if (flightId) {
                    url += `&flight_id=${flightId}`;
                }
                $.ajax({
                    url: url,
                    method: 'GET',
                    success: function (response) {
                        if (response.success && response.data.length > 0) {
                            let schedulesHtml = '';
                            response.data.forEach(function (schedule) {
                                schedulesHtml += `<tr data-id="${schedule.id}">
                                <th scope="row">${schedule.flight_number}</th>
                                <td>${schedule.destination}</td>
                                <td>${formatDateTime(new Date(schedule.departure_time).toLocaleString())}</td>
                                <td>
                                    <button class="btn btn-sm btn-warning edit-schedule" data-id="${schedule.id}" data-bs-toggle="modal" data-bs-target="#editScheduleModal"><i class="fa fa-pencil me-2"></i>Edit</button>
                                    <button class="btn btn-sm btn-danger delete-schedule" data-id="${schedule.id}"><i class="fa fa-trash-o me-2"></i>Delete</button>
                                </td>
                            </tr>`;
                            });
                            $('#scheduleTableBody').html(schedulesHtml);
                            $('table').show();
                            $('#noSchedulesMessage').hide();
                        } else {
                            $('table').hide();
                            $('#noSchedulesMessage').show();
                        }
                    },
                    error: function () {
                        $('#error').text('Failed to load schedules. Please try again.').show();
                    }
                });
            }

            loadSchedules();

            $('#scheduleForm').on('submit', function (e) {
                e.preventDefault();
                $.ajax({
                    url: '../src/controllers/ScheduleController.php?action=save',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function (response) {
                        if (response.success) {
                            $('#success').text(response.message).show();
                            $('#error').hide();
                            loadSchedules();
                            $('#scheduleForm').slideUp();
                            $('#showScheduleForm').show();
                            $('#scheduleForm')[0].reset();
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

            $(document).on('click', '.edit-schedule', function () {
                const scheduleId = $(this).data('id');
                $.ajax({
                    url: '../src/controllers/ScheduleController.php?action=getById',
                    method: 'GET',
                    data: {id: scheduleId},
                    success: function (response) {
                        if (response.success) {
                            const schedule = response.data;
                            console.log(schedule);
                            $('#edit_schedule_id').val(schedule.id);
                            $('#edit_flight_id').val(schedule.flight_id);
                            $('#edit_destination').val(schedule.destination);
                            $('#edit_departure_time').val(schedule.departure_time.replace(' ', 'T'));
                        } else {
                            $('#error').text(response.message).show();
                        }
                    },
                    error: function () {
                        $('#error').text('Failed to load schedule data.').show();
                    }
                });
            });

            $('#editScheduleForm').on('submit', function (e) {
                e.preventDefault();
                const scheduleId = $('#edit_schedule_id').val();
                const data = {
                    id: scheduleId,
                    flight_id: $('#edit_flight_id').val(),
                    destination: $('#edit_destination').val(),
                    departure_time: $('#edit_departure_time').val()
                };
                $.ajax({
                    url: '../src/controllers/ScheduleController.php?action=update',
                    method: 'POST',
                    data: data,
                    success: function (response) {
                        if (response.success) {
                            $('#success').text(response.message).show();
                            $('#error').hide();
                            loadSchedules();
                            $('#editScheduleModal').modal('hide');
                        } else {
                            $('#error').text(response.message).show();
                        }
                    },
                    error: function () {
                        $('#error').text('Failed to update the schedule.').show();
                    }
                });
            });

            $(document).on('click', '.delete-schedule', function () {
                const scheduleId = $(this).data('id');
                if (confirm('Are you sure you want to delete this schedule?')) {
                    $.ajax({
                        url: '../src/controllers/ScheduleController.php?action=delete',
                        method: 'POST',
                        data: {id: scheduleId},
                        success: function (response) {
                            if (response.success) {
                                $('#success').text(response.message).show();
                                loadSchedules();
                            } else {
                                $('#error').text(response.message).show();
                            }
                        },
                        error: function () {
                            $('#error').text('Failed to delete the schedule.').show();
                        }
                    });
                }
            });
        });
    </script>

<?php include __DIR__ . '/../partials/footer.php'; ?>