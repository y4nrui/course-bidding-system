<?php
require_once 'common.php';

if (!isset($_SESSION['username'])) {
    $_SESSION['errors'] = ["You have not logged in."];
    header("Location: login.php");
    exit;
} elseif ($_SESSION['username'] != 'admin') { // to prevent students from accessing admin home
    $_SESSION['errors'] = ["The page you wish to access is only accessible by Administrators."];
    header("Location: student-home.php");
    exit;
}
?>