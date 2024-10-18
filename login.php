<?php include 'partials/header.php';

if (isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}
?>
    <div class="login-page">
        <div class="container mt-5 d-flex justify-content-center align-items-center">
            <div class="form-container">
<!--                <div class="image-holder"></div>-->
                <form id="loginForm">
                    <h2 class="text-center"><strong>Login</strong></h2>
                    <div id="error" class="alert alert-danger" style="display: none;"></div>
                    <div class="form-group mt-2">
                        <input class="form-control" type="email" name="email" placeholder="Email" required>
                    </div>
                    <div class="form-group mt-2">
                        <input class="form-control" type="password" name="password" placeholder="Password" required>
                    </div>
                    <div class="form-group mt-3">
                        <div class="d-flex justify-content-between">
                            <div>
                                <a href="register.php" class="text-info">Dont have an account?</a>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mt-3">
                        <button type="submit" class="btn btn-primary w-100 mt-3">Login</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $('#loginForm').on('submit', function (e) {
                e.preventDefault();
                $.ajax({
                    url: 'src/controllers/UserController.php?action=login',
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function (res) {
                        if (res.success) {
                            window.location.href = 'index.php';
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