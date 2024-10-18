<?php include 'partials/header.php';
require_once __DIR__ . '/src/utils/Session.php';
Session::start();
if (isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}
?>

    <div class="login-page">
        <div class="container mt-5 d-flex justify-content-center align-items-center" style="min-height: 100vh;">
            <div class="form-container">
<!--                <div class="image-holder"></div>-->
                <form id="registerForm">
                    <h2 class="text-center"><strong>Register Now!</strong></h2>
                    <div id="error" class="alert alert-danger" style="display: none;"></div>
                    <div id="success" class="alert alert-success" style="display: none;"></div>
                    <div class="form-group mt-2">
                        <input class="form-control" type="text" name="name" placeholder="Name" required>
                    </div>
                    <div class="form-group mt-2">
                        <input class="form-control" type="email" name="email" placeholder="Email" required>
                    </div>
                    <div class="form-group mt-2">
                        <input class="form-control" type="password" name="password" placeholder="Password" required>
                    </div>
                    <div class="form-group mt-3">
                        <div class="d-flex justify-content-between">
                            <div>
                                <a href="login.php" class="text-info">Already have an account?</a>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mt-3">
                        <button type="submit" class="btn btn-primary w-100 mt-3">Register</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $('#registerForm').on('submit', function (e) {
                e.preventDefault();
                $('#error').hide();
                $('#success').hide();

                $.ajax({
                    url: 'src/controllers/UserController.php?action=register',
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function (res) {
                        if (res.success) {
                            $('#success').text(res.message).show();
                            $('#registerForm')[0].reset();
                            window.location.href = 'login.php';
                        } else {
                            $('#error').text(res.message).show();
                        }
                    },
                    error: function (err) {
                        console.log('An error occurred. Please try again.', err);
                        $('#error').text('An error occurred. Please try again.').show();
                    }
                });
            });
        });
    </script>

<?php include 'partials/footer.php'; ?>