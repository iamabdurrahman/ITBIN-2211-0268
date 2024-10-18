<?php
include __DIR__ . '/../partials/header.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$isAdmin = $_SESSION['user']['role'] === 'admin';
?>

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4 heading p-3 rounded-3">
            <h4 class="text-center available-schedules text-light" style="font-weight: 400">
                <i class="fa fa-users"></i> Manage Users
            </h4>
        </div>

        <div id="error" class="alert alert-danger" style="display: none;"></div>
        <div id="success" class="alert alert-success" style="display: none;"></div>

        <div class="create-section mt-4 mt-4">
            <div class="card shadow p-3" style="width: 100%; border-radius: 10px;">
                <h5 class="mt-4">All Users</h5>
                <div id="noUsersMessage" class="alert alert-info" style="display: none;">No users available.</div>
                <div class="container table-responsive py-2">
                    <table class="table table-bordered table-hover mt-3" style="display: none;">
                        <thead class="thead-dark">
                        <tr>
                            <th scope="col">Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Balance</th>
                            <th scope="col">Role</th>
                            <th scope="col">Actions</th>
                        </tr>
                        </thead>
                        <tbody id="userTableBody"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="userForm">
                            <input type="hidden" name="user_id" id="user_id">
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" name="name" id="name" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" name="email" id="email" readonly>
                            </div>
                            <div class="form-group">
                                <label for="role">Role</label>
                                <select class="form-control" name="role" id="role" required>
                                    <option value="user">User</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success" id="saveChanges">Save Changes</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            function loadUsers() {
                $.ajax({
                    url: '../src/controllers/UserController.php?action=getUsers',
                    method: 'GET',
                    success: function (response) {
                        if (response.success && response.data.length > 0) {
                            let usersHtml = '';
                            response.data.forEach(function (user) {
                                usersHtml += `<tr data-id="${user.id}">
                            <td>${user.name}</td>
                            <td>${user.email}</td>
                            <td>${user.balance}</td>
                            <td>${user.role}</td>
                            <td>
                                <button class="btn btn-sm btn-warning edit-user" data-id="${user.id}" data-bs-toggle="modal" data-bs-target="#editUserModal"><i class="fa fa-pencil me-2"></i>Edit</button>
                                <button class="btn btn-sm btn-danger delete-user" data-id="${user.id}"><i class="fa fa-trash-o me-2"></i>Delete</button>
                            </td>
                        </tr>`;
                            });
                            $('#userTableBody').html(usersHtml);
                            $('table').show();
                            $('#noUsersMessage').hide();
                        } else {
                            $('table').hide();
                            $('#noUsersMessage').show();
                        }
                    },
                    error: function () {
                        $('#error').text('Failed to load users. Please try again.').show();
                    }
                });
            }

            loadUsers();

            $(document).on('click', '.edit-user', function () {
                const userId = $(this).data('id');
                $.ajax({
                    url: '../src/controllers/UserController.php?action=getById',
                    method: 'GET',
                    data: {id: userId},
                    success: function (response) {
                        if (response.success) {
                            const user = response.data;
                            $('#name').val(user.name);
                            $('#email').val(user.email);
                            $('#role').val(user.role);
                            $('#user_id').val(user.id);
                        } else {
                            $('#error').text(response.message).show();
                        }
                    },
                    error: function () {
                        $('#error').text('Failed to load user data.').show();
                    }
                });
            });

            $('#saveChanges').on('click', function (e) {
                e.preventDefault();
                $.ajax({
                    url: '../src/controllers/UserController.php?action=updateUser',
                    method: 'POST',
                    data: $('#userForm').serialize(),
                    success: function (response) {
                        if (response.success) {
                            $('#success').text(response.message).show();
                            $('#error').hide();
                            $('#editUserModal').modal('hide');
                            loadUsers();
                        } else {
                            $('#error').text(response.message).show();
                            $('#success').hide();
                        }
                    },
                    error: function () {
                        $('#error').text('Failed to update user. Please try again.').show();
                        $('#success').hide();
                    }
                });
            });

            $(document).on('click', '.delete-user', function () {
                const userId = $(this).data('id');
                if (confirm('Are you sure you want to delete this user?')) {
                    $.ajax({
                        url: '../src/controllers/UserController.php?action=delete',
                        method: 'POST',
                        data: {id: userId},
                        success: function (response) {
                            if (response.success) {
                                $('#success').text(response.message).show();
                                loadUsers();
                            } else {
                                $('#error').text(response.message).show();
                            }
                        },
                        error: function () {
                            $('#error').text('Failed to delete the user.').show();
                        }
                    });
                }
            });
        });
    </script>

<?php include __DIR__ . '/../partials/footer.php'; ?>