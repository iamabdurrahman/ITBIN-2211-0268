<?php
include 'partials/header.php';


if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

if ($_SESSION['user']['role'] === 'admin') {
    header('Location: index.php');
    exit;
}

$currentUserId = $_SESSION['user']['id'];
$userName = htmlspecialchars($_SESSION['user']['name']);
?>

    <div class="container mt-5">
        <div class="alert alert-primary" role="alert">
            Currently logged in as a <b><?php echo $userName; ?></b>. <br/>You can view your profile details and update your password here.
        </div>

        <div class="card mt-4 w-100">
            <div class="card-header">
                <b>
                Your Profile
                </b>
            </div>
            <div class="card-body">
                <h5 class="card-title">Profile Details</h5>
                <table class="table">
                    <tr>
                        <th>Name:</th>
                        <td><?php echo htmlspecialchars($_SESSION['user']['name']); ?></td>
                    </tr>
                    <tr>
                        <th>Email:</th>
                        <td><?php echo htmlspecialchars($_SESSION['user']['email']); ?></td>
                    </tr>
                    <tr>
                        <th>Balance:</th>
                        <td id="balance"></td>
                    </tr>
                    <tr>
                        <th>Role:</th>
                        <td><?php echo htmlspecialchars($_SESSION['user']['role']); ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <button class="btn btn-primary mt-4" id="showPasswordForm">Change Password</button>

        <div class="card mt-4 w-100" id="passwordCard" style="display: none;">
            <div class="card-header">
                <b>Change Password</b>
            </div>
            <div class="card-body">
                <form id="passwordForm">
                    <div id="error" class="alert alert-danger" style="display: none;"></div>
                    <div id="success" class="alert alert-success" style="display: none;"></div>
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($_SESSION['user']['id']); ?>">
                    <div class="form-group">
                        <label for="old_password">Current Password</label>
                        <input type="password" class="form-control" id="old_password" name="old_password" required>
                    </div>
                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                               required>
                    </div>
                    <button type="submit" class="btn btn-success mt-3">Update Password</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Add the necessary script to handle showing/hiding the password form -->
    <script>
        $(document).ready(function () {

            $.ajax({
                url: 'src/controllers/UserController.php?action=getBalance',
                method: 'GET',
                success: function (response) {
                    if (response.success) {
                        $('#balance').text(response.data.balance);
                    } else {
                        $('#balance').text('Error fetching balance');
                    }
                },
                error: function () {
                    $('#balance').text('Error fetching balance');
                }
            });

            $('#showPasswordForm').on('click', function () {
                $('#passwordCard').toggle();
            });

            $('#passwordForm').on('submit', function (e) {
                e.preventDefault();

                if ($('#new_password').val() !== $('#confirm_password').val()) {
                    $('#error').text('New password and confirm password do not match.').show();
                    $('#success').hide();
                    return;
                }

                $.ajax({
                    url: 'src/controllers/UserController.php?action=updatePassword',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function (response) {
                        if (response.success) {
                            $('#success').text(response.message).show();
                            $('#error').hide();
                            $('#passwordForm')[0].reset();
                            $('#passwordCard').hide();
                        } else {
                            $('#error').text(response.message).show();
                            $('#success').hide();
                        }
                    },
                    error: function () {
                        $('#error').text('Failed to update password. Please try again.').show();
                        $('#success').hide();
                    }
                });
            });
        });
    </script>

<?php include 'partials/footer.php'; ?>