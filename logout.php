<?php

require_once 'src/utils/Session.php';
require_once 'src/utils/Auth.php';

Session::start();

Auth::logout();

header('Location: index.php');
exit;

?>