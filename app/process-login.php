<?php

require_once 'include/common.php';

if (!isset($_POST['submit'])) {
    header("Location: login.php");
    exit;
}

$errors = [
    isMissingOrEmpty('username'),
    isMissingOrEmpty('password')
];
$errors = array_filter($errors);

if (!isEmpty($errors)) {
    sort($errors);
    $_SESSION['errors'] = array_values($errors);
}

if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])) {
    header("Location: login.php");
    exit;
}

$studentDao = new StudentDAO();
$student = $studentDao->retrieveStudent($_POST['username']);

$adminDao = new AdminDAO();


if ($_POST['username'] != 'admin' && !isset($student)) {
    $_SESSION['errors'] = ['invalid username'];
    header("Location: login.php");
    exit;
} else { # when username is valid
    # if admin match admin password
    if ($_POST['username'] == 'admin' && password_verify($_POST['password'], $adminDao->getPassword())) {
        $_SESSION['username'] = 'admin';
        header("Location: admin-home.php");
        exit;
    } elseif (isset($student) && $_POST['password'] == $student->getPassword()) {
        # if not admin match student password
        # if is admin but password fail, need to avoid error with isset()
        $_SESSION['username'] = $_POST['username'];
        header("Location: student-home.php");
        exit;
    } else {
        $_SESSION['errors'] = ["invalid password"];
        header("Location: login.php");
        exit;
    }
}
