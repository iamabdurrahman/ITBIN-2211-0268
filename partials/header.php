<?php
require_once __DIR__ . '/../src/utils/Session.php';
Session::start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flight Booking System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="/flight-booking-system/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"
            integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p"
            crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"
            integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF"
            crossorigin="anonymous"></script>
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="/flight-booking-system/index.php">
            <img src="/flight-booking-system/assets/images/plane.png" alt="Brand Logo" width="32" height="32" class="me-2 mw-2">
            Flight Booking System
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-md-auto gap-2">
                <?php if (Session::isLoggedIn()): ?>
                    <li class="nav-item rounded">
                        <a class="nav-link active" aria-current="page" href="/flight-booking-system/logout.php">
                            <i class="bi bi-box-arrow-right me-2 icon-yellow"></i>Logout</a>
                    </li>
                    <?php if ($_SESSION['user']['role'] !== 'admin'): ?>
                        <li class="nav-item rounded">
                            <a class="nav-link" href="/flight-booking-system/profile.php">
                                <i class="bi bi-person-fill me-2 icon-yellow"></i>Profile</a>
                        </li>
                    <?php endif; ?>
                <?php else: ?>
                    <li class="nav-item rounded">
                        <a class="nav-link" href="/flight-booking-system/login.php">
                            <i class="bi bi-box-arrow-in-right me-2 icon-yellow"></i>Login</a>
                    </li>
                    <li class="nav-item rounded">
                        <a class="nav-link" href="/flight-booking-system/register.php">
                            <i class="bi bi-person-plus-fill me-2 icon-yellow"></i>Register</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<div class="content pb-3">